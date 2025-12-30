<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Transaksi;
use App\Models\Kelas;

use Illuminate\Support\Facades\DB;

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

    // ADMIN DASHBOARD
    public function adminDashboard()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect('/siswa/dashboard');
        }

        $jumlahSiswa = Siswa::count();
        $jumlahLunas = Tagihan::where('status', 'lunas')->count();
        $jumlahBelumLunas = Tagihan::where('status', 'belum lunas')->count();
        $totalTagihan = Tagihan::sum('total_tagihan');
        $totalDibayar = Tagihan::sum('sudah_dibayar');
        $totalTunggakan = $totalTagihan - $totalDibayar;
             // Grafik Pembayaran Per Bulan (6 bulan terakhir)
        $grafikPembayaran = Transaksi::selectRaw('MONTH(tanggal) as bulan, YEAR(tanggal) as tahun, SUM(jumlah_bayar) as total')
                                    ->where('tanggal', '>=', now()->subMonths(6))
                                    ->groupBy('tahun', 'bulan')
                                    ->orderBy('tahun', 'asc')
                                    ->orderBy('bulan', 'asc')
                                    ->get();

        // Top 5 Jenis Pembayaran
        $topJenisPembayaran = Transaksi::select('jenis_tagihan.nama', DB::raw('COUNT(*) as jumlah_transaksi'), DB::raw('SUM(transaksi.jumlah_bayar) as total_nominal'))
                                      ->join('tagihan', 'transaksi.tagihan_id', '=', 'tagihan.id')
                                      ->join('jenis_tagihan', 'tagihan.jenis_tagihan_id', '=', 'jenis_tagihan.id')
                                      ->groupBy('jenis_tagihan.id', 'jenis_tagihan.nama')
                                      ->orderBy('total_nominal', 'desc')
                                      ->limit(5)
                                      ->get();

        return view('admin.dashboard', compact('jumlahSiswa', 'jumlahLunas', 'jumlahBelumLunas', 'totalTagihan', 'totalDibayar', 'totalTunggakan', 'topJenisPembayaran', 'grafikPembayaran'));
    }

    // SISWA DASHBOARD
    public function siswaDashboard()
    {
        if (Auth::user()->role !== 'siswa') {
            return redirect('/admin/dashboard');
        }

        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect('/login')->withErrors(['error' => 'Data siswa tidak ditemukan.']);
        }

        $tagihans = Tagihan::where('siswa_nis', $siswa->nis)->get();

        $totalTagihan = $tagihans->sum('total_tagihan');
        $sudahDibayar = $tagihans->sum('sudah_dibayar');
        $totalTunggakan = $totalTagihan - $sudahDibayar;

        $tagihanAktif = $tagihans->where('status', '!=', 'lunas')->count();
        $belumLunas = $tagihans->where('status', 'belum lunas')->count();

        $tagihanTerbaru = Tagihan::where('siswa_nis', $siswa->nis)
            ->with('jenisTagihan')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $tagihanMendekatJatuhTempo = Tagihan::where('siswa_nis', $siswa->nis)
            ->where('status', '!=', 'lunas')
            ->count();

        $pembayaranTerbaru = null;

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