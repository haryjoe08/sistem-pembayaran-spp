<?php

namespace App\Http\Controllers;

use App\Models\TarifTagihan;
use App\Models\JenisPembayaran;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarifTagihanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tahunAjaranId = $request->input('tahun_ajaran_id', TahunAjaran::where('status', 'aktif')->first()->id ?? null);

        $tarif = TarifTagihan::with(['jenisTagihan', 'tahunAjaran'])
            ->when($tahunAjaranId, function ($q) use ($tahunAjaranId) {
                return $q->where('tahun_ajaran_id', $tahunAjaranId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();

        return view('admin.pembayaran.tarif_pembayaran.index', compact('tarif', 'tahunAjaran', 'tahunAjaranId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisTagihan = JenisPembayaran::where('status', 'aktif')->orderBy('nama')->get();
        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();

        return view('admin.pembayaran.tarif_pembayaran.create', compact('jenisTagihan', 'tahunAjaran'));
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
    {
        $request->validate([
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500'
        ], [
            'jenis_tagihan_id.required' => 'Jenis pembayaran harus dipilih',
            'tahun_ajaran_id.required' => 'Tahun ajaran harus dipilih',
            'nominal.required' => 'Nominal harus diisi',
            'nominal.numeric' => 'Nominal harus berupa angka',
            'nominal.min' => 'Nominal minimal 0'
        ]);

        // Cek duplikat
        $exists = TarifTagihan::where('jenis_tagihan_id', $request->jenis_tagihan_id)
            ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Tarif untuk jenis pembayaran dan tahun ajaran ini sudah ada! Silakan edit yang sudah ada.');
        }

        try {
            TarifTagihan::create($request->all());

            return redirect()
                ->route('tarif-tagihan.index')
                ->with('success', 'Tarif pembayaran berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan tarif: ' . $e->getMessage());
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tarif = TarifTagihan::with(['jenisTagihan', 'tahunAjaran'])->findOrFail($id);
        $jenisTagihan = JenisPembayaran::where('status', 'aktif')->orderBy('nama')->get();
        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();

        return view('admin.pembayaran.tarif_pembayaran.edit', compact('tarif', 'jenisTagihan', 'tahunAjaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500'
        ]);

        try {
            $tarif = TarifTagihan::findOrFail($id);

            // Cek duplikat (kecuali diri sendiri)
            $exists = TarifTagihan::where('jenis_tagihan_id', $request->jenis_tagihan_id)
                ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->with('error', 'Tarif untuk jenis pembayaran dan tahun ajaran ini sudah ada!');
            }

            $tarif->update($request->all());

            return redirect()
                ->route('tarif-tagihan.index')
                ->with('success', 'Tarif pembayaran berhasil diupdate!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal mengupdate tarif: ' . $e->getMessage());
        }
    }

    public function aktifkan($id)
    {
        $tarif = TarifTagihan::findOrFail($id);

        // nonaktifkan tarif lain (kombinasi sama)
        TarifTagihan::where('jenis_tagihan_id', $tarif->jenis_tagihan_id)
            ->where('tahun_ajaran_id', $tarif->tahun_ajaran_id)
            ->update(['status' => 'nonaktif']);

        // aktifkan yang dipilih
        $tarif->update(['status' => 'aktif']);

        return redirect()->back()->with('success', 'Tarif berhasil diaktifkan.');
    }

    public function nonaktif($id)
    {
        $tarif = TarifTagihan::findOrFail($id);
        $tarif->update(['status' => 'nonaktif']);

        return redirect()->back()->with('success', 'Tarif berhasil dinonaktifkan.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $tarif = TarifTagihan::findOrFail($id);

            // Cek apakah ada tagihan yang menggunakan tarif ini
            // (Optional, tergantung apakah ada relasi)

            $tarif->delete();

            return redirect()
                ->back()
                ->with('success', 'Tarif pembayaran berhasil dihapus!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal menghapus tarif: ' . $e->getMessage());
        }
    }

    /**
     * Copy tarif dari tahun ajaran sebelumnya
     */
}
