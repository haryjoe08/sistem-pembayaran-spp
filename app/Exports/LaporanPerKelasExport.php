<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPerKelasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $laporanKelas;

    public function __construct($laporanKelas)
    {
        $this->laporanKelas = $laporanKelas;
    }

    public function collection()
    {
        return $this->laporanKelas;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kelas',
            'Jumlah Siswa',
            'Total Tagihan',
            'Tagihan Lunas',
            'Tagihan Belum Lunas',
            'Total Nominal Tagihan',
            'Total Sudah Dibayar',
            'Total Tunggakan',
        ];
    }

    public function map($laporan): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $laporan['kelas']->kelas,
            $laporan['jumlah_siswa'],
            $laporan['total_tagihan'],
            $laporan['tagihan_lunas'],
            $laporan['tagihan_belum_lunas'],
            $laporan['total_nominal_tagihan'],
            $laporan['total_sudah_dibayar'],
            $laporan['total_tunggakan'],
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
        return 'Laporan Per Kelas';
    }
}