<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPembayaranExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $transaksi;
    protected $totalNominal;

    public function __construct($transaksi, $totalNominal)
    {
        $this->transaksi = $transaksi;
        $this->totalNominal = $totalNominal;
    }

    public function collection()
    {
        return $this->transaksi;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Jenis Pembayaran',
            'Jumlah',
            'Metode',
        ];
    }

    public function map($transaksi): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $transaksi->tanggal->format('d/m/Y H:i'),
            $transaksi->siswa_nis,
            $transaksi->siswa->nama,
            $transaksi->siswa->kelas->kelas ?? '-',
            $transaksi->tagihan->jenisPembayaran->nama ?? '-',
            $transaksi->jumlah_bayar,
            strtoupper($transaksi->metode),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Pembayaran';
    }
}