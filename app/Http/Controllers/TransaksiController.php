<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Siswa;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Display list of transactions
     */
    public function index(Request $request)
    {
        $query = Transaksi::with(['siswa.kelas', 'tagihan.jenisTagihan']);

        // Filter by date range
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }

        // Filter by siswa
        if ($request->filled('siswa_nis')) {
            $query->where('siswa_nis', $request->siswa_nis);
        }

        // Filter by metode
        if ($request->filled('metode')) {
            $query->where('metode', $request->metode);
        }

        // Search by keyword (NIS or nama siswa)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('siswa_nis', 'like', "%{$keyword}%")
                  ->orWhereHas('siswa', function($q) use ($keyword) {
                      $q->where('nama', 'like', "%{$keyword}%");
                  });
        }

        // Order by latest first
        $transaksi = $query->orderBy('tanggal', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

        // Calculate statistics untuk periode yang difilter
        $queryStats = clone $query;
        $totalTransaksi = $queryStats->count();
        $totalNominal = $queryStats->sum('jumlah_bayar');
        
        // Get list siswa for filter dropdown
        $siswaList = Siswa::whereNotNull('kelas_id')
                         ->orderBy('nama')
                         ->get();

        return view('admin.pembayaran.riwayat_transaksi.index', compact(
            'transaksi',
            'totalTransaksi',
            'totalNominal',
            'siswaList'
        ));
    }

    /**
     * Show transaction detail
     */
    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['siswa.kelas', 'tagihan.jenisTagihan']);
        
        return view('admin.pembayaran.riwayat_transaksi.show', compact('transaksi'));
    }

    /**
     * Print kwitansi
     */
    public function printKwitansi(Transaksi $transaksi)
    {
        $transaksi->load(['siswa.kelas', 'tagihan.jenisTagihan']);
        
        return view('admin.pembayaran.riwayat_transaksi.kwitansi-print', compact('transaksi'));
    }
}