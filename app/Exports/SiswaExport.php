<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class SiswaExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    protected $status;
    protected $kelasId;
    protected $jurusanId;

    public function __construct($status = null, $kelasId = null, $jurusanId = null)
    {
        $this->status = $status;
        $this->kelasId = $kelasId;
        $this->jurusanId = $jurusanId;
    }

    /**
     * Ambil data siswa
     */
    public function collection()
    {
        $query = Siswa::with(['kelas', 'jurusan']);

        // Filter status
        if ($this->status && $this->status !== 'semua') {
            $query->where('status', $this->status);
        }

        // Filter kelas
        if ($this->kelasId) {
            $query->where('kelas_id', $this->kelasId);
        }

        // Filter jurusan
        if ($this->jurusanId) {
            $query->where('jurusan_id', $this->jurusanId);
        }

        return $query->orderBy('nama')->get();
    }

    /**
     * Header Excel
     */
    public function headings(): array
    {
        return [
            'NIS',
            'Nama',
            'Kelas',
            'Jurusan',
            'Tahun Masuk',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Alamat',
            'Wali', 
            'Kontak',
            'Status',
        ];
    }

    /**
     * Mapping data per baris
     */
    public function map($siswa): array
    {
        return [
            $siswa->nis,
            $siswa->nama,
            $siswa->kelas->kelas ?? '-',
            $siswa->jurusan->nama ?? '-',
            $siswa->tahun_masuk ?? '-',
            $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            $siswa->tgl_lahir
                ? Carbon::parse($siswa->tanggal_lahir)->format('d-m-Y')
                : '-',
            $siswa->alamat ?? '-',
            $siswa->wali,
            $siswa->kontak ?? '-',
            ucfirst(str_replace('_', ' ', $siswa->status)),
        ];
    }

    /**
     * Styling Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lebar kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15, // NIS
            'B' => 30, // Nama
            'C' => 10, // Kelas
            'D' => 15, // Jurusan
            'E' => 15, // Tahun Masuk
            'F' => 15, // Jenis Kelamin
            'G' => 15, // Tanggal Lahir
            'H' => 40, // Alamat
            'I' => 15, // Kontak
            'J' => 40, // Wali
            'K' => 15, // Status
        ];
    }

    /**
     * Nama sheet
     */
    public function title(): string
    {
        return 'Data Siswa';
    }
}
