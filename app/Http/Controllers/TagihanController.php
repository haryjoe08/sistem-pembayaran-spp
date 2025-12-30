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

        $data = $query->orderBy('created_at', 'desc')->paginate(10);

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
        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();

        return view('admin.pembayaran.tagihan.create', compact('siswa', 'jenisTagihan', 'kelas', 'tahunAjaran'));
    }

    /**
     * Simpan tagihan baru (UPDATED: Support Tarif Pembayaran).
     */
    public function store(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:siswa,kelas',
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'jatuh_tempo' => 'nullable|date',
        ]);

        // Ambil tarif
        $tarif = TarifTagihan::getTarif(
            $request->jenis_tagihan_id,
            $request->tahun_ajaran_id
        );

        if (!$tarif) {
            return back()
                ->withInput()
                ->withErrors([
                    'jenis_tagihan_id' =>
                    'Tarif untuk jenis tagihan dan tahun ajaran ini belum diset. Silakan set di menu Tarif Tagihan.'
                ]);
        }

        $nominal = $tarif->nominal;
        $jatuhTempo = $request->jatuh_tempo ?? now()->addDays(30);

        DB::beginTransaction();
        try {
            // ================= MODE SISWA =================
            if ($request->mode === 'siswa') {

                $request->validate([
                    'siswa_nis' => 'required|exists:siswa,nis',
                ]);

                $siswa = Siswa::where('nis', $request->siswa_nis)->first();

                if (!$siswa->kelas_id) {
                    return back()->withErrors([
                        'siswa_nis' => 'Siswa belum di-assign ke kelas.'
                    ]);
                }

                // Cek duplikat
                $existing = Tagihan::where('siswa_nis', $request->siswa_nis)
                    ->where('jenis_tagihan_id', $request->jenis_tagihan_id)
                    ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                    ->where('status', '!=', 'lunas')
                    ->exists();

                if ($existing) {
                    return back()->withErrors([
                        'siswa_nis' =>
                        'Siswa sudah memiliki tagihan untuk jenis tagihan dan tahun ajaran ini.'
                    ]);
                }

                Tagihan::create([
                    'siswa_nis' => $request->siswa_nis,
                    'kelas_id' => $siswa->kelas_id,
                    'jenis_tagihan_id' => $request->jenis_tagihan_id,
                    'tahun_ajaran_id' => $request->tahun_ajaran_id,
                    'total_tagihan' => $nominal,
                    'sudah_dibayar' => 0,
                    'status' => 'belum lunas',
                    'jatuh_tempo' => $jatuhTempo,
                ]);

                $successMessage =
                    'Tagihan berhasil ditambahkan dengan nominal ' . $tarif->nominal_format;
            }
            // ================= MODE KELAS =================
            else {

                $request->validate([
                    'kelas_id' => 'nullable|exists:kelas,id',
                ]);

                $query = Siswa::where('status', 'aktif')
                    ->whereNotNull('kelas_id');

                if ($request->kelas_id) {
                    $query->where('kelas_id', $request->kelas_id);
                }

                $siswas = $query->whereDoesntHave('tagihan', function ($q) use ($request) {
                    $q->where('jenis_tagihan_id', $request->jenis_tagihan_id)
                        ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                        ->where('status', '!=', 'lunas');
                })->get();

                if ($siswas->isEmpty()) {
                    return back()->withErrors([
                        'kelas_id' =>
                        'Tidak ada siswa yang perlu dibuatkan tagihan untuk kombinasi ini.'
                    ]);
                }

                $tagihanData = [];
                foreach ($siswas as $siswa) {
                    $tagihanData[] = [
                        'siswa_nis' => $siswa->nis,
                        'kelas_id' => $siswa->kelas_id,
                        'jenis_tagihan_id' => $request->jenis_tagihan_id,
                        'tahun_ajaran_id' => $request->tahun_ajaran_id,
                        'total_tagihan' => $nominal,
                        'sudah_dibayar' => 0,
                        'status' => 'belum lunas',
                        'jatuh_tempo' => $jatuhTempo,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                Tagihan::insert($tagihanData);

                $kelasInfo = $request->kelas_id
                    ? 'kelas ' . Kelas::find($request->kelas_id)->kelas
                    : 'semua kelas';

                $successMessage =
                    "Tagihan berhasil ditambahkan untuk {$siswas->count()} siswa dari {$kelasInfo}
                dengan nominal {$tarif->nominal_format}.";
            }

            DB::commit();
            return redirect()->route('tagihan.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
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

        return view('admin.pembayaran.tagihan.edit', compact('tagihan', 'siswa', 'jenisTagihan', 'kelas', 'tahunAjaran'));
    }

    /**
     * Update tagihan (UPDATED: Support Tarif Pembayaran).
     */
    public function update(Request $request, Tagihan $tagihan)
    {
        $request->validate([
            'siswa_nis' => 'required|exists:siswa,nis',
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
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
            ->with('success', 'Tagihan berhasil diupdate dengan nominal ' . $tarif->nominal_format);
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
                'nominal_format' => $tarif->nominal_format
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
        // Cek apakah tagihan sudah pernah dibayar
        if ($tagihan->sudah_dibayar > 0) {
            return redirect()->route('tagihan.index')
                ->with('error', 'Tagihan tidak dapat dihapus karena sudah ada pembayaran sebesar Rp' . number_format($tagihan->sudah_dibayar, 0, ',', '.'));
        }

        // Cek apakah status lunas
        if ($tagihan->status === 'lunas') {
            return redirect()->route('tagihan.index')
                ->with('error', 'Tagihan tidak dapat dihapus karena sudah lunas.');
        }

        $tagihan->delete();
        return redirect()->route('tagihan.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }
}
