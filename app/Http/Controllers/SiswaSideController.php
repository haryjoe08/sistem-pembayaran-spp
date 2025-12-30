<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaSideController extends Controller
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

        // Hitung data untuk dashboard
        $tagihans = Tagihan::where('siswa_nis', $siswa->nis)->get();

        $totalTagihan = $tagihans->sum('total_tagihan');
        $sudahDibayar = $tagihans->sum('sudah_dibayar');
        $totalTunggakan = $totalTagihan - $sudahDibayar;


        // Tagihan terbaru (5 teratas)
        $tagihanTerbaru = Tagihan::where('siswa_nis', $siswa->nis)
            ->with('jenisTagihan')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Tagihan yang mendekati jatuh tempo (7 hari)
        $tagihanMendekatJatuhTempo = Tagihan::where('siswa_nis', $siswa->nis)
            ->where('status', '!=', 'lunas')
            ->whereNotNull('jatuh_tempo')
            ->where('jatuh_tempo', '<=', now()->addDays(7))
            ->where('jatuh_tempo', '>', now())
            ->count();

        // Pembayaran terbaru
        $pembayaranTerbaru = Transaksi::where('siswa_nis', $siswa->nis)
            ->latest()
            ->first();

        return view('siswa.dashboard', compact(
            'siswa',
            'totalTunggakan',
            'sudahDibayar',
            'tagihanTerbaru',
            'tagihanMendekatJatuhTempo',
            'pembayaranTerbaru'
        ));
    }

    /**
     * Halaman Semua Tagihan
     */
    public function tagihan()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect()->route('login')->withErrors(['error' => 'Data siswa tidak ditemukan.']);
        }

        $tagihans = Tagihan::where('siswa_nis', $siswa->nis)
            ->where('status', 'belum lunas')
            ->with('jenisTagihan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Summary
        $totalTagihan = $tagihans->sum('total_tagihan');
        $totalDibayar = $tagihans->sum('sudah_dibayar');
        $totalTunggakan = $totalTagihan - $totalDibayar;
        $jumlahLunas = $tagihans->where('status', 'lunas')->count();
        $jumlahBelumLunas = $tagihans->where('status', 'belum lunas')->count();

        return view('siswa.tagihan.index', compact(
            'siswa',
            'tagihans',
            'totalTagihan',
            'totalDibayar',
            'totalTunggakan',
            'jumlahLunas',
            'jumlahBelumLunas'
        ));
    }

    /**
     * Halaman Tagihan Belum Lunas
     */
    public function semuaTagihan()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect()->route('login')->withErrors(['error' => 'Data siswa tidak ditemukan.']);
        }

        $tagihans = Tagihan::where('siswa_nis', $siswa->nis)
            ->with('jenisTagihan')
            ->orderBy('jatuh_tempo', 'asc')
            ->paginate(10);

        $totalTunggakan = $tagihans->sum(function ($t) {
            return $t->total_tagihan - $t->sudah_dibayar;
        });

        return view('siswa.tagihan.semua-tagihan', compact(
            'siswa',
            'tagihans',
            'totalTunggakan'
        ));
    }

    /**
     * History Pembayaran
     */
    public function historyPembayaran()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect()->route('login')->withErrors(['error' => 'Data siswa tidak ditemukan.']);
        }

        $transaksi = Transaksi::where('siswa_nis', $siswa->nis)
            ->with('tagihan.jenisTagihan')
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        $totalPembayaran = $transaksi->sum('jumlah_bayar');

        return view('siswa.tagihan.history', compact(
            'siswa',
            'transaksi',
            'totalPembayaran'
        ));
    }

    /**
     * Lihat Kwitansi
     */
    public function kwitansi($transaksiId)
    {
        $siswa = Auth::user()->siswa;

        $transaksi = Transaksi::where('siswa_nis', $siswa->nis)
            ->where('id', $transaksiId)
            ->with(['siswa.kelas', 'tagihan.jenisTagihan'])
            ->firstOrFail();

        return view('siswa.tagihan.kwitansi', compact('transaksi'));
    }

    /**
     * Profil Siswa
     */
    public function profil()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $nama = \App\Models\Admin::where('login_id', $user->id)->value('nama');
        } else {
            $nama = \App\Models\Siswa::where('login_id', $user->id)->value('nama');
        }

        return view('siswa.profile', compact('user', 'nama'));

    }
}
