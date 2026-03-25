<?php

namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\TarifTagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    /**
     * Tampilkan daftar tagihan.
     */
    public function index(Request $request)
    {
        $query = Tagihan::with(['siswa', 'jenisTagihan', 'kelas', 'tahunAjaran']);

        // Filter status (default: belum lunas)
        $status = $request->input('status', 'belum lunas');
        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        // Filter tahun ajaran
        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        }

        // Filter jenis tagihan
        if ($request->filled('jenis_tagihan_id')) {
            $query->where('jenis_tagihan_id', $request->jenis_tagihan_id);
        }

        // Filter kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter search (NIS atau Nama siswa)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nis', 'LIKE', "%{$search}%")
                    ->orWhere('nama', 'LIKE', "%{$search}%");
            });
        }

        $data = $query->orderBy('kelas_id', 'asc')
            ->orderBy('jenis_tagihan_id', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();
        $jenisTagihan = JenisPembayaran::where('status', 'aktif')->orderBy('nama')->get();
        $kelas = Kelas::where('status', 'aktif')->orderBy('kelas')->get();

        return view('admin.pembayaran.tagihan.index', compact('data', 'tahunAjaran', 'jenisTagihan', 'kelas', 'status'));
    }

    /**
     * Tampilkan form tambah tagihan.
     */
    public function create()
    {
        $siswa = Siswa::where('status', 'aktif')->orderBy('nama')->get();
        $jenisTagihan = TarifTagihan::with('jenisTagihan')
            ->where('status', 'aktif')
            ->whereHas('tahunAjaran', function ($q) {
                $q->where('status', 'aktif');
            })
            ->get()
            ->pluck('jenisTagihan')
            ->unique('id');

        $kelas = Kelas::where('status', 'aktif')->orderBy('kelas')->get();
        $tahunAjaranAktif = TahunAjaran::where('status', 'aktif')->first();


        return view('admin.pembayaran.tagihan.create', compact('siswa', 'jenisTagihan', 'kelas', 'tahunAjaranAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mode'             => 'required|in:siswa,kelas',
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'jatuh_tempo'      => 'nullable|date',
        ]);

        // Ambil tahun ajaran aktif
        $tahunAjaran = TahunAjaran::where('status', 'aktif')->first();

        if (!$tahunAjaran) {
            return back()->withInput()->withErrors([
                'error' => 'Tidak ada tahun ajaran yang aktif. Silakan aktifkan tahun ajaran terlebih dahulu.'
            ]);
        }

        // Ambil jenis tagihan
        $jenisTagihan = JenisPembayaran::find($request->jenis_tagihan_id);

        // Validasi & set bulan (berlaku untuk mode siswa maupun kelas)
        $bulan = null;
        if ($jenisTagihan->tipe === 'bulanan') {
            $request->validate([
                'bulan' => 'required|integer|min:1|max:12'
            ]);
            $bulan = (int) $request->bulan;
        }

        // Ambil tarif
        $tarif = TarifTagihan::getTarif($request->jenis_tagihan_id, $tahunAjaran->id);

        if (!$tarif) {
            return back()->withInput()->withErrors([
                'jenis_tagihan_id' => "Tarif untuk jenis tagihan ini di tahun ajaran {$tahunAjaran->tahun} belum diset. Silakan set di menu Tarif Tagihan."
            ]);
        }

        $nominal = $tarif->nominal;

        // ✅ JATUH TEMPO:
        if ($jenisTagihan->tipe === 'bulanan' && $bulan) {

            [$tahunAwal, $tahunAkhir] = explode('/', $tahunAjaran->tahun);

            // Juli–Desember → tahun awal
            // Januari–Juni → tahun akhir
            $tahunFinal = ($bulan >= 7) ? $tahunAwal : $tahunAkhir;

            $jatuhTempo = \Carbon\Carbon::create(
                (int) $tahunFinal,
                (int) $bulan,
                10
            )->format('Y-m-d');
        } else {
            $jatuhTempo = $request->jatuh_tempo ?? now()->addDays(30)->format('Y-m-d');
        }


        DB::beginTransaction();
        try {

            // ================= MODE SISWA =================
            if ($request->mode === 'siswa') {

                $request->validate([
                    'siswa_nis' => 'required|exists:siswa,nis',
                ]);

                $siswa = Siswa::where('nis', $request->siswa_nis)->first();

                if (!$siswa->kelas_id) {
                    return back()->withInput()->withErrors([
                        'siswa_nis' => 'Siswa belum di-assign ke kelas.'
                    ]);
                }

                // Cek duplikat
                $duplicateQuery = Tagihan::where('siswa_nis', $request->siswa_nis)
                    ->where('jenis_tagihan_id', $request->jenis_tagihan_id)
                    ->where('tahun_ajaran_id', $tahunAjaran->id);

                if ($jenisTagihan->tipe === 'bulanan') {
                    $duplicateQuery->where('bulan', $bulan);
                    $namaBulan    = \Carbon\Carbon::create()->month($bulan)->translatedFormat('F');
                    $errorMessage = "Tagihan SPP bulan {$namaBulan} sudah ada untuk siswa ini.";
                } else {
                    $duplicateQuery->where('status', '!=', 'lunas');
                    $errorMessage = "Tagihan {$jenisTagihan->nama} yang belum lunas sudah ada.";
                }

                if ($duplicateQuery->exists()) {
                    return back()->withInput()->withErrors([
                        'siswa_nis' => $errorMessage
                    ]);
                }

                Tagihan::create([
                    'siswa_nis'        => $request->siswa_nis,
                    'kelas_id'         => $siswa->kelas_id,
                    'jenis_tagihan_id' => $request->jenis_tagihan_id,
                    'tahun_ajaran_id'  => $tahunAjaran->id,
                    'total_tagihan'    => $nominal,
                    'sudah_dibayar'    => 0,
                    'status'           => 'belum lunas',
                    'bulan'            => $bulan,
                    'jatuh_tempo'      => $jatuhTempo,
                ]);

                $namaBulanInfo  = $bulan
                    ? ' bulan ' . \Carbon\Carbon::create()->month($bulan)->translatedFormat('F')
                    : '';
                $successMessage = "Tagihan{$namaBulanInfo} berhasil ditambahkan dengan nominal {$tarif->nominal_format} untuk tahun ajaran {$tahunAjaran->tahun}.";
            }
            // ================= MODE KELAS =================
            else {

                $request->validate([
                    'kelas_id' => 'nullable|exists:kelas,id',
                ]);

                $query = Siswa::where('status', 'aktif')->whereNotNull('kelas_id');

                if ($request->kelas_id) {
                    $query->where('kelas_id', $request->kelas_id);
                }

                // Filter siswa yang belum punya tagihan (gunakan kolom bulan, bukan jatuh_tempo)
                $siswas = $query->whereDoesntHave('tagihan', function ($q) use ($request, $tahunAjaran, $jenisTagihan, $bulan) {
                    $q->where('jenis_tagihan_id', $request->jenis_tagihan_id)
                        ->where('tahun_ajaran_id', $tahunAjaran->id);

                    if ($jenisTagihan->tipe === 'bulanan') {
                        // ✅ Pakai kolom bulan
                        $q->where('bulan', $bulan);
                    } else {
                        $q->where('status', '!=', 'lunas');
                    }
                })->get();

                if ($siswas->isEmpty()) {
                    return back()->withInput()->withErrors([
                        'kelas_id' => 'Tidak ada siswa yang perlu dibuatkan tagihan untuk kombinasi ini.'
                    ]);
                }

                $tagihanData = [];
                foreach ($siswas as $siswa) {
                    $tagihanData[] = [
                        'siswa_nis'        => $siswa->nis,
                        'kelas_id'         => $siswa->kelas_id,
                        'jenis_tagihan_id' => $request->jenis_tagihan_id,
                        'tahun_ajaran_id'  => $tahunAjaran->id,
                        'total_tagihan'    => $nominal,
                        'sudah_dibayar'    => 0,
                        'status'           => 'belum lunas',
                        'bulan'            => $bulan,       // null untuk non-bulanan
                        'jatuh_tempo'      => $jatuhTempo,  // otomatis tgl 10 untuk bulanan
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }

                Tagihan::insert($tagihanData);

                $kelasInfo      = $request->kelas_id
                    ? 'kelas ' . Kelas::find($request->kelas_id)->kelas
                    : 'semua kelas';
                $periodeInfo    = $bulan
                    ? ' bulan ' . \Carbon\Carbon::create()->month($bulan)->translatedFormat('F')
                    : '';
                $successMessage = "Tagihan{$periodeInfo} berhasil ditambahkan untuk {$siswas->count()} siswa dari {$kelasInfo} dengan nominal {$tarif->nominal_format}.";
            }

            DB::commit();
            return redirect()->route('tagihan.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }


    /**
     * Tampilkan form edit tagihan.
     */
    public function edit(Tagihan $tagihan)
    {
        $siswa = Siswa::where('status', 'aktif')->whereNotNull('kelas_id')->orderBy('nama')->get();

        // Ambil jenis pembayaran dengan tarif sesuai tahun ajaran tagihan
        $jenisTagihan = TarifTagihan::with('jenisTagihan')
            ->where('tahun_ajaran_id', $tagihan->tahun_ajaran_id)
            ->where('status', 'aktif')
            ->get()
            ->map(function ($tarif) {
                $jp = $tarif->jenisTagihan;
                $jp->nominal = $tarif->nominal; // Inject nominal dari tarif
                return $jp;
            })
            ->unique('id');

        $kelas = Kelas::where('status', 'aktif')->orderBy('kelas')->get();
        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();
        $isReadonly = $tagihan->sudah_dibayar > 0;
        return view('admin.pembayaran.tagihan.edit', compact('tagihan', 'siswa', 'jenisTagihan', 'kelas', 'tahunAjaran', 'isReadonly'));
    }

    /**
     * Update tagihan (UPDATED: Support Tarif Pembayaran).
     */
    public function update(Request $request, Tagihan $tagihan)
    {
        $request->validate([
            'siswa_nis' => 'required|exists:siswa,nis',
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'jatuh_tempo' => 'required|date',
        ]);

        // Gunakan tahun ajaran dari request, kalau kosong pakai yang lama
        $tahunAjaranId = $request->tahun_ajaran_id ?? $tagihan->tahun_ajaran_id;

        // Ambil tarif baru
        $tarif = TarifTagihan::getTarif(
            $request->jenis_tagihan_id,
            $tahunAjaranId
        );

        if (!$tarif) {
            return back()->withErrors(['jenis_tagihan_id' => 'Tarif untuk kombinasi ini belum diset!']);
        }

        $siswa = Siswa::where('nis', $request->siswa_nis)->first();

        $tagihan->update([
            'siswa_nis' => $request->siswa_nis,
            'kelas_id' => $siswa->kelas_id,
            'jenis_tagihan_id' => $request->jenis_tagihan_id,
            'tahun_ajaran_id' => $tahunAjaranId,
            'total_tagihan' => $tarif->nominal,
            'jatuh_tempo' => $request->jatuh_tempo,
        ]);

        return redirect()->route('tagihan.index')
            ->with('success', 'Tagihan berhasil diupdate');
    }

    /**
     * AJAX: Get tarif by jenis pembayaran & tahun ajaran
     */
    public function getTarif(Request $request)
    {
        $tarif = TarifTagihan::getTarif(
            $request->jenis_tagihan_id,
            $request->tahun_ajaran_id
        );

        if ($tarif) {
            return response()->json([
                'success' => true,
                'nominal' => $tarif->nominal,
                'nominal_format' => $tarif->nominal_format,
                'tipe'          => $tarif->jenisTagihan->tipe,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tarif belum diset untuk kombinasi ini'
            ], 404);
        }
    }

    /**
     * Hapus tagihan.
     */
    public function destroy(Tagihan $tagihan)
    {
        // Cek apakah status lunas
        if ($tagihan->status === 'lunas') {
            return redirect()->route('tagihan.index')
                ->with('error', 'Tagihan tidak dapat dihapus karena sudah lunas.');
        }

        // Cek apakah tagihan sudah pernah dibayar
        if ($tagihan->sudah_dibayar > 0) {
            return redirect()->route('tagihan.index')
                ->with('error', 'Tagihan tidak dapat dihapus karena sudah ada pembayaran sebesar Rp' . number_format($tagihan->sudah_dibayar, 0, ',', '.'));
        }

        $tagihan->delete();
        return redirect()->route('tagihan.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }
}
