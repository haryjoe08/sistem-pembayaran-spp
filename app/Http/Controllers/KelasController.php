<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    // tampilkan index
    public function index()
    {
        $kelas = Kelas::orderBy('id', 'asc')->get();
        return view('admin.master_data.kelas.create', compact('kelas'));
    }

    // halaman create
    public function create()
    {
        return view('admin.master_data.kelas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas' => 'required|string|max:100',
        ]);

        Kelas::create($request->only(['kelas']));

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kela)
    {
        return view('admin.master_data.kelas.edit', compact('kela'));
    }

    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'kelas' => 'required|string|max:100',
        ]);

        $kela->update($request->only(['kelas']));

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
