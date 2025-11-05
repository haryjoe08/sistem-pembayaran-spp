<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;

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

        // create via Eloquent (memerlukan $fillable di model)
        TahunAjaran::create([
            'tahun' => $request->tahun,
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

    public function destroy(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->delete();
        return redirect()->route('tahun-ajaran.index')
                         ->with('success', 'Tahun Ajaran berhasil dihapus.');
    }
}
