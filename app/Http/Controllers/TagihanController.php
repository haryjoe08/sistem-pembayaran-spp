<?php

namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    /**
     * Tampilkan daftar tagihan.
     */
    public function index()
    {
        $data = Tagihan::with(['siswa', 'jenisPembayaran', 'kelas'])
            ->where('status', 'Belum Lunas')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.pembayaran.tagihan.index', compact('data'));
    }

    /**
     * Tampilkan form tambah tagihan.
     */
    public function create()
    {
        $siswa = Siswa::all();
        $jenisPembayaran = JenisPembayaran::all();
        $kelas = Kelas::all();

        return view('admin.pembayaran.tagihan.create', compact('siswa', 'jenisPembayaran', 'kelas'));
    }

    /**
     * Simpan tagihan baru.
     */
  public function store(Request $request)
{
    $request->validate([
        'mode' => 'required|in:siswa,kelas',
        'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
        'jatuh_tempo' => 'nullable|date', // TAMBAHKAN VALIDASI
    ]);

    $jenisPembayaran = JenisPembayaran::findOrFail($request->jenis_pembayaran_id);
    $nominal = $jenisPembayaran->nominal;
    
    // Default jatuh tempo (contoh: 30 hari dari sekarang)
    $jatuhTempo = $request->jatuh_tempo ?? now()->addDays(30);

    DB::beginTransaction();
    try {
        if ($request->mode === 'siswa') {
            $request->validate([
                'siswa_nis' => 'required|exists:siswa,nis',
            ]);

            $siswa = Siswa::find($request->siswa_nis);
            
            if (!$siswa->kelas_id) {
                return back()->withErrors(['siswa_nis' => 'Siswa belum di-assign ke kelas.']);
            }

            $existingTagihan = Tagihan::where('siswa_nis', $request->siswa_nis)
                                     ->where('jenis_pembayaran_id', $request->jenis_pembayaran_id)
                                     ->where('status', '!=', 'lunas')
                                     ->first();
            
            if ($existingTagihan) {
                return back()->withErrors(['siswa_nis' => 'Siswa sudah memiliki tagihan untuk jenis pembayaran ini.']);
            }

            Tagihan::create([
                'siswa_nis' => $request->siswa_nis,
                'kelas_id' => $siswa->kelas_id,
                'jenis_pembayaran_id' => $request->jenis_pembayaran_id,
                'total_tagihan' => $nominal,
                'sudah_dibayar' => 0,
                'status' => 'belum lunas',
                'jatuh_tempo' => $jatuhTempo, // TAMBAHKAN INI
            ]);

            $successMessage = 'Tagihan berhasil ditambahkan.';

        } else { // mode kelas
            $request->validate([
                'kelas_id' => 'nullable|exists:kelas,id',
            ]);

            $query = Siswa::whereNotNull('kelas_id');
            
            if ($request->kelas_id) {
                $query->where('kelas_id', $request->kelas_id);
            }
            
            $siswas = $query->whereDoesntHave('tagihan', function ($query) use ($request) {
                $query->where('jenis_pembayaran_id', $request->jenis_pembayaran_id)
                      ->where('status', '!=', 'lunas');
            })->get();

            if ($siswas->isEmpty()) {
                $message = $request->kelas_id
                    ? 'Tidak ada siswa di kelas ini atau semua siswa sudah memiliki tagihan untuk jenis pembayaran ini.'
                    : 'Tidak ada siswa yang perlu dibuatkan tagihan ini (semua sudah memiliki tagihan atau sudah lunas).';

                return back()->withErrors(['kelas_id' => $message]);
            }

            $tagihanData = [];
            foreach ($siswas as $s) {
                $tagihanData[] = [
                    'siswa_nis' => $s->nis,
                    'kelas_id' => $s->kelas_id,
                    'jenis_pembayaran_id' => $request->jenis_pembayaran_id,
                    'total_tagihan' => $nominal,
                    'sudah_dibayar' => 0,
                    'status' => 'belum lunas',
                    'jatuh_tempo' => $jatuhTempo, // TAMBAHKAN INI
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Tagihan::insert($tagihanData);

            $kelasInfo = $request->kelas_id ? 'kelas ' . Kelas::find($request->kelas_id)->kelas : 'semua kelas';
            $successMessage = "Tagihan berhasil ditambahkan untuk {$siswas->count()} siswa dari {$kelasInfo}.";
        }

        DB::commit();
        return redirect()->route('tagihan.index')->with('success', $successMessage);

    } catch (\Exception $e) {
        DB::rollback();
        return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan tagihan.']);
    }
}

    /**
     * Tampilkan form edit tagihan.
     */
    /**
     * Tampilkan form edit tagihan.
     */
    public function edit(Tagihan $tagihan)
    {
        // Ambil data yang diperlukan
        $siswa = Siswa::whereNotNull('kelas_id')->orderBy('nama')->get();
        $jenisPembayaran = JenisPembayaran::all();
        $kelas = Kelas::all();

        return view('admin.pembayaran.tagihan.edit', compact('tagihan', 'siswa', 'jenisPembayaran', 'kelas'));
    }

    /**
     * Update tagihan.
     */
public function update(Request $request, $id)
{
    $request->validate([
        'siswa_nis' => 'required|exists:siswa,nis',
        'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
        'jatuh_tempo' => 'required|date|after_or_equal:today', // TAMBAHKAN INI
    ]);

    $tagihan = Tagihan::findOrFail($id);
    
    $tagihan->update([
        'siswa_nis' => $request->siswa_nis,
        'jenis_pembayaran_id' => $request->jenis_pembayaran_id,
        'jatuh_tempo' => $request->jatuh_tempo, // TAMBAHKAN INI
        'total_tagihan' => JenisPembayaran::find($request->jenis_pembayaran_id)->nominal,
        'kelas_id' => Siswa::where('nis', $request->siswa_nis)->first()->kelas_id,
    ]);

    return redirect()->route('tagihan.index')
                     ->with('success', 'Tagihan berhasil diupdate!');
}

    /**
     * Bulk edit tagihan berdasarkan kriteria
     */
    public function bulkEdit(Request $request)
    {
        $request->validate([
            'action' => 'required|in:update_nominal,mark_lunas,mark_belum_lunas',
            'tagihan_ids' => 'required|array',
            'tagihan_ids.*' => 'exists:tagihan,id',
            'new_nominal' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $tagihanIds = $request->tagihan_ids;
            $affectedRows = 0;

            switch ($request->action) {
                case 'update_nominal':
                    if (!$request->new_nominal) {
                        return back()->withErrors(['new_nominal' => 'Nominal baru harus diisi.']);
                    }

                    $affectedRows = Tagihan::whereIn('id', $tagihanIds)
                        ->where('status', '!=', 'lunas')
                        ->update(['total_tagihan' => $request->new_nominal]);
                    break;

                case 'mark_lunas':
                    // Update tagihan jadi lunas, dan set sudah_dibayar = total_tagihan
                    $tagihans = Tagihan::whereIn('id', $tagihanIds)
                        ->where('status', '!=', 'lunas')
                        ->get();

                    foreach ($tagihans as $tagihan) {
                        $tagihan->update([
                            'sudah_dibayar' => $tagihan->total_tagihan,
                            'status' => 'lunas'
                        ]);
                        $affectedRows++;
                    }
                    break;

                case 'mark_belum_lunas':
                    $affectedRows = Tagihan::whereIn('id', $tagihanIds)
                        ->where('status', 'lunas')
                        ->update([
                            'sudah_dibayar' => 0,
                            'status' => 'belum lunas'
                        ]);
                    break;
            }

            DB::commit();

            $actionText = [
                'update_nominal' => 'nominal diperbarui',
                'mark_lunas' => 'ditandai sebagai lunas',
                'mark_belum_lunas' => 'ditandai sebagai belum lunas'
            ];

            return redirect()->route('tagihan.index')
                ->with('success', "{$affectedRows} tagihan berhasil {$actionText[$request->action]}.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui tagihan.']);
        }
    }

    /**
     * Edit tagihan berdasarkan kelas dan jenis pembayaran
     */
    public function editByClass(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
            'action' => 'required|in:view,update',
            'new_nominal' => 'nullable|numeric|min:0',
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);
        $jenisPembayaran = JenisPembayaran::findOrFail($request->jenis_pembayaran_id);

        if ($request->action === 'view') {
            // Tampilkan tagihan untuk kelas dan jenis pembayaran tertentu
            $tagihans = Tagihan::where('kelas_id', $request->kelas_id)
                ->where('jenis_pembayaran_id', $request->jenis_pembayaran_id)
                ->with(['siswa', 'jenisPembayaran', 'kelas'])
                ->orderBy('created_at', 'desc')
                ->paginate(50);

            return view('admin.pembayaran.tagihan.bulk-edit', compact('tagihans', 'kelas', 'jenisPembayaran'));
        }

        if ($request->action === 'update' && $request->new_nominal) {
            DB::beginTransaction();
            try {
                $affectedRows = Tagihan::where('kelas_id', $request->kelas_id)
                    ->where('jenis_pembayaran_id', $request->jenis_pembayaran_id)
                    ->where('status', '!=', 'lunas')
                    ->update(['total_tagihan' => $request->new_nominal]);

                DB::commit();
                return redirect()->route('tagihan.index')
                    ->with('success', "{$affectedRows} tagihan untuk kelas {$kelas->kelas} berhasil diperbarui.");
            } catch (\Exception $e) {
                DB::rollback();
                return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui tagihan.']);
            }
        }

        return back()->withErrors(['error' => 'Action tidak valid atau nominal tidak diisi.']);
    }
    /**
     * Hapus tagihan.
     */
    public function destroy(Tagihan $tagihan)
    {
        $tagihan->delete();

        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil dihapus.');
    }
}
