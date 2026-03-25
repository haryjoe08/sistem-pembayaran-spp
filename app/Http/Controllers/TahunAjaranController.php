<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunAjaranController extends Controller
{
    public function index()
    {
        // ambil semua, terbaru dulu
        $tahunAjarans = TahunAjaran::orderBy('id', 'desc')->get();
        return view('admin.master_data.tahun_ajaran.create', compact('tahunAjarans'));
    }


    public function store(Request $request)
    {
        // validasi hanya untuk 'tahun'
        $request->validate([
            'tahun' => 'required|string|max:50|unique:tahun_ajaran,tahun',
        ]);

     
        TahunAjaran::create([
            'tahun' => $request->tahun,
            'status' => 'nonaktif',
        ]);

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun Ajaran berhasil ditambahkan.');
    }

    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('admin.master_data.tahun_ajaran.edit', compact('tahunAjaran'));
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'tahun' => 'required|string|max:50|unique:tahun_ajaran,tahun,' . $tahunAjaran->id,
        ]);

        $tahunAjaran->update([
            'tahun' => $request->tahun,
        ]);

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun Ajaran berhasil diperbarui.');
    }
    public function activate(TahunAjaran $tahunAjaran)
    {
        DB::transaction(function () use ($tahunAjaran) {

            // nonaktifkan semua tahun ajaran
            TahunAjaran::where('status', 'aktif')
                ->update(['status' => 'nonaktif']);

            // aktifkan yang dipilih
            $tahunAjaran->update(['status' => 'aktif']);
        });

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diaktifkan.');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->delete();
        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun Ajaran berhasil dihapus.');
    }
}
