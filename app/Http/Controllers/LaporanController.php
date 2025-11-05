<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPembayaranExport;
use App\Exports\LaporanTunggakanExport;
use App\Exports\LaporanPerKelasExport;

use App\Models\Transaksi;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\JenisPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Laporan Dashboard/Index
     */
    public function index()
    {
        // Summary Cards
        $totalSiswa = Siswa::count();
        $totalTagihanAktif = Tagihan::where('status', 'belum lunas')->count();
        $totalTunggakan = Tagihan::where('status', 'belum lunas')
                                ->sum(DB::raw('total_tagihan - sudah_dibayar'));
        $totalPembayaranBulanIni = Transaksi::whereMonth('tanggal', now()->month)
                                           ->whereYear('tanggal', now()->year)
                                           ->sum('jumlah_bayar');

        // Grafik Pembayaran Per Bulan (6 bulan terakhir)
        $grafikPembayaran = Transaksi::selectRaw('MONTH(tanggal) as bulan, YEAR(tanggal) as tahun, SUM(jumlah_bayar) as total')
                                    ->where('tanggal', '>=', now()->subMonths(6))
                                    ->groupBy('tahun', 'bulan')
                                    ->orderBy('tahun', 'asc')
                                    ->orderBy('bulan', 'asc')
                                    ->get();

        // Top 5 Jenis Pembayaran
        $topJenisPembayaran = Transaksi::select('jenis_pembayaran.nama', DB::raw('COUNT(*) as jumlah_transaksi'), DB::raw('SUM(transaksi.jumlah_bayar) as total_nominal'))
                                      ->join('tagihan', 'transaksi.tagihan_id', '=', 'tagihan.id')
                                      ->join('jenis_pembayaran', 'tagihan.jenis_pembayaran_id', '=', 'jenis_pembayaran.id')
                                      ->groupBy('jenis_pembayaran.id', 'jenis_pembayaran.nama')
                                      ->orderBy('total_nominal', 'desc')
                                      ->limit(5)
                                      ->get();

        return view('admin.laporan.index', compact(
            'totalSiswa',
            'totalTagihanAktif',
            'totalTunggakan',
            'totalPembayaranBulanIni',
            'grafikPembayaran',
            'topJenisPembayaran'
        ));
    }

    /**
     * Laporan Pembayaran
     */
    public function pembayaran(Request $request)
    {
        $query = Transaksi::with(['siswa.kelas', 'tagihan.jenisPembayaran']);

        // Default bulan ini jika tidak ada filter
        $dariTanggal = $request->dari_tanggal ?? now()->startOfMonth()->format('Y-m-d');
        $sampaiTanggal = $request->sampai_tanggal ?? now()->endOfMonth()->format('Y-m-d');

        $query->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal]);

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Filter by jenis pembayaran
        if ($request->filled('jenis_pembayaran_id')) {
            $query->whereHas('tagihan', function($q) use ($request) {
                $q->where('jenis_pembayaran_id', $request->jenis_pembayaran_id);
            });
        }

        // Filter by metode
        if ($request->filled('metode')) {
            $query->where('metode', $request->metode);
        }

        $transaksi = $query->orderBy('tanggal', 'desc')->get();

        // Summary
        $totalTransaksi = $transaksi->count();
        $totalNominal = $transaksi->sum('jumlah_bayar');
        $groupByMetode = $transaksi->groupBy('metode')->map(function($items) {
            return [
                'jumlah' => $items->count(),
                'total' => $items->sum('jumlah_bayar')
            ];
        });

        // Data untuk filter
        $kelasList = Kelas::orderBy('kelas')->get();
        $jenisPembayaranList = JenisPembayaran::orderBy('nama')->get();

        return view('admin.laporan.pembayaran', compact(
            'transaksi',
            'totalTransaksi',
            'totalNominal',
            'groupByMetode',
            'kelasList',
            'jenisPembayaranList',
            'dariTanggal',
            'sampaiTanggal'
        ));
    }

    /**
     * Laporan Tunggakan
     */
    public function tunggakan(Request $request)
    {
        $query = Tagihan::with(['siswa.kelas', 'jenisPembayaran'])
                       ->where('status', 'belum lunas');

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter by jenis pembayaran
        if ($request->filled('jenis_pembayaran_id')) {
            $query->where('jenis_pembayaran_id', $request->jenis_pembayaran_id);
        }

        $tagihan = $query->orderBy('created_at', 'desc')->get();

        // Calculate tunggakan per siswa
        $tunggakanPerSiswa = $tagihan->groupBy('siswa_nis')->map(function($items) {
            return [
                'siswa' => $items->first()->siswa,
                'jumlah_tagihan' => $items->count(),
                'total_tunggakan' => $items->sum(function($item) {
                    return $item->total_tagihan - $item->sudah_dibayar;
                })
            ];
        })->sortByDesc('total_tunggakan');

        // Summary
        $totalTagihanBelumLunas = $tagihan->count();
        $totalTunggakan = $tagihan->sum(function($item) {
            return $item->total_tagihan - $item->sudah_dibayar;
        });
        $totalSiswaMenunggak = $tunggakanPerSiswa->count();

        // Data untuk filter
        $kelasList = Kelas::orderBy('kelas')->get();
        $jenisPembayaranList = JenisPembayaran::orderBy('nama')->get();

        return view('admin.laporan.tunggakan', compact(
            'tunggakanPerSiswa',
            'totalTagihanBelumLunas',
            'totalTunggakan',
            'totalSiswaMenunggak',
            'kelasList',
            'jenisPembayaranList'
        ));
    }

    /**
     * Laporan Per Kelas
     */
    public function perKelas(Request $request)
    {
        $kelasList = Kelas::with(['siswa.tagihan.jenisPembayaran'])
                         ->orderBy('kelas')
                         ->get();

        $laporanKelas = $kelasList->map(function($kelas) {
            $siswas = $kelas->siswa;
            $tagihans = $siswas->flatMap->tagihan;

            return [
                'kelas' => $kelas,
                'jumlah_siswa' => $siswas->count(),
                'total_tagihan' => $tagihans->count(),
                'tagihan_lunas' => $tagihans->where('status', 'lunas')->count(),
                'tagihan_belum_lunas' => $tagihans->where('status', 'belum lunas')->count(),
                'total_nominal_tagihan' => $tagihans->sum('total_tagihan'),
                'total_sudah_dibayar' => $tagihans->sum('sudah_dibayar'),
                'total_tunggakan' => $tagihans->where('status', 'belum lunas')->sum(function($item) {
                    return $item->total_tagihan - $item->sudah_dibayar;
                })
            ];
        });

        return view('admin.laporan.per-kelas', compact('laporanKelas'));
    }

    /**
     * Export Laporan Pembayaran ke Excel
     */
    public function exportPembayaran(Request $request)
    {
        $query = Transaksi::with(['siswa.kelas', 'tagihan.jenisPembayaran']);

        $dariTanggal = $request->dari_tanggal ?? now()->startOfMonth()->format('Y-m-d');
        $sampaiTanggal = $request->sampai_tanggal ?? now()->endOfMonth()->format('Y-m-d');

        $query->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal]);

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        if ($request->filled('jenis_pembayaran_id')) {
            $query->whereHas('tagihan', function($q) use ($request) {
                $q->where('jenis_pembayaran_id', $request->jenis_pembayaran_id);
            });
        }

        if ($request->filled('metode')) {
            $query->where('metode', $request->metode);
        }

        $transaksi = $query->orderBy('tanggal', 'desc')->get();
        $totalNominal = $transaksi->sum('jumlah_bayar');

        $filename = 'Laporan-Pembayaran-' . date('d-m-Y') . '.xlsx';
        
        return Excel::download(new \App\Exports\LaporanPembayaranExport($transaksi, $totalNominal), $filename);
    }

    /**
     * Export Laporan Tunggakan ke Excel
     */
    public function exportTunggakan(Request $request)
    {
        $query = Tagihan::with(['siswa.kelas', 'jenisPembayaran'])
                       ->where('status', 'belum lunas');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('jenis_pembayaran_id')) {
            $query->where('jenis_pembayaran_id', $request->jenis_pembayaran_id);
        }

        $tagihan = $query->orderBy('created_at', 'desc')->get();

        $tunggakanPerSiswa = $tagihan->groupBy('siswa_nis')->map(function($items) {
            return [
                'siswa' => $items->first()->siswa,
                'jumlah_tagihan' => $items->count(),
                'total_tunggakan' => $items->sum(function($item) {
                    return $item->total_tagihan - $item->sudah_dibayar;
                })
            ];
        })->sortByDesc('total_tunggakan');

        $filename = 'Laporan-Tunggakan-' . date('d-m-Y') . '.xlsx';
        
        return Excel::download(new \App\Exports\LaporanTunggakanExport($tunggakanPerSiswa), $filename);
    }

    /**
     * Export Laporan Per Kelas ke Excel
     */
    public function exportPerKelas(Request $request)
    {
        $kelasList = Kelas::with(['siswa.tagihan.jenisPembayaran'])
                         ->orderBy('kelas')
                         ->get();

        $laporanKelas = $kelasList->map(function($kelas) {
            $siswas = $kelas->siswa;
            $tagihans = $siswas->flatMap->tagihan;

            return [
                'kelas' => $kelas,
                'jumlah_siswa' => $siswas->count(),
                'total_tagihan' => $tagihans->count(),
                'tagihan_lunas' => $tagihans->where('status', 'lunas')->count(),
                'tagihan_belum_lunas' => $tagihans->where('status', 'belum lunas')->count(),
                'total_nominal_tagihan' => $tagihans->sum('total_tagihan'),
                'total_sudah_dibayar' => $tagihans->sum('sudah_dibayar'),
                'total_tunggakan' => $tagihans->where('status', 'belum lunas')->sum(function($item) {
                    return $item->total_tagihan - $item->sudah_dibayar;
                })
            ];
        });

        $filename = 'Laporan-Per-Kelas-' . date('d-m-Y') . '.xlsx';
        
        return Excel::download(new \App\Exports\LaporanPerKelasExport($laporanKelas), $filename);
    }
}