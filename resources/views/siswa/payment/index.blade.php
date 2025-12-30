@extends('layouts.siswaMaster')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      
      <!-- Header -->
      <div class="text-center mb-4">
        <h3 class="fw-bold">
          <i class="bi bi-credit-card text-primary"></i>
          Pembayaran Online
        </h3>
        <p class="text-muted">Bayar tagihan dengan mudah dan aman</p>
      </div>

      <!-- Tagihan Info Card -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">
            <i class="bi bi-receipt me-2"></i>
            Detail Tagihan
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="row mb-3">
            <div class="col-md-6">
              <small class="text-muted d-block">Nama Siswa</small>
              <h6 class="fw-bold">{{ $tagihan->siswa->nama }}</h6>
            </div>
            <div class="col-md-6">
              <small class="text-muted d-block">NIS</small>
              <h6 class="fw-bold">{{ $tagihan->siswa->nis }}</h6>
            </div>
          </div>
          
          <div class="row mb-3">
            <div class="col-md-6">
              <small class="text-muted d-block">Jenis Tagihan</small>
              <h6 class="fw-bold">{{ $tagihan->jenisTagihan->nama }}</h6>
            </div>
            <div class="col-md-6">
              <small class="text-muted d-block">Kelas</small>
              <h6 class="fw-bold">{{ $tagihan->kelas->kelas ?? '-' }}</h6>
            </div>
          </div>

          <hr>

          <div class="row">
            <div class="col-md-4">
              <small class="text-muted d-block">Total Tagihan</small>
              <h5 class="fw-bold">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</h5>
            </div>
            <div class="col-md-4">
              <small class="text-muted d-block">Sudah Dibayar</small>
              <h5 class="fw-bold text-success">Rp {{ number_format($tagihan->sudah_dibayar, 0, ',', '.') }}</h5>
            </div>
            <div class="col-md-4">
              <small class="text-muted d-block">Sisa Tagihan</small>
              <h5 class="fw-bold text-danger">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</h5>
            </div>
          </div>

          @if($tagihan->jatuh_tempo)
          <div class="alert alert-warning mt-3 mb-0">
            <i class="bi bi-calendar-event me-2"></i>
            <strong>Jatuh Tempo:</strong> {{ $tagihan->jatuh_tempo->format('d F Y') }}
            @if($tagihan->isJatuhTempo())
              <span class="badge bg-danger ms-2">Sudah Lewat!</span>
            @elseif($tagihan->isMendekatJatuhTempo())
              <span class="badge bg-warning text-dark ms-2">Segera!</span>
            @endif
          </div>
          @endif
        </div>
      </div>

      <!-- Payment Form -->
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
          <h5 class="mb-0">
            <i class="bi bi-wallet2 me-2 text-primary"></i>
            Jumlah Pembayaran
          </h5>
        </div>
        <div class="card-body p-4">
          
          <!-- Quick Amount Buttons -->
          <div class="mb-3">
            <label class="form-label fw-bold">Pilih Nominal Cepat</label>
            <div class="d-grid gap-2">
              @php
                $quickAmounts = [
                  ['label' => 'Bayar Lunas', 'value' => $sisaTagihan],
                  ['label' => 'Bayar 50%', 'value' => $sisaTagihan / 2],
                  ['label' => 'Bayar Rp 100.000', 'value' => min(100000, $sisaTagihan)],
                  ['label' => 'Bayar Rp 500.000', 'value' => min(500000, $sisaTagihan)],
                ];
              @endphp
              
              <div class="row g-2">
                @foreach($quickAmounts as $qa)
                  @if($qa['value'] > 0)
                  <div class="col-6">
                    <button type="button" 
                            class="btn btn-outline-primary w-100" 
                            onclick="setAmount({{ $qa['value'] }})">
                      {{ $qa['label'] }}<br>
                      <small>Rp {{ number_format($qa['value'], 0, ',', '.') }}</small>
                    </button>
                  </div>
                  @endif
                @endforeach
              </div>
            </div>
          </div>

          <div class="text-center my-3">
            <span class="text-muted">atau</span>
          </div>

          <!-- Custom Amount -->
          <div class="mb-4">
            <label for="payment-amount" class="form-label fw-bold">Masukkan Jumlah Manual</label>
            <div class="input-group input-group-lg">
              <span class="input-group-text bg-primary text-white">Rp</span>
              <input type="text" 
                     class="form-control" 
                     id="payment-amount" 
                     placeholder="Masukkan jumlah"
                     autocomplete="off">
              <input type="hidden" id="payment-amount-raw" value="0">
            </div>
            <div class="form-text">
              Minimal Rp 10.000 • Maksimal Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
            </div>
          </div>

          <!-- Payment Summary -->
          <div class="alert alert-light border">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">Jumlah yang akan dibayar:</span>
              <h4 class="mb-0 fw-bold text-primary" id="amount-display">Rp 0</h4>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">Sisa setelah pembayaran:</span>
              <h5 class="mb-0 fw-bold text-danger" id="remaining-display">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</h5>
            </div>
          </div>

          <!-- Payment Button -->
          <button type="button" 
                  class="btn btn-primary btn-lg w-100" 
                  id="pay-button"
                  disabled>
            <i class="bi bi-credit-card me-2"></i>
            Bayar Sekarang
          </button>

          <div class="text-center mt-3">
            <small class="text-muted">
              <i class="bi bi-shield-check me-1"></i>
              Pembayaran aman dengan Midtrans
            </small>
          </div>
        </div>
      </div>

      <!-- Payment Methods Info -->
      <div class="card border-0 shadow-sm mt-3">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3">
            <i class="bi bi-info-circle text-primary me-2"></i>
            Metode Pembayaran yang Tersedia
          </h6>
          <div class="row g-2">
            <div class="col-4 col-md-2 text-center">
              <div class="border rounded p-2">
                <i class="bi bi-credit-card fs-4 text-primary"></i>
                <small class="d-block">Kartu Kredit</small>
              </div>
            </div>
            <div class="col-4 col-md-2 text-center">
              <div class="border rounded p-2">
                <i class="bi bi-bank fs-4 text-primary"></i>
                <small class="d-block">Transfer Bank</small>
              </div>
            </div>
            <div class="col-4 col-md-2 text-center">
              <div class="border rounded p-2">
                <i class="bi bi-qr-code fs-4 text-primary"></i>
                <small class="d-block">QRIS</small>
              </div>
            </div>
            <div class="col-4 col-md-2 text-center">
              <div class="border rounded p-2">
                <i class="bi bi-phone fs-4 text-primary"></i>
                <small class="d-block">GoPay</small>
              </div>
            </div>
            <div class="col-4 col-md-2 text-center">
              <div class="border rounded p-2">
                <i class="bi bi-shop fs-4 text-primary"></i>
                <small class="d-block">Alfamart</small>
              </div>
            </div>
            <div class="col-4 col-md-2 text-center">
              <div class="border rounded p-2">
                <i class="bi bi-shop fs-4 text-primary"></i>
                <small class="d-block">Indomaret</small>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Midtrans Snap JS -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
const sisaTagihan = {{ $sisaTagihan }};
const tagihanId = {{ $tagihan->id }};
const paymentAmountInput = document.getElementById('payment-amount');
const paymentAmountRaw = document.getElementById('payment-amount-raw');
const amountDisplay = document.getElementById('amount-display');
const remainingDisplay = document.getElementById('remaining-display');
const payButton = document.getElementById('pay-button');

// Format rupiah input
paymentAmountInput.addEventListener('input', function(e) {
  let value = e.target.value.replace(/\D/g, '');
  
  if (value) {
    const numValue = parseInt(value);
    
    // Validate
    if (numValue > sisaTagihan) {
      value = sisaTagihan.toString();
      alert('Jumlah tidak boleh melebihi sisa tagihan!');
    }
    
    paymentAmountRaw.value = value;
    e.target.value = parseInt(value).toLocaleString('id-ID');
    updateSummary(parseInt(value));
  } else {
    paymentAmountRaw.value = 0;
    e.target.value = '';
    updateSummary(0);
  }
});

// Set amount from quick buttons
function setAmount(amount) {
  paymentAmountRaw.value = amount;
  paymentAmountInput.value = amount.toLocaleString('id-ID');
  updateSummary(amount);
}

// Update summary display
function updateSummary(amount) {
  amountDisplay.textContent = 'Rp ' + amount.toLocaleString('id-ID');
  
  const remaining = sisaTagihan - amount;
  remainingDisplay.textContent = 'Rp ' + remaining.toLocaleString('id-ID');
  
  // Enable/disable button
  if (amount >= 10000 && amount <= sisaTagihan) {
    payButton.disabled = false;
  } else {
    payButton.disabled = true;
  }
}

// Pay button click
payButton.addEventListener('click', function() {
  const amount = parseInt(paymentAmountRaw.value);
  
  if (amount < 10000) {
    alert('Minimal pembayaran Rp 10.000');
    return;
  }
  
  if (amount > sisaTagihan) {
    alert('Jumlah melebihi sisa tagihan!');
    return;
  }
  
  // Disable button
  payButton.disabled = true;
  payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
  
  // Create payment
  fetch(`/payment/create/${tagihanId}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      amount: amount
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Open Snap payment
      snap.pay(data.snap_token, {
        onSuccess: function(result) {
          window.location.href = `/payment/finish?order_id=${data.order_id}`;
        },
        onPending: function(result) {
          window.location.href = `/payment/finish?order_id=${data.order_id}`;
        },
        onError: function(result) {
          window.location.href = `/payment/error?order_id=${data.order_id}`;
        },
        onClose: function() {
          // Re-enable button
          payButton.disabled = false;
          payButton.innerHTML = '<i class="bi bi-credit-card me-2"></i>Bayar Sekarang';
        }
      });
    } else {
      alert('Error: ' + data.message);
      payButton.disabled = false;
      payButton.innerHTML = '<i class="bi bi-credit-card me-2"></i>Bayar Sekarang';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Terjadi kesalahan. Silakan coba lagi.');
    payButton.disabled = false;
    payButton.innerHTML = '<i class="bi bi-credit-card me-2"></i>Bayar Sekarang';
  });
});
</script>
@endsection