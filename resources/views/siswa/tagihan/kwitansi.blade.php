<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - #{{ str_pad($transaksi->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }

        .kwitansi-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .kwitansi-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: 5px solid #5568d3;
        }

        .school-logo {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .school-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .school-address {
            font-size: 14px;
            opacity: 0.9;
        }

        .kwitansi-title {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-bottom: 2px solid #dee2e6;
        }

        .kwitansi-title h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 5px;
        }

        .kwitansi-number {
            font-size: 14px;
            color: #666;
        }

        .kwitansi-body {
            padding: 30px;
        }

        .info-section {
            margin-bottom: 25px;
        }

        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            width: 180px;
            font-weight: 600;
            color: #555;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        .amount-section {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border: 2px dashed #667eea;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }

        .amount-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .amount-value {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }

        .amount-words {
            font-size: 14px;
            font-style: italic;
            color: #666;
            margin-top: 10px;
        }

        .payment-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 16px;
            padding-top: 15px;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 30px;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }

        .signature-label {
            margin-bottom: 80px;
            font-weight: 600;
        }

        .signature-name {
            border-top: 2px solid #333;
            padding-top: 10px;
            font-weight: 600;
        }

        .signature-title {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .kwitansi-footer {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-top: 2px solid #dee2e6;
            font-size: 12px;
            color: #666;
        }

        .notes {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            font-size: 13px;
        }

        .notes strong {
            display: block;
            margin-bottom: 5px;
            color: #856404;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .action-buttons {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .kwitansi-container {
                box-shadow: none;
                max-width: 100%;
            }

            .action-buttons {
                display: none;
            }

            .kwitansi-body {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="kwitansi-container">
        <!-- Header -->
        <div class="kwitansi-header">
            <div class="school-name">MA NEGERI EXAMPLE</div>
            <div class="school-address">
                Alamat :JI. Jenderal Ahmad Yani no. 114Kauman Batang 51215
                email: manubatang@yahoo.co.id Telp. (0285) 392663

            </div>
        </div>

        <!-- Title -->
        <div class="kwitansi-title">
            <h2>KWITANSI PEMBAYARAN</h2>
            <div class="kwitansi-number">No: {{ str_pad($transaksi->id, 6, '0', STR_PAD_LEFT) }}/KWT/{{ $transaksi->tanggal->format('Y') }}</div>
        </div>

        <!-- Body -->
        <div class="kwitansi-body">
            <!-- Student Info -->
            <div class="info-section">
                <div class="info-row">
                    <div class="info-label">Telah Diterima Dari</div>
                    <div class="info-value">: {{ $transaksi->siswa->nama }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">NIS</div>
                    <div class="info-value">: {{ $transaksi->siswa_nis }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Kelas</div>
                    <div class="info-value">: {{ $transaksi->siswa->kelas->kelas ?? '-' }}</div>
                </div>
            </div>

            <!-- Amount -->
            <div class="amount-section">
                <div class="amount-label">Jumlah Pembayaran</div>
                <div class="amount-value">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</div>
                <div class="amount-words">
                    # {{ ucwords(terbilang($transaksi->jumlah_bayar)) }} Rupiah #
                </div>
            </div>

            <!-- Payment Details -->
            <div class="payment-details">
                <div class="detail-row">
                    <span>Untuk Pembayaran</span>
                    <strong>{{ $transaksi->tagihan->jenisTagihan->nama ?? '-' }}</strong>
                </div>
                <div class="detail-row">
                    <span>Tanggal Transaksi</span>
                    <span>{{ $transaksi->tanggal->format('d F Y, H:i') }} WIB</span>
                </div>
                <div class="detail-row">
                    <span>Metode Pembayaran</span>
                    <span>
                        @php
                        $metodeText = [
                        'cash' => 'Cash/Tunai',
                        'transfer' => 'Transfer Bank',
                        'va' => 'Virtual Account',
                        'qris' => 'QRIS',
                        ];
                        @endphp
                        {{ $metodeText[$transaksi->metode] ?? $transaksi->metode }}
                    </span>
                </div>
                <div class="detail-row">
                    <span>Total Tagihan</span>
                    <span>Rp {{ number_format($transaksi->tagihan->total_tagihan, 0, ',', '.') }}</span>
                </div>
                <div class="detail-row">
                    <span>Sudah Dibayar</span>
                    <span>Rp {{ number_format($transaksi->tagihan->sudah_dibayar, 0, ',', '.') }}</span>
                </div>
                <div class="detail-row">
                    <span>Sisa Tagihan</span>
                    <strong style="color: {{ $transaksi->tagihan->status == 'lunas' ? '#28a745' : '#dc3545' }}">
                        Rp {{ number_format($transaksi->tagihan->total_tagihan - $transaksi->tagihan->sudah_dibayar, 0, ',', '.') }}
                        <span class="badge badge-{{ $transaksi->tagihan->status == 'lunas' ? 'success' : 'warning' }}" style="float: right;">
                            {{ ucfirst($transaksi->tagihan->status) }}
                        </span>
                    </strong>
                </div>
            </div>

            @if($transaksi->keterangan)
            <!-- Notes -->
            <div class="notes">
                <strong>Catatan:</strong>
                {{ $transaksi->keterangan }}
            </div>
            @endif

            <!-- Signature -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-label">Penerima,</div>
                    <div class="signature-name">_________________</div>
                    <div class="signature-title">Bendahara</div>
                </div>
                <div class="signature-box">
                    <div class="signature-label">Mengetahui,</div>
                    <div class="signature-name">_________________</div>
                    <div class="signature-title">Kepala Sekolah</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="kwitansi-footer">
            Kwitansi ini sah dan dihasilkan oleh sistem pembayaran sekolah<br>
            Dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('siswa.history') }}" class="btn btn-secondary">
            ← Kembali
        </a>
        <button class="btn btn-primary" onclick="window.print()">
            🖨️ Print Kwitansi
        </button>
    </div>
</body>

</html>

@php
// Helper function untuk terbilang
function terbilang($angka) {
$angka = abs($angka);
$baca = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
$terbilang = "";

if ($angka < 12) {
    $terbilang=" " . $baca[$angka];
    } else if ($angka < 20) {
    $terbilang=terbilang($angka - 10) . " belas" ;
    } else if ($angka < 100) {
    $terbilang=terbilang($angka / 10) . " puluh" . terbilang($angka % 10);
    } else if ($angka < 200) {
    $terbilang=" seratus" . terbilang($angka - 100);
    } else if ($angka < 1000) {
    $terbilang=terbilang($angka / 100) . " ratus" . terbilang($angka % 100);
    } else if ($angka < 2000) {
    $terbilang=" seribu" . terbilang($angka - 1000);
    } else if ($angka < 1000000) {
    $terbilang=terbilang($angka / 1000) . " ribu" . terbilang($angka % 1000);
    } else if ($angka < 1000000000) {
    $terbilang=terbilang($angka / 1000000) . " juta" . terbilang($angka % 1000000);
    }

    return trim($terbilang);
    }
    @endphp