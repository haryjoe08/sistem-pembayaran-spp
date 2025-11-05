<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Siswa;
use App\Models\Tagihan;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role === 'siswa') {
                return redirect()->intended('/siswa/dashboard');
            }

            return redirect('/'); // fallback
        }

        return back()->withErrors(['login_error' => 'Username atau password salah']);
    }

    // dashboard masing-masing
    public function adminDashboard()
    {
        $jumlahSiswa = Siswa::count();
        $jumlahLunas = Tagihan::where('status', 'Lunas')->count();
        $jumlahBelumLunas = Tagihan::where('status', 'Belum Lunas')->count();
        $totalTagihan   = Tagihan::sum('total_tagihan');
        $totalDibayar   = Tagihan::sum('sudah_dibayar');
        $totalTunggakan = $totalTagihan - $totalDibayar;

        if (Auth::user()->role !== 'admin') {
            return redirect('/siswa/dashboard');
        }

        return view('admin.dashboard', compact('jumlahSiswa', 'jumlahLunas', 'jumlahBelumLunas', 'totalTagihan', 'totalDibayar', 'totalTunggakan'));
    }

    public function siswaDashboard()
    {
        if (Auth::user()->role !== 'siswa') {
            return redirect('/admin/dashboard');
        }

        // Ambil data siswa yang login
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect('/login')->withErrors(['error' => 'Data siswa tidak ditemukan.']);
        }

        // Hitung data untuk dashboard
        $tagihans = Tagihan::where('siswa_nis', $siswa->nis)->get();

        $totalTagihan = $tagihans->sum('total_tagihan');
        $sudahDibayar = $tagihans->sum('sudah_dibayar');
        $totalTunggakan = $totalTagihan - $sudahDibayar;

        $tagihanAktif = $tagihans->where('status', '!=', 'lunas')->count();
        $belumLunas = $tagihans->where('status', 'belum lunas')->count();

        // Tagihan terbaru (5 teratas)
        $tagihanTerbaru = Tagihan::where('siswa_nis', $siswa->nis)
            ->with('jenisPembayaran')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Tagihan yang mendekati jatuh tempo (misal dalam 7 hari)
        // Asumsi ada kolom 'jatuh_tempo' di tabel tagihan
        $tagihanMendekatJatuhTempo = Tagihan::where('siswa_nis', $siswa->nis)
            ->where('status', '!=', 'lunas')
            ->count();

        // Pembayaran terbaru (jika ada tabel transaksi/pembayaran)
        // $pembayaranTerbaru = Transaksi::where('siswa_nis', $siswa->nis)
        //                               ->latest()
        //                               ->first();
        $pembayaranTerbaru = null; // sementara null dulu

        return view('siswa.dashboard', compact(
            'totalTunggakan',
            'sudahDibayar',
            'tagihanAktif',
            'belumLunas',
            'tagihanTerbaru',
            'tagihanMendekatJatuhTempo',
            'pembayaranTerbaru'
        ));
    }

    public function logout()
    {
        Session::flush();
        return redirect('/login');
    }
}
