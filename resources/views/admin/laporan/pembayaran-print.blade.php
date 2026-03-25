<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembayaran - Print</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            padding: 1cm;
        }

        /* KOP SURAT */
        .kop-surat table {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 5px;
        }

        .kop-surat img {
            width: 160px;
            height: auto;
        }

        .kop-surat td {
            vertical-align: middle;
        }

        .kop-surat .center-content {
            text-align: center;
        }

        .kop-surat h4 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-surat h5 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-surat h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-surat p {
            margin: 0;
            font-size: 11px;
        }

        .border-bottom {
            border-bottom: 1px solid #000;
            margin-bottom: 20px;
        }

        /* JUDUL */
        .title-section {
            text-align: center;
            margin: 20px 0 30px 0;
        }

        .title-section h3 {
            margin: 0;
            font-weight: bold;
            text-decoration: underline;
            font-size: 16px;
        }

        .title-section p {
            margin: 5px 0 0 0;
            font-size: 11px;
        }

        /* RINGKASAN */
        .summary-section {
            margin-bottom: 20px;
        }

        .summary-section table {
            width: 100%;
            font-size: 11px;
        }

        .summary-section td {
            padding: 2px 0;
        }

        /* TABLE */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }

        table.data-table th {
            background-color: #f8f9fa;
            border: 1px solid #000;
            padding: 6px 4px;
            font-weight: bold;
            text-align: left;
        }

        table.data-table td {
            border: 1px solid #000;
            padding: 4px;
        }

        /* GRAND TOTAL ROW - Best Practice */
        table.data-table tr.grand-total td {
            background-color: #f8f9fa !important;
            border: 1px solid #000;
            padding: 6px 4px;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .text-success {
            color: #198754;
        }

        .fw-bold {
            font-weight: bold;
        }

        /* TTD */
        .signature-section {
            margin-top: 40px;
        }

        .signature-section table {
            width: 100%;
            font-size: 11px;
        }

        .signature-section td {
            vertical-align: top;
        }

        .signature-space {
            height: 60px;
        }

        /* PRINT SETTINGS - Critical Fixes */
        @page {
            size: A4 portrait;
            margin: 1.5cm 1cm 1cm 1cm;
        }

        @media print {
            body {
                padding: 0;
            }

            .text-success {
                color: #198754 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            table.data-table th {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Cegah pemisahan baris data */
            tr {
                page-break-inside: avoid;
            }

            /* Pastikan 2 baris terakhir (data + total) tidak terpisah */
            table.data-table tr:nth-last-child(2),
            table.data-table tr.grand-total {
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            /* Hindari total muncul di halaman baru sendirian */
            table.data-table tr.grand-total {
                page-break-before: avoid;
            }
        }
    </style>
</head>

<body>
    <!-- KOP SURAT -->
    <div class="kop-surat">
        <table>
            <tr>
                <td style="width: 100px; text-align: center;">
                    <img src="{{ asset('assets/img/logo-nu.png') }}" alt="Logo">
                </td>
                <td class="center-content">
                    <h4>LEMBAGA PENDIDIKAN MA'ARIF NU</h4>
                    <h5>MA NAHDLATUL ULAMA BATANG</h5>
                    <h4>BADAN HUKUM PERKUMPULAN NAHDATUL ULAMA</h4>
                    <p>SK MENTERI HUKUM DAN HAK ASASI MANUSIA NO. AHU-119.AH.01.08 TAHUN 2013</p>
                    <h2>NPSN : 20364933 NSS : 131233250006</h2>
                    <p>Alamat :JI. Jenderal Ahmad Yani no. 114 Kauman Batang 51215</p>
                    <p>email: manubatang@yahoo.co.id Telp. (0285) 392663</p>
                </td>
                <td style="width: 100px;"></td>
            </tr>
        </table>
        <div class="border-bottom"></div>
    </div>

    <!-- JUDUL LAPORAN -->
    <div class="title-section">
        <h3>LAPORAN TRANSAKSI PEMBAYARAN</h3>
        <p>Periode: {{ \Carbon\Carbon::parse($dariTanggal)->isoFormat('D MMMM Y') }} s/d {{ \Carbon\Carbon::parse($sampaiTanggal)->isoFormat('D MMMM Y') }}</p>
        @if(request('kelas_id') || request('jenis_tagihan_id') || request('metode'))
        <p>
            @if(request('kelas_id'))
            Kelas: {{ $kelasList->find(request('kelas_id'))->kelas ?? '-' }}
            @endif
            @if(request('kelas_id') && (request('jenis_tagihan_id') || request('metode'))) | @endif
            @if(request('jenis_tagihan_id'))
            Jenis: {{ $jenisPembayaranList->find(request('jenis_tagihan_id'))->nama ?? '-' }}
            @endif
            @if(request('jenis_tagihan_id') && request('metode')) | @endif
            @if(request('metode'))
            Metode: {{ strtoupper(request('metode')) }}
            @endif
        </p>
        @endif
    </div>

    <!-- RINGKASAN -->
    <div class="summary-section">
        <table>
            <tr>
                <td style="width: 30%;">Total Transaksi</td>
                <td style="width: 2%;">:</td>
                <td class="fw-bold">{{ number_format($totalTransaksi) }} Transaksi</td>
            </tr>
            <tr>
                <td>Total Nominal Pembayaran</td>
                <td>:</td>
                <td class="fw-bold text-success">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
            </tr>
            @if(count($groupByMetode) > 0)
            <tr>
                <td>Rincian Per Metode</td>
                <td>:</td>
                <td>
                    @php
                    $metodeNames = [
                    'cash' => 'Cash',
                    'cstore' => 'Indomaret / Alfamart',
                    'va' => 'VA',
                    'qris' => 'QRIS',
                    ];
                    @endphp

                    @foreach($groupByMetode as $metode => $data)
                    <span class="fw-bold">
                        {{ $metodeNames[$metode] ?? strtoupper($metode) }}:
                    </span>
                    {{ $data['jumlah'] }} transaksi
                    (Rp {{ number_format($data['total'], 0, ',', '.') }})
                    @if(!$loop->last) | @endif
                    @endforeach
                </td>
            </tr>
            @endif
        </table>
    </div>

    <!-- TABEL DATA - DIPERBAIKI -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 70px;">Tanggal</th>
                <th style="width: 60px;">NIS</th>
                <th>Nama Siswa</th>
                <th style="width: 80px;">Kelas</th>
                <th style="width: 120px;">Jenis Tagihan</th>
                <th class="text-end" style="width: 100px;">Jumlah</th>
                <th class="text-center" style="width: 50px;">Metode</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $t)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $t->tanggal->format('d/m/Y') }}</td>
                <td>{{ $t->siswa_nis }}</td>
                <td>{{ $t->siswa->nama }}</td>
                <td>{{ $t->siswa->kelas->kelas ?? '-' }}</td>
                <td>
                    {{ strtoupper(($t->tagihan->jenisTagihan->nama ?? '-') . ' ' . ($t->tagihan->periode ?? '')) }}
                </td>

                <td class="text-end fw-bold text-success">
                    Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}
                </td>
                <td class="text-center">
                    {{ $t->metode === 'cstore'
                        ? 'Indomaret / Alfamart'
                        : strtoupper($t->metode) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px;">
                    Tidak ada data transaksi
                </td>
            </tr>
            @endforelse

            {{-- GRAND TOTAL - HANYA DI AKHIR TABEL, BUKAN DI SETIAP HALAMAN --}}
            @if($transaksi->count() > 0)
            <tr class="grand-total">
                <td colspan="6" class="text-end">TOTAL:</td>
                <td class="text-end text-success">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                <td></td>
            </tr>
            @endif
        </tbody>
        {{-- <tfoot> DIHAPUS TOTAL - PENYEBAB MASALAH --}}
    </table>

    <!-- TTD -->
    <div class="signature-section">
        <table>
            <tr>
                <td style="width: 50%;">
                    <p>Mengetahui,</p>
                    <p>Kepala Sekolah,</p>
                    <div class="signature-space"></div>
                    <p class="fw-bold" style="text-decoration: underline;">Sarwani Spd.I</p>
                    <p>NIP. 197703172005011006</p>
                </td>
                <td style="width: 50%; text-align: center;">
                    <p>Batang, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                    <p>Tata Usaha,</p>
                    <div class="signature-space"></div>
                    <p class="fw-bold" style="text-decoration: underline;">Wiroso</p>
                </td>
            </tr>
        </table>
    </div>

    <script>
        // Auto print dengan penundaan untuk memastikan rendering selesai
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>