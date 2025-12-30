<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KenaikanKelasController extends Controller
{
    // Step 1: Form pilih tahun ajaran
    public function index()
    {
        return view('admin.kenaikan_kelas.index');
    }

    // Step 2: Preview siswa per kelas
    public function preview(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string'
        ]);

        $tahunAjaran = $request->tahun_ajaran;

        // Ambil siswa per kelas yang status = aktif
        $kelasX = Siswa::where('kelas', 'X')
                       ->where('status', 'aktif')
                       ->orderBy('nama')
                       ->get();
        
        $kelasXI = Siswa::where('kelas', 'XI')
                        ->where('status', 'aktif')
                        ->orderBy('nama')
                        ->get();
        
        $kelasXII = Siswa::where('kelas', 'XII')
                         ->where('status', 'aktif')
                         ->orderBy('nama')
                         ->get();

        return view('admin.kenaikan-kelas.preview', compact(
            'tahunAjaran',
            'kelasX',
            'kelasXI',
            'kelasXII'
        ));
    }

    // Step 3: Execute kenaikan kelas (SIMPLE - No History)
    public function execute(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string',
            'excluded_nis' => 'array',
            'excluded_nis.*' => 'integer',
            'lulus_nis' => 'array',
            'lulus_nis.*' => 'integer'
        ]);

        $excludedNis = $request->excluded_nis ?? [];
        $lulusNis = $request->lulus_nis ?? [];

        $naik = 0;
        $tinggal = 0;
        $lulus = 0;

        DB::beginTransaction();
        try {
            // 1. KELAS X → XI (kecuali yang di-exclude)
            Siswa::where('kelas', 'X')
                 ->where('status', 'aktif')
                 ->whereNotIn('nis', $excludedNis)
                 ->update(['kelas' => 'XI']);
            
            $naik += Siswa::where('kelas', 'XI')
                          ->where('status', 'aktif')
                          ->whereNotIn('nis', $excludedNis)
                          ->count();

            // 2. KELAS XI → XII (kecuali yang di-exclude)
            Siswa::where('kelas', 'XI')
                 ->where('status', 'aktif')
                 ->whereNotIn('nis', $excludedNis)
                 ->update(['kelas' => 'XII']);
            
            $naik += Siswa::where('kelas', 'XII')
                          ->where('status', 'aktif')
                          ->whereNotIn('nis', $excludedNis)
                          ->count();

            // 3. KELAS XII → LULUS
            Siswa::where('kelas', 'XII')
                 ->where('status', 'aktif')
                 ->whereIn('nis', $lulusNis)
                 ->update(['status' => 'lulus']);
            
            $lulus = count($lulusNis);

            // Count tinggal kelas
            $tinggal = count($excludedNis);

            DB::commit();

            return redirect()
                ->route('admin.kenaikan-kelas.index')
                ->with('success', "Kenaikan kelas berhasil! Naik: {$naik}, Tinggal: {$tinggal}, Lulus: {$lulus}");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}