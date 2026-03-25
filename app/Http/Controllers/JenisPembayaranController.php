<?php

namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use Illuminate\Http\Request;

class JenisPembayaranController extends Controller
{
    public function index()
    {
        $data = JenisPembayaran::orderBy('id', 'desc')->paginate(10);
        return view('admin.pembayaran.jenis_pembayaran.index', compact('data'));
    }

    public function create()
    {
        return view('admin.pembayaran.jenis_pembayaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:jenis_tagihan,nama',
            'tipe' => 'required|string|in:bulanan,tahunan,insidental',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        JenisPembayaran::create([
            'nama' => $request->nama,
            'tipe' => $request->tipe,
            'deskripsi' => $request->deskripsi,
            'status' => 'aktif', // default
        ]);

        return redirect()
            ->route('jenis-pembayaran.index')
            ->with('success', 'Jenis pembayaran berhasil ditambahkan.');
    }

    public function edit(JenisPembayaran $jenis_pembayaran)
    {
        return view(
            'admin.pembayaran.jenis_pembayaran.edit',
            compact('jenis_pembayaran')
        );
    }

    public function update(Request $request, JenisPembayaran $jenis_pembayaran)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:jenis_tagihan,nama,' . $jenis_pembayaran->id,
            'tipe' => 'required|string|in:bulanan,tahunan,insidental',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $jenis_pembayaran->update([
            'nama' => $request->nama,
            'tipe' => $request->tipe,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()
            ->route('jenis-pembayaran.index')
            ->with('success', 'Jenis pembayaran berhasil diperbarui.');
    }

    /**
     * NONAKTIFKAN (soft delete versi bisnis)
     */
    public function nonaktifkan(JenisPembayaran $jenis_pembayaran)
    {
        $jenis_pembayaran->update([
            'status' => 'nonaktif'
        ]);

        return back()->with('success', 'Jenis pembayaran berhasil dinonaktifkan.');
    }

    /**
     * AKTIFKAN kembali
     */
    public function aktifkan(JenisPembayaran $jenis_pembayaran)
    {
        $jenis_pembayaran->update([
            'status' => 'aktif'
        ]);

        return back()->with('success', 'Jenis pembayaran berhasil diaktifkan.');
    }

    /**
     * OPTIONAL: kalau benar-benar mau hapus (biasanya jarang dipakai)
     */
    public function destroy(JenisPembayaran $jenis_pembayaran)
    {
        $jenis_pembayaran->delete();

        return redirect()
            ->route('jenis-pembayaran.index')
            ->with('success', 'Jenis pembayaran berhasil dihapus.');
    }
}
