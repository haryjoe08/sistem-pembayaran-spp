@extends('layouts.siswaMaster')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7">
      
      @if($paymentOrder->isSuccess())
        <!-- SUCCESS -->
        <div class="text-center mb-4">
          <div class="success-animation">
            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
              <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
              <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
          </div>
          <h3 class="fw-bold text-success mt-3">Pembayaran Berhasil!</h3>
          <p class="text-muted">Terima kasih, pembayaran Anda telah dikonfirmasi</p>
        </div>

      @elseif($paymentOrder->isPending())
        <!-- PENDING -->
        <div class="text-center mb-4">
          <i class="bi bi-clock-history display-1 text-warning"></i>
          <h3 class="fw-bold text-warning mt-3">Menunggu Pembayaran</h3>
          <p class="text-muted">Silakan selesaikan pembayaran Anda</p>
        </div>

      @else
        <!-- FAILED -->
        <div class="text-center mb-4">
          <i class="bi bi-x-circle display-1 text-danger"></i>
          <h3 class="fw-bold text-danger mt-3">Pembayaran Gagal</h3>
          <p class="text-muted">Terjadi kesalahan pada proses pembayaran</p>
        </div>
      @endif

      <!-- Payment Details -->
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-4">
            <i class="bi bi-receipt me-2"></i>
            Detail Pembayaran
          </h5>

          <div class="row mb-3">
            <div class="col-6">
              <small class="text-muted d-block">Order ID</small>
              <p class="fw-bold mb-0">{{ $paymentOrder->order_id }}</p>
            </div>
            <div class="col-6">
              <small class="text-muted d-block">Status</small>
              <p class="mb-0">
                <span class="badge {{ $paymentOrder->statusBadgeClass() }}">
                  {{ strtoupper($paymentOrder->status) }}
                </span>
              </p>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-6">
              <small class="text-muted d-block">Tanggal Transaksi</small>
              <p class="fw-bold mb-0">{{ $paymentOrder->created_at->format('d F Y H:i') }}</p>
            </div>
            <div class="col-6">
              <small class="text-muted d-block">Metode Pembayaran</small>
              <p class="fw-bold mb-0">{{ $paymentOrder->paymentTypeLabel() }}</p>
            </div>
          </div>

          <hr>

          <div class="row mb-3">
            <div class="col-12">
              <small class="text-muted d-block">Nama Siswa</small>
              <p class="fw-bold mb-0">{{ $paymentOrder->siswa->nama }}</p>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-12">
              <small class="text-muted d-block">Jenis Tagihan</small>
              <p class="fw-bold mb-0">{{ $paymentOrder->tagihan->jenisTagihan->nama }}</p>
            </div>
          </div>

          <hr>

          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Total Pembayaran</h5>
            <h4 class="mb-0 fw-bold text-primary">Rp {{ number_format($paymentOrder->amount, 0, ',', '.') }}</h4>
          </div>

          @if($paymentOrder->isSuccess())
            <div class="alert alert-success mt-3 mb-0">
              <i class="bi bi-check-circle me-2"></i>
              Transaksi telah dikonfirmasi dan Pembayaran telah tercatat.
            </div>
          @endif

          @if($paymentOrder->isPending())
            <!-- Payment Instructions for Pending -->
            @if($paymentOrder->payment_code)
              <div class="alert alert-warning mt-3">
                <h6 class="fw-bold mb-2">
                  <i class="bi bi-info-circle me-2"></i>
                  Kode Pembayaran
                </h6>
                <div class="input-group">
                  <input type="text" 
                         class="form-control form-control-lg fw-bold text-center" 
                         value="{{ $paymentOrder->payment_code }}" 
                         id="payment-code" 
                         readonly>
                  <button class="btn btn-outline-secondary" 
                          type="button" 
                          onclick="copyPaymentCode()">
                    <i class="bi bi-clipboard"></i> Copy
                  </button>
                </div>
                <small class="text-muted d-block mt-2">
                  Gunakan kode ini untuk melakukan pembayaran
                </small>
              </div>
            @endif

            @if($paymentOrder->pdf_url)
              <a href="{{ $paymentOrder->pdf_url }}" 
                 target="_blank" 
                 class="btn btn-outline-primary w-100 mt-2">
                <i class="bi bi-file-pdf me-2"></i>
                Download Instruksi Pembayaran
              </a>
            @endif

            <div class="alert alert-light border mt-3 mb-0">
              <small class="text-muted">
                <i class="bi bi-clock me-1"></i>
                Pembayaran akan otomatis expired pada: 
                <strong>{{ $paymentOrder->expired_at->format('d F Y H:i') }}</strong>
              </small>
            </div>
          @endif

        </div>
      </div>

      
      <!-- Action Buttons -->
      <div class="d-grid gap-2">
        @if($paymentOrder->isSuccess())
          <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary btn-lg">
            <i class="bi bi-house me-2"></i>
            Kembali ke Dashboard
          </a>
          <a href="{{ route('siswa.history') }}" class="btn btn-outline-primary">
            <i class="bi bi-clock-history me-2"></i>
            Lihat Riwayat Pembayaran
          </a>

        @elseif($paymentOrder->isPending())
          <button type="button" 
                  class="btn btn-primary btn-lg" 
                  onclick="checkStatus()">
            <i class="bi bi-arrow-clockwise me-2"></i>
            Cek Status Pembayaran
          </button>
          <a href="{{ route('siswa.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-house me-2"></i>
            Kembali ke Dashboard
          </a>

        @else
          <a href="{{ route('payment.index', $paymentOrder->tagihan_id) }}" class="btn btn-primary btn-lg">
            <i class="bi bi-arrow-repeat me-2"></i>
            Coba Lagi
          </a>
          <a href="{{ route('siswa.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-house me-2"></i>
            Kembali ke Dashboard
          </a>
        @endif
      </div>

    </div>
  </div>
</div>

<style>
.success-animation {
  margin: 20px auto;
}

.checkmark {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  display: block;
  stroke-width: 2;
  stroke: #4CAF50;
  stroke-miterlimit: 10;
  box-shadow: inset 0px 0px 0px #4CAF50;
  animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
  position: relative;
  margin: 0 auto;
}

.checkmark__circle {
  stroke-dasharray: 166;
  stroke-dashoffset: 166;
  stroke-width: 2;
  stroke-miterlimit: 10;
  stroke: #4CAF50;
  fill: #fff;
  animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.checkmark__check {
  transform-origin: 50% 50%;
  stroke-dasharray: 48;
  stroke-dashoffset: 48;
  stroke: #4CAF50;
  animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}

@keyframes stroke {
  100% {
    stroke-dashoffset: 0;
  }
}

@keyframes scale {
  0%, 100% {
    transform: none;
  }
  50% {
    transform: scale3d(1.1, 1.1, 1);
  }
}

@keyframes fill {
  100% {
    box-shadow: inset 0px 0px 0px 30px #4CAF50;
  }
}
</style>

<script>
// Copy payment code
function copyPaymentCode() {
  const codeInput = document.getElementById('payment-code');
  codeInput.select();
  document.execCommand('copy');
  
  // Show feedback
  const btn = event.target.closest('button');
  const originalHTML = btn.innerHTML;
  btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
  btn.classList.add('btn-success');
  btn.classList.remove('btn-outline-secondary');
  
  setTimeout(() => {
    btn.innerHTML = originalHTML;
    btn.classList.remove('btn-success');
    btn.classList.add('btn-outline-secondary');
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
          // Success, reload page
          window.location.reload();
        } else if (data.status === 'pending') {
          alert('Pembayaran masih menunggu. Silakan selesaikan pembayaran Anda.');
        } else {
          alert('Status pembayaran: ' + data.status.toUpperCase());
        }
      }
      
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Cek Status Pembayaran';
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Gagal mengecek status. Silakan coba lagi.');
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Cek Status Pembayaran';
    });
}

// Auto-check status for pending payments every 30 seconds
@if($paymentOrder->isPending())
setInterval(() => {
  fetch('/payment/check-status/{{ $paymentOrder->order_id }}')
    .then(response => response.json())
    .then(data => {
      if (data.success && (data.status === 'settlement' || data.status === 'capture')) {
        window.location.reload();
      }
    });
}, 30000);
@endif
</script>
@endsection