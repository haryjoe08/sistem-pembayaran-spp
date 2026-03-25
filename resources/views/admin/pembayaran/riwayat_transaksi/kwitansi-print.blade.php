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
        font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
        padding: 10px;
        background: #f9fafb;
        color: #333;
    }

    .kwitansi-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .kwitansi-header {
        background: white !important;
        color: #1e293b !important;
        border-top: 4px solid #1e3a8a;
        padding: 12px 20px;
        text-align: center;
    }

    .school-logo {
        height: 45px;
        margin-bottom: 8px;
        object-fit: contain;
    }

    .school-name {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .school-address {
        font-size: 12px;
        opacity: 0.9;
        line-height: 1.3;
    }

    .kwitansi-title {
        background: #f8fafc;
        padding: 10px;
        text-align: center;
        border-bottom: 1px solid #e2e8f0;
    }

    .kwitansi-title h2 {
        font-size: 16px;
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .kwitansi-number {
        font-size: 12px;
        color: #64748b;
        font-family: monospace;
    }

    .kwitansi-body {
        padding: 18px;
    }

    .info-section {
        margin-bottom: 15px;
    }

    .info-row {
        display: flex;
        padding: 6px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        width: 150px;
        font-weight: 600;
        color: #475569;
    }

    .info-value {
        flex: 1;
        color: #1e293b;
    }

    .amount-section {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 6px;
        padding: 14px;
        margin: 14px 0;
        text-align: center;
    }

    .amount-label {
        font-size: 13px;
        color: #475569;
        margin-bottom: 6px;
    }

    .amount-value {
        font-size: 22px;
        font-weight: 700;
        color: #0c4a6e;
        font-family: 'Courier New', monospace;
    }

    .amount-words {
        font-size: 12px;
        color: #64748b;
        margin-top: 6px;
        font-style: italic;
    }

    .payment-details {
        background: #f8fafc;
        padding: 14px;
        border-radius: 6px;
        margin: 10px 0;
        border: 1px solid #e2e8f0;
        font-size: 13px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .detail-row:last-child {
        border-bottom: none;
        font-weight: 700;
        font-size: 14px;
        padding-top: 8px;
    }

    .signature-section {
        display: flex;
        justify-content: space-between;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
        font-size: 13px;
    }

    .signature-box {
        text-align: center;
        width: 45%;
    }

    .signature-label {
        margin-bottom: 50px;
        font-weight: 600;
        color: #475569;
    }

    .signature-name {
        border-top: 1px solid #475569;
        padding-top: 5px;
        font-weight: 600;
        color: #1e293b;
    }

    .signature-title {
        font-size: 11px;
        color: #64748b;
        margin-top: 4px;
    }

    .kwitansi-footer {
        background: #f8fafc;
        padding: 10px;
        text-align: center;
        border-top: 1px solid #e2e8f0;
        font-size: 11px;
        color: #64748b;
    }

    .notes {
        background: #fffbeb;
        border-left: 3px solid #eab308;
        padding: 10px;
        margin: 12px 0;
        font-size: 12px;
        color: #854d0e;
        border-radius: 0 4px 4px 0;
    }

    .badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        margin-left: 6px;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .print-button {
        position: fixed;
        bottom: 25px;
        right: 25px;
        background: #1e3a8a;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 3px 8px rgba(30, 58, 138, 0.3);
        transition: all 0.25s ease;
    }

    .print-button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    /* === OPTIMASI UNTUK PRINT === */
    @media print {
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        html, body {
            width: 210mm;
            height: 297mm;
            background: white;
            padding: 0;
            margin: 0;
        }

        .kwitansi-container {
            max-width: 100%;
            border: none;
            box-shadow: none;
            margin: 0;
        }

        .kwitansi-body {
            padding: 15px;
        }

        .print-button {
            display: none !important;
        }

        /* Pastikan tidak ada elemen terpotong */
        .signature-section {
            page-break-inside: avoid;
        }

        .amount-section,
        .payment-details {
            page-break-inside: avoid;
        }
    }
</style>

</head>

<body>
    <div class="kwitansi-container">
        <!-- Header -->
        <div class="kwitansi-header">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSxQJNLNivJGJpOmS6PMtai7LBygqKAEaE0uQ&s" alt="Logo MA NU Batang" class="school-logo">
            <div class="school-name">MA NU BATANG</div>
            <div class="school-address">
                Alamat :JI. Jenderal Ahmad Yani no. 114Kauman Batang 51215 <br>
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
                    <strong>{{ $transaksi->tagihan->jenisPembayaran->nama ?? '-' }}</strong>
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
                        {{ $metodeText[$transaksi->metode] ?? ucfirst($transaksi->metode) }}
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
                    <strong style="color: {{ $transaksi->tagihan->status == 'lunas' ? '#166534' : '#b91c1c' }}">
                        Rp {{ number_format($transaksi->tagihan->total_tagihan - $transaksi->tagihan->sudah_dibayar, 0, ',', '.') }}
                        <span class="badge badge-{{ $transaksi->tagihan->status == 'lunas' ? 'success' : 'warning' }}">
                            {{ ucfirst($transaksi->tagihan->status) }}
                        </span>
                    </strong>
                </div>
            </div>

            @if($transaksi->keterangan)
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
                    <div class="signature-title">Wiroso</div>
                </div>
                <div class="signature-box">
                    <div class="signature-label">Mengetahui,</div>
                    <div class="signature-name">_________________</div>
                    <div class="signature-title">Sarwani Spd.I</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="kwitansi-footer">
            Kwitansi ini sah dan dihasilkan oleh sistem pembayaran sekolah<br>
            Dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB
        </div>
    </div>

    <!-- Print Button -->
    <button class="print-button" onclick="window.print()">
        Cetak Kwitansi
    </button>

    <script>
        // Opsional: auto print
        // window.onload = () => window.print();
    </script>
</body>

</html>

@php
function terbilang($angka) {
$angka = abs($angka);
$baca = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
$terbilang = "";

if ($angka < 12) {
    $terbilang=" " . $baca[$angka];
    } else if ($angka < 20) {
    $terbilang=terbilang($angka - 10) . " belas" ;
    } else if ($angka < 100) {
    $terbilang=terbilang(intval($angka / 10)) . " puluh" . terbilang($angka % 10);
    } else if ($angka < 200) {
    $terbilang=" seratus" . terbilang($angka - 100);
    } else if ($angka < 1000) {
    $terbilang=terbilang(intval($angka / 100)) . " ratus" . terbilang($angka % 100);
    } else if ($angka < 2000) {
    $terbilang=" seribu" . terbilang($angka - 1000);
    } else if ($angka < 1000000) {
    $terbilang=terbilang(intval($angka / 1000)) . " ribu" . terbilang($angka % 1000);
    } else if ($angka < 1000000000) {
    $terbilang=terbilang(intval($angka / 1000000)) . " juta" . terbilang($angka % 1000000);
    }

    return trim($terbilang);
    }
    @endphp