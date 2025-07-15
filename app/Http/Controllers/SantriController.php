<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Santri;

class SantriController extends Controller
{
    public function create()
    {
        return view('santri.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'kelas' => 'required|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'nama_orangtua' => 'nullable|string|max:255',
        ]);

        \App\Models\Santri::create($validated);
        return redirect()->route('santri.create')->with('success', 'Santri berhasil ditambahkan!');
    }

    public function index()
{
    $santris = \App\Models\Santri::paginate(10); // Menampilkan 10 data per halaman
    return view('data-santri', compact('santris'));
}

public function edit($id)
{
    $santri = Santri::findOrFail($id);
    return view('santri.edit', compact('santri'));
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'tgl_lahir' => 'required|date',
        'kelas' => 'required|string|max:100',
        'alamat' => 'nullable|string|max:255',
        'nama_orangtua' => 'nullable|string|max:255',
    ]);

    $santri = Santri::findOrFail($id);
    $santri->update($validated);

    return redirect()->route('santri.index')->with('success', 'Data berhasil diupdate.');
}

public function destroy($id)
{
    $santri = Santri::findOrFail($id);
    $santri->delete();

    return redirect()->route('santri.index')->with('success', 'Data berhasil dihapus.');
}

}
