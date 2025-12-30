<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaSideTagihanController extends Controller
{
    /**
     * Dashboard Siswa
     */
    public function dashboard()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect()->route('login')->withErrors(['error' => 'Data siswa tidak ditemukan.']);
        }

        // Statistik
        $tagihans = Tagihan::where('siswa_nis', $siswa->nis)->get();

        $totalTagihan = $tagihans->sum('total_tagihan');
        $sudahDibayar = $tagihans->sum('sudah_dibayar');
        $totalTunggakan = $totalTagihan - $sudahDibayar;

        $tagihanAktif = $tagihans->where('status', '!=', 'lunas')->count();
        $belumLunas = $tagihans->where('status', 'belum lunas')->count();

        return view('siswa.dashboard', compact(
            'siswa',
            'totalTunggakan',
            'sudahDibayar',
            'tagihanAktif',
            'belumLunas',
        ));
    }

    /**
     * Semua Tagihan Siswa
     */
    public function index()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect()->route('login')->withErrors(['error' => 'Data siswa tidak ditemukan.']);
        }

        $tagihans = Tagihan::where('siswa_nis', $siswa->nis)
            ->where('status', 'belum lunas')
            ->with('jenisPembayaran')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by status
        $tagihanBelumLunas = $tagihans->where('status', 'belum lunas');
        $tagihanLunas = $tagihans->where('status', 'lunas');

        return view('siswa.tagihan.index', compact('siswa', 'tagihanBelumLunas', 'tagihanLunas'));
    }

    /**
     * Tagihan Belum Lunas
     */
    public function semuaTagihan()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect()->route('login')->withErrors(['error' => 'Data siswa tidak ditemukan.']);
        }

        $tagihans = Tagihan::where('siswa_nis', $siswa->nis)
            ->with('jenisPembayaran')
            ->orderBy('jatuh_tempo', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('siswa.tagihan.belum-lunas', compact('siswa', 'tagihans'));
    }

    /**
     * History Pembayaran
     */
    public function history()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect()->route('login')->withErrors(['error' => 'Data siswa tidak ditemukan.']);
        }

        $transaksi = Transaksi::where('siswa_nis', $siswa->nis)
            ->with(['tagihan.jenisPembayaran'])
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('siswa.tagihan.history', compact('siswa', 'transaksi'));
    }

    /**
     * Detail Tagihan
     */
    public function show(Tagihan $tagihan)
    {
        $siswa = Auth::user()->siswa;

        // Pastikan tagihan milik siswa yang login
        if ($tagihan->siswa_nis != $siswa->nis) {
            abort(403, 'Unauthorized');
        }

        $tagihan->load('jenisPembayaran', 'transaksi');

        return view('siswa.tagihan.show', compact('siswa', 'tagihan'));
    }

    /**
     * Kwitansi
     */
    public function kwitansi(Transaksi $transaksi)
    {
        $siswa = Auth::user()->siswa;

        // Pastikan transaksi milik siswa yang login
        if ($transaksi->siswa_nis != $siswa->nis) {
            abort(403, 'Unauthorized');
        }

        $transaksi->load(['siswa.kelas', 'tagihan.jenisPembayaran']);

        return view('siswa.tagihan.kwitansi', compact('transaksi'));
    }
}
