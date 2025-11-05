<?php
namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use Illuminate\Http\Request;

class JenisPembayaranController extends Controller
{
    public function index()
    {
        $data = JenisPembayaran::paginate(5);
        return view('admin.pembayaran.jenis_pembayaran.index', compact('data'));
    }

    public function create()
    {
        return view('admin.pembayaran.jenis_pembayaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nominal' => 'nullable|numeric',
        ]);

        JenisPembayaran::create($request->all());

        return redirect()->route('jenis-pembayaran.index')->with('success', 'Jenis pembayaran berhasil ditambahkan.');
    }

    public function edit(JenisPembayaran $jenis_pembayaran)
    {
        return view('admin.pembayaran.jenis_pembayaran.edit', compact('jenis_pembayaran'));
    }

    public function update(Request $request, JenisPembayaran $jenis_pembayaran)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nominal' => 'nullable|numeric',
        ]);

        $jenis_pembayaran->update($request->all());

        return redirect()->route('jenis-pembayaran.index')->with('success', 'Jenis pembayaran berhasil diperbarui.');
    }

    public function destroy(JenisPembayaran $jenis_pembayaran)
    {
        $jenis_pembayaran->delete();
        return redirect()->route('jenis-pembayaran.index')->with('success', 'Jenis pembayaran berhasil dihapus.');
    }
}
