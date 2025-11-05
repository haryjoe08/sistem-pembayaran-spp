<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TerimaPembayaranController extends Controller
{
    public function index()
    {
        return view('admin.pembayaran.terima_pembayaran.index');
    }

    public function cari(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|min:1'
        ]);

        $siswa = Siswa::where('nis', $request->keyword)
            ->orWhere('nama', 'like', "%{$request->keyword}%")
            ->with(['kelas', 'tagihan.jenisPembayaran'])
            ->first();

        if (!$siswa) {
            return back()->withErrors(['keyword' => 'Siswa tidak ditemukan.'])->withInput();
        }

        return view('admin.pembayaran.terima_pembayaran.index', compact('siswa'));
    }

    public function proses(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1',
            'catatan' => 'nullable|string|max:500',
        ]);

        $tagihan = Tagihan::findOrFail($id);
        $jumlahBayar = $request->jumlah;
        $sisaBelumBayar = $tagihan->total_tagihan - $tagihan->sudah_dibayar;

        // Validasi jumlah pembayaran
        if ($jumlahBayar > $sisaBelumBayar) {
            return back()->withErrors([
                'error' => 'Jumlah pembayaran (Rp ' . number_format($jumlahBayar, 0, ',', '.') . ') melebihi sisa tagihan (Rp ' . number_format($sisaBelumBayar, 0, ',', '.') . ')'
            ]);
        }

        DB::beginTransaction();
        try {
            // Update tagihan
            $tagihan->sudah_dibayar += $jumlahBayar;
            
            if ($tagihan->sudah_dibayar >= $tagihan->total_tagihan) {
                $tagihan->status = 'lunas';
                $tagihan->sudah_dibayar = $tagihan->total_tagihan;
            } else {
                $tagihan->status = 'belum lunas';
            }
            
            $tagihan->save();

            // Insert ke tabel transaksi
            Transaksi::create([
                'tagihan_id' => $tagihan->id,
                'siswa_nis' => $tagihan->siswa_nis,
                'tanggal' => now(),
                'jumlah_bayar' => $jumlahBayar,
                'metode' => 'cash', // default cash
                'keterangan' => $request->catatan,
            ]);

            DB::commit();

            $sisaTagihan = $tagihan->total_tagihan - $tagihan->sudah_dibayar;
            $message = 'Pembayaran berhasil dicatat! ';
            
            if ($tagihan->status == 'lunas') {
                $message .= 'Tagihan sudah lunas.';
            } else {
                $message .= 'Sisa tagihan: Rp ' . number_format($sisaTagihan, 0, ',', '.');
            }

            return redirect()
                ->route('pembayaran.cari', ['keyword' => $tagihan->siswa_nis])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}