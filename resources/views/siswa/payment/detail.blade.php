@extends('layouts.siswaMaster')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
          <i class="bi bi-file-earmark-text me-2 text-primary"></i>
          Detail Pembayaran
        </h4>
        <a href="{{ route('siswa.payment.history') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
      </div>

      <!-- Status Banner -->
      @if($paymentOrder->isSuccess())
        <div class="alert alert-success border-0 shadow-sm mb-4">
          <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-1 me-3"></i>
            <div>
              <h5 class="mb-1">Pembayaran Berhasil</h5>
              <p class="mb-0">Transaksi telah dikonfirmasi dan tercatat dalam sistem</p>
            </div>
          </div>
        </div>
      @elseif($paymentOrder->isPending())
        <div class="alert alert-warning border-0 shadow-sm mb-4">
          <div class="d-flex align-items-center">
            <i class="bi bi-clock-history fs-1 me-3"></i>
            <div>
              <h5 class="mb-1">Menunggu Pembayaran</h5>
              <p class="mb-0">Silakan selesaikan pembayaran sebelum waktu expired</p>
            </div>
          </div>
        </div>
      @else
        <div class="alert alert-danger border-0 shadow-sm mb-4">
          <div class="d-flex align-items-center">
            <i class="bi bi-x-circle-fill fs-1 me-3"></i>
            <div>
              <h5 class="mb-1">Pembayaran Gagal</h5>
              <p class="mb-0">Transaksi tidak dapat diproses</p>
            </div>
          </div>
        </div>
      @endif

      <!-- Transaction Details -->
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">
            <i class="bi bi-receipt me-2"></i>
            Informasi Transaksi
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="row mb-3">
            <div class="col-md-6">
              <small class="text-muted d-block mb-1">Order ID</small>
              <h6 class="fw-bold font-monospace mb-0">{{ $paymentOrder->order_id }}</h6>
            </div>
            <div class="col-md-6">
              <small class="text-muted d-block mb-1">Status</small>
              <span class="badge {{ $paymentOrder->statusBadgeClass() }} px-3 py-2">
                {{ strtoupper($paymentOrder->status) }}
              </span>
            </div>
          </div>

          @if($paymentOrder->transaction_id)
          <div class="row mb-3">
            <div class="col-12">
              <small class="text-muted d-block mb-1">Transaction ID</small>
              <p class="fw-bold font-monospace mb-0">{{ $paymentOrder->transaction_id }}</p>
            </div>
          </div>
          @endif

          <div class="row mb-3">
            <div class="col-md-6">
              <small class="text-muted d-block mb-1">Tanggal Dibuat</small>
              <p class="fw-bold mb-0">{{ $paymentOrder->created_at->format('d F Y, H:i') }}</p>
            </div>
            @if($paymentOrder->paid_at)
            <div class="col-md-6">
              <small class="text-muted d-block mb-1">Tanggal Dibayar</small>
              <p class="fw-bold mb-0 text-success">{{ $paymentOrder->paid_at->format('d F Y, H:i') }}</p>
            </div>
            @endif
          </div>

          @if($paymentOrder->isPending() && $paymentOrder->expired_at)
          <div class="row mb-3">
            <div class="col-12">
              <small class="text-muted d-block mb-1">Waktu Expired</small>
              <p class="fw-bold mb-0 {{ $paymentOrder->isExpired() ? 'text-danger' : 'text-warning' }}">
                {{ $paymentOrder->expired_at->format('d F Y, H:i') }}
                @if($paymentOrder->isExpired())
                  <span class="badge bg-danger ms-2">EXPIRED</span>
                @else
                  <span class="badge bg-warning text-dark ms-2">{{ $paymentOrder->expired_at->diffForHumans() }}</span>
                @endif
              </p>
            </div>
          </div>
          @endif

          @if($paymentOrder->payment_type)
          <div class="row">
            <div class="col-12">
              <small class="text-muted d-block mb-1">Metode Pembayaran</small>
              <p class="fw-bold mb-0">{{ $paymentOrder->paymentTypeLabel() }}</p>
            </div>
          </div>
          @endif
        </div>
      </div>

      <!-- Student & Tagihan Info -->
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white">
          <h5 class="mb-0 fw-bold">
            <i class="bi bi-person-circle me-2 text-primary"></i>
            Informasi Siswa & Tagihan
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="row mb-3">
            <div class="col-md-6">
              <small class="text-muted d-block mb-1">Nama Siswa</small>
              <h6 class="fw-bold mb-0">{{ $paymentOrder->siswa->nama }}</h6>
            </div>
            <div class="col-md-6">
              <small class="text-muted d-block mb-1">NIS</small>
              <h6 class="fw-bold mb-0">{{ $paymentOrder->siswa->nis }}</h6>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <small class="text-muted d-block mb-1">Kelas</small>
              <p class="fw-bold mb-0">{{ $paymentOrder->siswa->kelas->kelas ?? '-' }}</p>
            </div>
            <div class="col-md-6">
              <small class="text-muted d-block mb-1">Jurusan</small>
              <p class="fw-bold mb-0">{{ $paymentOrder->siswa->jurusan->jurusan ?? '-' }}</p>
            </div>
          </div>

          <hr>

          <div class="row">
            <div class="col-12">
              <small class="text-muted d-block mb-1">Jenis Pembayaran</small>
              <h6 class="fw-bold mb-0">{{ $paymentOrder->tagihan->jenisPembayaran->nama }}</h6>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Amount -->
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <small class="text-muted d-block mb-1">Total Pembayaran</small>
              <h3 class="fw-bold mb-0 text-primary">Rp {{ number_format($paymentOrder->amount, 0, ',', '.') }}</h3>
            </div>
            @if($paymentOrder->isSuccess())
              <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
            @endif
          </div>
        </div>
      </div>

      <!-- Payment Code (for pending) -->
      @if($paymentOrder->isPending() && $paymentOrder->payment_code)
      <div class="card border-0 shadow-sm border-warning mb-3">
        <div class="card-header bg-warning text-dark">
          <h6 class="mb-0 fw-bold">
            <i class="bi bi-tag me-2"></i>
            Kode Pembayaran
          </h6>
        </div>
        <div class="card-body p-4">
          <div class="input-group input-group-lg mb-3">
            <input type="text" 
                   class="form-control fw-bold text-center font-monospace" 
                   value="{{ $paymentOrder->payment_code }}" 
                   id="payment-code" 
                   readonly>
            <button class="btn btn-warning" type="button" onclick="copyPaymentCode()">
              <i class="bi bi-clipboard"></i> Copy
            </button>
          </div>
          
          <div class="alert alert-light border mb-0">
            <i class="bi bi-info-circle me-2"></i>
            <small>Gunakan kode ini untuk melakukan pembayaran di channel yang Anda pilih</small>
          </div>

          @if($paymentOrder->pdf_url)
          <a href="{{ $paymentOrder->pdf_url }}" 
             target="_blank" 
             class="btn btn-outline-warning w-100 mt-3">
            <i class="bi bi-file-pdf me-2"></i>
            Download Instruksi Pembayaran
          </a>
          @endif
        </div>
      </div>
      @endif

      <!-- Actions -->
      <div class="d-grid gap-2">
        @if($paymentOrder->isPending() && !$paymentOrder->isExpired())
          <button type="button" 
                  class="btn btn-primary btn-lg" 
                  onclick="checkStatus()">
            <i class="bi bi-arrow-clockwise me-2"></i>
            Cek Status Pembayaran
          </button>
        @endif

        @if($paymentOrder->isSuccess())
          <a href="{{ route('siswa.history') }}" class="btn btn-outline-primary">
            <i class="bi bi-clock-history me-2"></i>
            Lihat Riwayat Lengkap
          </a>
        @endif

        @if($paymentOrder->isFailed() || $paymentOrder->isExpired())
          <a href="{{ route('payment.index', $paymentOrder->tagihan_id) }}" 
             class="btn btn-primary btn-lg">
            <i class="bi bi-arrow-repeat me-2"></i>
            Coba Bayar Lagi
          </a>
        @endif

        <a href="{{ route('siswa.payment.history') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-2"></i>
          Kembali ke History
        </a>
      </div>

      <!-- Print Button (for success only) -->
      @if($paymentOrder->isSuccess())
      <div class="text-center mt-3">
        <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
          <i class="bi bi-printer me-1"></i>
          Print Detail
        </button>
      </div>
      @endif

    </div>
  </div>
</div>

<script>
// Copy payment code
function copyPaymentCode() {
  const codeInput = document.getElementById('payment-code');
  codeInput.select();
  document.execCommand('copy');
  
  const btn = event.target.closest('button');
  const originalHTML = btn.innerHTML;
  btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
  btn.classList.remove('btn-warning');
  btn.classList.add('btn-success');
  
  setTimeout(() => {
    btn.innerHTML = originalHTML;
    btn.classList.remove('btn-success');
    btn.classList.add('btn-warning');
  }, 2000);
}

// Check payment status
function checkStatus() {
  const btn = event.target;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengecek...';
  
  fetch('/payment/check-status/{{ $paymentOrder->order_id }}')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        if (data.status === 'settlement' || data.status === 'capture') {
          alert('Pembayaran berhasil dikonfirmasi!');
          window.location.reload();
        } else if (data.status === 'pending') {
          alert('Pembayaran masih menunggu. Silakan selesaikan pembayaran Anda.');
        } else {
          alert('Status: ' + data.status.toUpperCase());
        }
      }
      
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Cek Status Pembayaran';
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Gagal mengecek status.');
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Cek Status Pembayaran';
    });
}

// Auto-check for pending payments
@if($paymentOrder->isPending() && !$paymentOrder->isExpired())
setInterval(() => {
  fetch('/payment/check-status/{{ $paymentOrder->order_id }}')
    .then(response => response.json())
    .then(data => {
      if (data.success && (data.status === 'settlement' || data.status === 'capture')) {
        window.location.reload();
      }
    });
}, 30000); // Check every 30 seconds
@endif
</script>

<style>
@media print {
  .btn, nav, .alert { display: none; }
}
</style>
@endsection