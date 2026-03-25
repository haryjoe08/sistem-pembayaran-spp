<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPembayaranExport;
use App\Exports\LaporanTunggakanExport;
use App\Exports\LaporanPerKelasExport;
use App\Models\JenisPembayaran;
use App\Models\Transaksi;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\JenisTagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{

    public function pembayaran(Request $request)
    {
        $query = Transaksi::with(['siswa.kelas', 'tagihan.jenisTagihan']);

        // Default bulan ini jika tidak ada filter
        $dariTanggal = $request->dari_tanggal ?? now()->startOfMonth()->format('Y-m-d');
        $sampaiTanggal = $request->sampai_tanggal ?? now()->endOfMonth()->format('Y-m-d');

        $query->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal]);

        // Filter by keyword (NIS atau Nama Siswa)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->whereHas('siswa', function ($q) use ($keyword) {
                $q->where('nis', 'like', "%{$keyword}%")
                    ->orWhere('nama', 'like', "%{$keyword}%");
            });
        }

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Filter by jenis tagihan
        if ($request->filled('jenis_tagihan_id')) {
            $query->whereHas('tagihan', function ($q) use ($request) {
                $q->where('jenis_tagihan_id', $request->jenis_tagihan_id);
            });
        }

        // Filter by metode
        if ($request->filled('metode')) {
            $query->where('metode', $request->metode);
        }

        // Clone query untuk summary sebelum pagination
        $summaryQuery = clone $query;

        // Summary - hitung dari semua data tanpa pagination
        $allTransaksi = $summaryQuery->get();
        $totalTransaksi = $allTransaksi->count();
        $totalNominal = $allTransaksi->sum('jumlah_bayar');
        $groupByMetode = $allTransaksi->groupBy('metode')->map(function ($items) {
            return [
                'jumlah' => $items->count(),
                'total' => $items->sum('jumlah_bayar')
            ];
        });

        // Pagination - 20 data per halaman
        $transaksi = $query->orderBy('tanggal', 'desc')->paginate(20)->withQueryString();

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
    public function exportPembayaran(Request $request)
    {
        $query = Transaksi::with(['siswa.kelas', 'tagihan.jenisTagihan']);

        $dariTanggal = $request->dari_tanggal ?? now()->startOfMonth()->format('Y-m-d');
        $sampaiTanggal = $request->sampai_tanggal ?? now()->endOfMonth()->format('Y-m-d');

        $query->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal]);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->whereHas('siswa', function ($q) use ($keyword) {
                $q->where('nis', 'like', "%{$keyword}%")
                    ->orWhere('nama', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        if ($request->filled('jenis_tagihan_id')) {
            $query->whereHas('tagihan', function ($q) use ($request) {
                $q->where('jenis_tagihan_id', $request->jenis_tagihan_id);
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
    public function printPembayaran(Request $request)
    {
        $query = Transaksi::with(['siswa.kelas', 'tagihan.jenisTagihan']);

        // Filter tanggal
        $dariTanggal = $request->dari_tanggal ?? now()->startOfMonth()->format('Y-m-d');
        $sampaiTanggal = $request->sampai_tanggal ?? now()->endOfMonth()->format('Y-m-d');
        $query->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal]);

        // Filter by keyword (NIS atau Nama Siswa)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->whereHas('siswa', function ($q) use ($keyword) {
                $q->where('nis', 'like', "%{$keyword}%")
                    ->orWhere('nama', 'like', "%{$keyword}%");
            });
        }

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Filter by jenis tagihan
        if ($request->filled('jenis_tagihan_id')) {
            $query->whereHas('tagihan', function ($q) use ($request) {
                $q->where('jenis_tagihan_id', $request->jenis_tagihan_id);
            });
        }

        // Filter by metode
        if ($request->filled('metode')) {
            $query->where('metode', $request->metode);
        }

        // AMBIL SEMUA DATA TANPA PAGINATION
        $transaksi = $query->orderBy('tanggal', 'desc')->get();

        // Summary
        $totalTransaksi = $transaksi->count();
        $totalNominal = $transaksi->sum('jumlah_bayar');
        $groupByMetode = $transaksi->groupBy('metode')->map(function ($items) {
            return [
                'jumlah' => $items->count(),
                'total' => $items->sum('jumlah_bayar')
            ];
        });

        // Data untuk filter
        $kelasList = Kelas::orderBy('kelas')->get();
        $jenisPembayaranList = JenisPembayaran::orderBy('nama')->get();

        return view('admin.laporan.pembayaran-print', compact(
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

    public function tunggakan(Request $request)
    {
        // Build subquery untuk filter siswa jika ada
        $siswaQuery = Siswa::query();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $siswaQuery->where(function ($q) use ($keyword) {
                $q->where('nis', 'like', "%{$keyword}%")
                    ->orWhere('nama', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('kelas_id')) {
            $siswaQuery->where('kelas_id', $request->kelas_id);
        }

        $filteredNis = $siswaQuery->pluck('nis')->toArray();

        // QUERY DASAR dengan JOIN
        $query = Tagihan::query()
            ->select(
                'tagihan.siswa_nis',
                'siswa.kelas_id',
                'kelas.kelas as nama_kelas',
                DB::raw('COUNT(*) as jumlah_tagihan'),
                DB::raw('SUM(tagihan.total_tagihan - tagihan.sudah_dibayar) as total_tunggakan')
            )
            ->join('siswa', 'tagihan.siswa_nis', '=', 'siswa.nis')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->where('tagihan.status', 'belum lunas')
            ->groupBy('tagihan.siswa_nis', 'siswa.kelas_id', 'kelas.kelas')
            ->havingRaw('SUM(tagihan.total_tagihan - tagihan.sudah_dibayar) > 0')
            ->with(['siswa.kelas']);

        // Apply filter NIS jika ada
        if ($request->filled('keyword') || $request->filled('kelas_id')) {
            $query->whereIn('tagihan.siswa_nis', $filteredNis);
        }

        // FILTER JENIS TAGIHAN
        if ($request->filled('jenis_tagihan_id')) {
            $query->where('tagihan.jenis_tagihan_id', $request->jenis_tagihan_id);
        }

        // SUMMARY
        $summaryQuery = clone $query;
        $totalSiswaMenunggak = $summaryQuery->get()->count();
        $totalTunggakan = $summaryQuery->get()->sum('total_tunggakan');
        $totalTagihan = $summaryQuery->sum('jumlah_tagihan');

        // PAGINATION dengan SORTING: Kelas ASC, lalu Total Tunggakan DESC
        $tunggakanPerSiswa = $query
            ->orderBy('kelas.kelas', 'asc')
            ->orderByDesc('total_tunggakan')
            ->paginate(20)
            ->withQueryString();

        // MASTER DATA
        $kelasList = Kelas::orderBy('kelas', 'asc')->get();
        $jenisPembayaranList = JenisPembayaran::orderBy('nama')->get();

        return view('admin.laporan.tunggakan', compact(
            'tunggakanPerSiswa',
            'totalSiswaMenunggak',
            'totalTunggakan',
            'kelasList',
            'jenisPembayaranList',
            'totalTagihan'
        ));
    }


    public function exportTunggakan(Request $request)
    {
        // Build subquery untuk filter siswa
        $siswaQuery = Siswa::query();

        // Filter by keyword (NIS atau Nama Siswa)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $siswaQuery->where(function ($q) use ($keyword) {
                $q->where('nis', 'like', "%{$keyword}%")
                    ->orWhere('nama', 'like', "%{$keyword}%");
            });
        }

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $siswaQuery->where('kelas_id', $request->kelas_id);
        }

        // Ambil NIS yang sesuai filter
        $filteredNis = $siswaQuery->pluck('nis')->toArray();

        // Query tunggakan
        $query = Tagihan::query()
            ->select(
                'siswa_nis',
                DB::raw('COUNT(*) as jumlah_tagihan'),
                DB::raw('SUM(total_tagihan - sudah_dibayar) as total_tunggakan')
            )
            ->where('status', 'belum lunas')
            ->groupBy('siswa_nis')
            ->havingRaw('SUM(total_tagihan - sudah_dibayar) > 0')
            ->with(['siswa.kelas']);

        // Apply filter NIS jika ada filter keyword atau kelas
        if ($request->filled('keyword') || $request->filled('kelas_id')) {
            $query->whereIn('siswa_nis', $filteredNis);
        }

        // Filter by jenis tagihan
        if ($request->filled('jenis_tagihan_id')) {
            $query->where('jenis_tagihan_id', $request->jenis_tagihan_id);
        }

        $tunggakanPerSiswa = $query->orderByDesc('total_tunggakan')->get();

        $filename = 'Laporan-Tunggakan-' . date('d-m-Y') . '.xlsx';

        return Excel::download(new \App\Exports\LaporanTunggakanExport($tunggakanPerSiswa), $filename);
    }

    public function printTunggakan(Request $request)
    {
        // Build subquery untuk filter siswa
        $siswaQuery = Siswa::query();

        // Filter by keyword (NIS atau Nama Siswa)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $siswaQuery->where(function ($q) use ($keyword) {
                $q->where('nis', 'like', "%{$keyword}%")
                    ->orWhere('nama', 'like', "%{$keyword}%");
            });
        }

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $siswaQuery->where('kelas_id', $request->kelas_id);
        }

        // Ambil NIS yang sesuai filter
        $filteredNis = $siswaQuery->pluck('nis')->toArray();

        // Query tunggakan
        $query = Tagihan::query()
            ->select(
                'siswa_nis',
                DB::raw('COUNT(*) as jumlah_tagihan'),
                DB::raw('SUM(total_tagihan - sudah_dibayar) as total_tunggakan')
            )
            ->where('status', 'belum lunas')
            ->groupBy('siswa_nis')
            ->havingRaw('SUM(total_tagihan - sudah_dibayar) > 0')
            ->with(['siswa.kelas']);

        // Apply filter NIS jika ada filter keyword atau kelas
        if ($request->filled('keyword') || $request->filled('kelas_id')) {
            $query->whereIn('siswa_nis', $filteredNis);
        }

        // Filter by jenis tagihan
        if ($request->filled('jenis_tagihan_id')) {
            $query->where('jenis_tagihan_id', $request->jenis_tagihan_id);
        }

        // AMBIL SEMUA DATA TANPA PAGINATION
        $tunggakanPerSiswa = $query->orderByDesc('total_tunggakan')->get();

        // SUMMARY
        $totalSiswaMenunggak = $tunggakanPerSiswa->count();
        $totalTunggakan = $tunggakanPerSiswa->sum('total_tunggakan');

        $kelasList = Kelas::orderBy('kelas')->get();
        $jenisPembayaranList = JenisPembayaran::orderBy('nama')->get();

        return view('admin.laporan.tunggakan-print', compact(
            'tunggakanPerSiswa',
            'totalSiswaMenunggak',
            'totalTunggakan',
            'kelasList',
            'jenisPembayaranList'
        ));
    }
}
