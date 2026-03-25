<?php

namespace App\Http\Controllers;

use App\Models\{Tagihan, Siswa, Transaksi};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatatPembayaranController extends Controller
{
    public function index()
    {
        return view('admin.pembayaran.catat_pembayaran.index');
    }

    public function cari(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|min:1'
        ], [
            'keyword.required' => 'Masukkan NIS atau nama siswa',
            'keyword.min'      => 'Minimal 1 karakter'
        ]);

        $hasil = Siswa::with(['kelas', 'tagihan.jenisTagihan'])
            ->where('nis', $request->keyword)
            ->orWhere('nama', 'like', "%{$request->keyword}%")
            ->get();

        if ($hasil->isEmpty()) {
            return back()->withInput()->with('swal_error', [
                'title' => 'Siswa Tidak Ditemukan!',
                'text'  => "Siswa dengan NIS atau nama {$request->keyword} tidak ditemukan.",
                'icon'  => 'error',
            ]);
        }

        // Jika hasil hanya 1, langsung tampilkan detail
        if ($hasil->count() === 1) {
            $siswa = $hasil->first();
            return view('admin.pembayaran.catat_pembayaran.index', compact('siswa'));
        }

        // Jika lebih dari 1, tampilkan daftar pilihan
        return view('admin.pembayaran.catat_pembayaran.index', ['daftarSiswa' => $hasil]);
    }


    public function proses(Request $request, $id)
    {
        $request->validate([
            'jumlah'  => 'required|numeric|min:1',
            'catatan' => 'nullable|string|max:500',
        ]);

        $tagihan     = Tagihan::findOrFail($id);
        $jumlahBayar = (int) $request->jumlah;

        DB::beginTransaction();
        try {
            // Update tagihan
            $tagihan->sudah_dibayar += $jumlahBayar;

            if ($tagihan->sudah_dibayar >= $tagihan->total_tagihan) {
                $tagihan->update([
                    'sudah_dibayar' => $tagihan->total_tagihan,
                    'status'        => 'lunas',
                ]);
            } else {
                $tagihan->update(['status' => 'belum lunas']);
            }

            // Simpan transaksi
            $transaksi = Transaksi::create([
                'tagihan_id'   => $tagihan->id,
                'siswa_nis'    => $tagihan->siswa_nis,
                'tanggal'      => now(),
                'jumlah_bayar' => $jumlahBayar,
                'metode'       => 'cash',
                'keterangan'   => $request->catatan,
            ]);

            DB::commit();

            return redirect()
                ->route('pembayaran.cari', ['keyword' => $tagihan->siswa_nis])
                ->with('swal_success', [
                    'status'        => $tagihan->status,
                    'siswa'         => $tagihan->siswa->nama,
                    'jenis_tagihan' => $tagihan->jenisTagihan->nama,
                    'jumlah_bayar'  => $jumlahBayar,
                    'total'         => $tagihan->total_tagihan,
                    'sudah_bayar'   => $tagihan->sudah_dibayar,
                    'sisa'          => $tagihan->total_tagihan - $tagihan->sudah_dibayar,
                    'catatan'       => $request->catatan,
                ])
                ->with('transaksi_id', $transaksi->id);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('swal_error', [
                'title' => 'Terjadi Kesalahan!',
                'text'  => $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }
}
