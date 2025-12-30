@extends('layouts.siswaMaster')

@section('content')
<div class="container-fluid p-4">
  
  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-credit-card-2-front me-2 text-primary"></i>
            Riwayat Pembayaran Online
          </h4>
          <p class="text-muted mb-0">Daftar transaksi pembayaran online Anda</p>
        </div>
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-outline-secondary">
          <i class="bi bi-house me-1"></i> Dashboard
        </a>
      </div>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
              <i class="bi bi-receipt fs-4 text-primary"></i>
            </div>
            <div>
              <small class="text-muted d-block">Total Transaksi</small>
              <h5 class="fw-bold mb-0">{{ $payments->total() }}</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
              <i class="bi bi-check-circle fs-4 text-success"></i>
            </div>
            <div>
              <small class="text-muted d-block">Berhasil</small>
              <h5 class="fw-bold mb-0 text-success">{{ $payments->where('status', 'settlement')->count() + $payments->where('status', 'capture')->count() }}</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
              <i class="bi bi-clock-history fs-4 text-warning"></i>
            </div>
            <div>
              <small class="text-muted d-block">Pending</small>
              <h5 class="fw-bold mb-0 text-warning">{{ $payments->where('status', 'pending')->count() }}</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
              <i class="bi bi-cash-stack fs-4 text-info"></i>
            </div>
            <div>
              <small class="text-muted d-block">Total Dibayar</small>
              <h5 class="fw-bold mb-0 text-info">Rp {{ number_format($payments->whereIn('status', ['settlement', 'capture'])->sum('amount'), 0, ',', '.') }}</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Payment List -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
      <h5 class="mb-0 fw-bold">
        <i class="bi bi-list-ul me-2"></i>
        Daftar Transaksi
      </h5>
    </div>
    <div class="card-body p-0">
      @forelse($payments as $payment)
        <div class="payment-item p-4 border-bottom" data-order-id="{{ $payment->order_id }}">
          <div class="row align-items-center">
            
            <!-- Payment Info -->
            <div class="col-md-6">
              <div class="d-flex align-items-start">
                <div class="rounded-circle {{ $payment->isSuccess() ? 'bg-success' : ($payment->isPending() ? 'bg-warning' : 'bg-danger') }} bg-opacity-10 p-2 me-3">
                  <i class="bi {{ $payment->isSuccess() ? 'bi-check-circle text-success' : ($payment->isPending() ? 'bi-clock text-warning' : 'bi-x-circle text-danger') }} fs-4"></i>
                </div>
                <div class="flex-grow-1">
                  <h6 class="fw-bold mb-1">{{ $payment->tagihan->jenisTagihan->nama }}</h6>
                  <p class="text-muted small mb-1">
                    <i class="bi bi-hash"></i> {{ $payment->order_id }}
                  </p>
                  <p class="text-muted small mb-1">
                    <i class="bi bi-calendar3"></i> {{ $payment->created_at->format('d F Y, H:i') }}
                  </p>
                  @if($payment->payment_type)
                    <span class="badge bg-light text-dark border">
                      <i class="bi bi-credit-card me-1"></i>
                      {{ $payment->paymentTypeLabel() }}
                    </span>
                  @endif
                </div>
              </div>
            </div>

            <!-- Amount & Status -->
            <div class="col-md-3">
              <small class="text-muted d-block mb-1">Jumlah</small>
              <h5 class="fw-bold mb-0">Rp {{ number_format($payment->amount, 0, ',', '.') }}</h5>
            </div>

            <!-- Status & Action -->
            <div class="col-md-3 text-end">
              <span class="badge {{ $payment->statusBadgeClass() }} mb-2">
                {{ strtoupper($payment->status) }}
              </span>
              
              <div class="d-grid gap-2">
                <a href="{{ route('payment.detail', $payment->order_id) }}" 
                   class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-eye me-1"></i> Detail
                </a>
                
                @if($payment->isPending())
                  <button type="button" 
                          class="btn btn-sm btn-outline-warning"
                          onclick="checkPaymentStatus('{{ $payment->order_id }}')">
                    <i class="bi bi-arrow-clockwise me-1"></i> Cek Status
                  </button>
                @endif
              </div>
            </div>

          </div>

          <!-- Payment Code (for pending VA/Store) -->
          @if($payment->isPending() && $payment->payment_code)
            <div class="alert alert-warning mt-3 mb-0">
              <div class="row align-items-center">
                <div class="col-md-8">
                  <small class="text-muted d-block mb-1">Kode Pembayaran:</small>
                  <h6 class="fw-bold mb-0 font-monospace">{{ $payment->payment_code }}</h6>
                </div>
                <div class="col-md-4 text-end">
                  <button class="btn btn-sm btn-warning" onclick="copyCode('{{ $payment->payment_code }}', this)">
                    <i class="bi bi-clipboard"></i> Copy
                  </button>
                  @if($payment->pdf_url)
                    <a href="{{ $payment->pdf_url }}" target="_blank" class="btn btn-sm btn-outline-warning">
                      <i class="bi bi-file-pdf"></i> PDF
                    </a>
                  @endif
                </div>
              </div>
              <small class="text-muted d-block mt-2">
                <i class="bi bi-clock"></i> Expired: {{ $payment->expired_at->format('d M Y H:i') }}
              </small>
            </div>
          @endif
        </div>
      @empty
        <div class="text-center py-5">
          <i class="bi bi-inbox display-1 text-muted"></i>
          <h5 class="text-muted mt-3">Belum Ada Transaksi</h5>
          <p class="text-muted">Anda belum melakukan pembayaran online</p>
          <a href="{{ route('siswa.tagihan.belum-lunas') }}" class="btn btn-primary mt-2">
            <i class="bi bi-receipt me-1"></i> Lihat Tagihan
          </a>
        </div>
      @endforelse
    </div>

    @if($payments->hasPages())
      <div class="card-footer bg-white">
        {{ $payments->links() }}
      </div>
    @endif
  </div>

</div>

<style>
.payment-item {
  transition: all 0.2s;
}

.payment-item:hover {
  background-color: #f8f9fa;
}

.payment-item:last-child {
  border-bottom: none !important;
}
</style>

<script>
// Copy payment code
function copyCode(code, button) {
  navigator.clipboard.writeText(code).then(() => {
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="bi bi-check"></i> Copied!';
    button.classList.remove('btn-warning');
    button.classList.add('btn-success');
    
    setTimeout(() => {
      button.innerHTML = originalHTML;
      button.classList.remove('btn-success');
      button.classList.add('btn-warning');
    }, 2000);
  });
}

// Check payment status
function checkPaymentStatus(orderId) {
  const button = event.target;
  const originalHTML = button.innerHTML;
  button.disabled = true;
  button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Checking...';
  
  fetch(`/payment/check-status/${orderId}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        if (data.status === 'settlement' || data.status === 'capture') {
          // Success, reload page
          alert('Pembayaran berhasil dikonfirmasi!');
          window.location.reload();
        } else if (data.status === 'pending') {
          alert('Pembayaran masih menunggu. Silakan selesaikan pembayaran Anda.');
        } else {
          alert('Status: ' + data.status.toUpperCase());
        }
      }
      
      button.disabled = false;
      button.innerHTML = originalHTML;
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Gagal mengecek status.');
      button.disabled = false;
      button.innerHTML = originalHTML;
    });
}
</script>
@endsection