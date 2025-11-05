<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanTunggakanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $tunggakanPerSiswa;

    public function __construct($tunggakanPerSiswa)
    {
        $this->tunggakanPerSiswa = $tunggakanPerSiswa;
    }

    public function collection()
    {
        return $this->tunggakanPerSiswa;
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Jumlah Tagihan',
            'Total Tunggakan',
        ];
    }

    public function map($data): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $data['siswa']->nis,
            $data['siswa']->nama,
            $data['siswa']->kelas->kelas ?? '-',
            $data['jumlah_tagihan'],
            $data['total_tunggakan'],
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
        return 'Laporan Tunggakan';
    }
}