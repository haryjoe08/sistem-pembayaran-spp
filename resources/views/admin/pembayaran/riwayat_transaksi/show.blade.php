@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">
  
  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-receipt me-2 text-primary"></i>
            Detail Transaksi
          </h4>
          <p class="text-muted mb-0">Informasi lengkap transaksi pembayaran</p>
        </div>
        <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i>
          Kembali
        </a>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Transaction Info -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-3">
          <h6 class="mb-0 fw-semibold">
            <i class="bi bi-info-circle me-2"></i>
            Informasi Transaksi
          </h6>
        </div>
        <div class="card-body p-4">
          <div class="row mb-3">
            <div class="col-md-4">
              <label class="text-muted mb-1">ID Transaksi</label>
              <div class="fw-semibold">#{{ str_pad($transaksi->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted mb-1">Tanggal & Waktu</label>
              <div class="fw-semibold">{{ $transaksi->tanggal->format('d F Y, H:i') }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted mb-1">Metode Pembayaran</label>
              <div>
                @php
                  $metodeBadge = [
                    'cash' => ['class' => 'bg-success', 'icon' => 'cash', 'text' => 'Cash/Tunai'],
                    'transfer' => ['class' => 'bg-primary', 'icon' => 'bank', 'text' => 'Transfer Bank'],
                    'va' => ['class' => 'bg-info', 'icon' => 'credit-card', 'text' => 'Virtual Account'],
                    'qris' => ['class' => 'bg-warning', 'icon' => 'qr-code', 'text' => 'QRIS'],
                  ];
                  $badge = $metodeBadge[$transaksi->metode] ?? ['class' => 'bg-secondary', 'icon' => 'question', 'text' => $transaksi->metode];
                @endphp
                <span class="badge {{ $badge['class'] }}">
                  <i class="bi bi-{{ $badge['icon'] }} me-1"></i>
                  {{ $badge['text'] }}
                </span>
              </div>
            </div>
          </div>

          <hr class="my-4">

          <div class="row mb-3">
            <div class="col-12">
              <label class="text-muted mb-2">Jumlah Pembayaran</label>
              <h2 class="fw-bold text-success mb-0">
                Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}
              </h2>
            </div>
          </div>

          @if($transaksi->keterangan)
          <hr class="my-4">
          <div class="row">
            <div class="col-12">
              <label class="text-muted mb-2">Catatan</label>
              <div class="alert alert-light mb-0">
                <i class="bi bi-chat-left-text me-2"></i>
                {{ $transaksi->keterangan }}
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>

      <!-- Tagihan Info -->
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
          <h6 class="mb-0 fw-semibold">
            <i class="bi bi-file-earmark-text me-2"></i>
            Detail Tagihan
          </h6>
        </div>
        <div class="card-body p-4">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="text-muted mb-1">Jenis Tagihan</label>
              <div class="d-flex align-items-center">
                <i class="bi bi-receipt text-primary me-2 fs-5"></i>
                <span class="fw-semibold">{{ $transaksi->tagihan->jenisTagihan->nama ?? '-' }}</span>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="text-muted mb-1">Total Tagihan</label>
              <div class="fw-semibold">Rp {{ number_format($transaksi->tagihan->total_tagihan, 0, ',', '.') }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="text-muted mb-1">Sudah Dibayar</label>
              <div class="fw-semibold text-success">Rp {{ number_format($transaksi->tagihan->sudah_dibayar, 0, ',', '.') }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="text-muted mb-1">Status Tagihan</label>
              <div>
                <span class="badge {{ $transaksi->tagihan->status == 'lunas' ? 'bg-success' : 'bg-warning' }} px-3 py-2">
                  <i class="bi bi-{{ $transaksi->tagihan->status == 'lunas' ? 'check-circle' : 'clock' }} me-1"></i>
                  {{ ucfirst($transaksi->tagihan->status) }}
                </span>
              </div>
            </div>
          </div>

          @if($transaksi->tagihan->status != 'lunas')
          <div class="alert alert-warning mt-3 mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Sisa Tagihan:</strong> Rp {{ number_format($transaksi->tagihan->total_tagihan - $transaksi->tagihan->sudah_dibayar, 0, ',', '.') }}
          </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Student Info -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
          <h6 class="mb-0 fw-semibold">
            <i class="bi bi-person me-2"></i>
            Data Siswa
          </h6>
        </div>
        <div class="card-body p-4 text-center">
          <img src="https://ui-avatars.com/api/?name={{ urlencode($transaksi->siswa->nama) }}&background=667eea&color=fff&size=120" 
               class="rounded-circle mb-3" 
               width="120" height="120"
               alt="{{ $transaksi->siswa->nama }}">
          <h5 class="fw-bold mb-1">{{ $transaksi->siswa->nama }}</h5>
          <p class="text-muted mb-3">
            <span class="badge bg-secondary">{{ $transaksi->siswa_nis }}</span>
          </p>
          <div class="text-start">
            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
              <span class="text-muted">Kelas:</span>
              <span class="fw-semibold">{{ $transaksi->siswa->kelas->kelas ?? '-' }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
              <span class="text-muted">Jurusan:</span>
              <span class="fw-semibold">{{ $transaksi->siswa->jurusan->nama ?? '-' }}</span>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted">Tahun Ajaran:</span>
              <span class="fw-semibold">{{ $transaksi->siswa->tahunAjaran->tahun ?? '-' }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
          <a href="{{ route('transaksi.kwitansi', $transaksi->id) }}" 
             class="btn btn-success w-100 mb-2"
             target="_blank">
            <i class="bi bi-printer me-2"></i>
            Print Kwitansi
          </a>
          <a href="{{ route('pembayaran.cari', ['keyword' => $transaksi->siswa_nis]) }}" 
             class="btn btn-outline-primary w-100">
            <i class="bi bi-credit-card me-2"></i>
            Lihat Tagihan Siswa
          </a>
        </div>
      </div>

      <!-- Metadata -->
      <div class="card border-0 shadow-sm mt-3">
        <div class="card-body p-3">
          <small class="text-muted d-block mb-1">
            <i class="bi bi-clock-history me-1"></i>
            Dicatat: {{ $transaksi->created_at->format('d/m/Y H:i') }}
          </small>
          @if($transaksi->created_at != $transaksi->updated_at)
          <small class="text-muted d-block">
            <i class="bi bi-pencil me-1"></i>
            Diubah: {{ $transaksi->updated_at->format('d/m/Y H:i') }}
          </small>
          @endif
        </div>
      </div>
    </div>
  </div>

</div>

<style>
.border-bottom:last-child {
  border-bottom: none !important;
}
</style>
@endsection