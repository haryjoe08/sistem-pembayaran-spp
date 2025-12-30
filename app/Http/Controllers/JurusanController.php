<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::orderBy('id')->get();
        return view('admin.master_data.jurusan.create', compact('jurusans'));
    }

    // =====================
    // STORE
    // =====================
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        Jurusan::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'status' => 'aktif', // default aktif
        ]);

        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan berhasil ditambahkan.');
    }

    // =====================
    // EDIT
    // =====================
    public function edit($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        return view('admin.master_data.jurusan.edit', compact('jurusan'));
    }

    // =====================
    // UPDATE
    // =====================
    public function update(Request $request, $id)
    {
        $jurusan = Jurusan::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $jurusan->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan berhasil diperbarui.');
    }

    // =====================
    // NONAKTIF
    // =====================
    public function nonaktif($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $jurusan->update(['status' => 'nonaktif']);

        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan berhasil dinonaktifkan.');
    }

    // =====================
    // AKTIFKAN
    // =====================
    public function aktifkan($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $jurusan->update(['status' => 'aktif']);

        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan berhasil diaktifkan kembali.');
    }
}
