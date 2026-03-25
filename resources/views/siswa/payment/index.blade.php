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
          @php
            $nominalCepat   = [100000, 250000, 500000, 1000000];
            $nominalCepat   = array_values(array_filter($nominalCepat, fn($n) => $n < $sisaTagihan));
            $bayarSetengah  = (int) floor($sisaTagihan / 2);
          @endphp

          <div class="mb-4">
            <label class="form-label fw-semibold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:.06em;">
              Nominal Cepat
            </label>

            <!-- Baris 1: Bayar Lunas (full width, paling prominent) -->
            <button type="button"
                    class="quick-btn btn btn-primary w-100 mb-2 py-3"
                    data-amount="{{ $sisaTagihan }}">
              <div class="d-flex align-items-center justify-content-between px-1">
                <span class="fw-bold fs-6">
                  <i class="bi bi-check-circle-fill me-2"></i>Bayar Lunas
                </span>
                <span class="badge bg-white text-primary fw-bold px-3 py-2" style="font-size:.85rem;">
                  Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                </span>
              </div>
            </button>

            <!-- Baris 2: Bayar 50% (selalu tampil, setengah lebar) -->
            <div class="row g-2 mb-2">
              <div class="col-6">
                <button type="button"
                        class="quick-btn btn btn-outline-success w-100 py-3"
                        data-amount="{{ $bayarSetengah }}">
                  <div class="fw-semibold">Bayar 50%</div>
                  <small class="text-success opacity-75">Rp {{ number_format($bayarSetengah, 0, ',', '.') }}</small>
                </button>
              </div>

              {{-- Slot untuk nominal cepat pertama jika ada --}}
              @if(isset($nominalCepat[0]))
              <div class="col-6">
                <button type="button"
                        class="quick-btn btn btn-outline-secondary w-100 py-3"
                        data-amount="{{ $nominalCepat[0] }}">
                  <div class="fw-semibold">Rp {{ number_format($nominalCepat[0], 0, ',', '.') }}</div>
                  <small class="opacity-50">nominal cepat</small>
                </button>
              </div>
              @endif
            </div>

            {{-- Baris 3+: Sisa nominal cepat (jika ada lebih dari 1) --}}
            @if(count($nominalCepat) > 1)
            <div class="row g-2">
              @foreach(array_slice($nominalCepat, 1) as $nominal)
              <div class="col-6">
                <button type="button"
                        class="quick-btn btn btn-outline-secondary w-100 py-3"
                        data-amount="{{ $nominal }}">
                  <div class="fw-semibold">Rp {{ number_format($nominal, 0, ',', '.') }}</div>
                  <small class="opacity-50">nominal cepat</small>
                </button>
              </div>
              @endforeach
            </div>
            @endif
          </div>

          <!-- Divider -->
          <div class="d-flex align-items-center gap-3 my-4">
            <hr class="flex-grow-1 m-0">
            <span class="text-muted small px-1">atau masukkan manual</span>
            <hr class="flex-grow-1 m-0">
          </div>

          <!-- Custom Amount -->
          <div class="mb-4">
            <label for="payment-amount" class="form-label fw-semibold">Jumlah Lainnya</label>
            <div class="input-group input-group-lg">
              <span class="input-group-text bg-primary text-white fw-semibold">Rp</span>
              <input type="text"
                     class="form-control"
                     id="payment-amount"
                     placeholder="0"
                     autocomplete="off">
              <input type="hidden" id="payment-amount-raw" value="0">
            </div>
            <div class="form-text">
              Minimal Rp 10.000 &bull; Maksimal Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
            </div>
          </div>

          <!-- Payment Summary -->
          <div class="alert alert-light border rounded-3 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted small">Jumlah yang akan dibayar:</span>
              <h4 class="mb-0 fw-bold text-primary" id="amount-display">Rp 0</h4>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted small">Sisa setelah pembayaran:</span>
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

<style>
  /* Quick button base */
  .quick-btn {
    transition: all 0.15s ease;
    position: relative;
  }

  .quick-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
  }

  .quick-btn:active {
    transform: translateY(0);
  }

  /* Active / selected state */
  .quick-btn.is-active {
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25) !important;
  }

  .quick-btn.is-active.btn-primary {
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.35) !important;
  }

  .quick-btn.is-active.btn-outline-success {
    background-color: #198754 !important;
    color: #fff !important;
    box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.25) !important;
  }

  .quick-btn.is-active.btn-outline-secondary {
    background-color: #6c757d !important;
    color: #fff !important;
    box-shadow: 0 0 0 3px rgba(108, 117, 125, 0.25) !important;
  }

  /* Input focus ring */
  #payment-amount:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.2);
  }

  /* Highlight input when manually typing */
  #payment-amount.is-manual-active {
    border-color: #0d6efd;
    background-color: #f0f6ff;
  }
</style>

<!-- Midtrans Snap JS -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
  const sisaTagihan       = {{ $sisaTagihan }};
  const tagihanId         = {{ $tagihan->id }};
  const paymentAmountInput = document.getElementById('payment-amount');
  const paymentAmountRaw   = document.getElementById('payment-amount-raw');
  const amountDisplay      = document.getElementById('amount-display');
  const remainingDisplay   = document.getElementById('remaining-display');
  const payButton          = document.getElementById('pay-button');
  const quickBtns          = document.querySelectorAll('.quick-btn');

  // ── Helpers ──────────────────────────────────────────────────────────────

  function clearActiveButtons() {
    quickBtns.forEach(btn => btn.classList.remove('is-active'));
  }

  function updateSummary(amount) {
    amountDisplay.textContent   = 'Rp ' + amount.toLocaleString('id-ID');
    remainingDisplay.textContent = 'Rp ' + (sisaTagihan - amount).toLocaleString('id-ID');
    payButton.disabled = !(amount >= 10000 && amount <= sisaTagihan);
  }

  // ── Quick Buttons ─────────────────────────────────────────────────────────

  quickBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      const amount = parseInt(this.dataset.amount);

      // Active state
      clearActiveButtons();
      this.classList.add('is-active');

      // Sync input
      paymentAmountRaw.value   = amount;
      paymentAmountInput.value = amount.toLocaleString('id-ID');
      paymentAmountInput.classList.remove('is-manual-active');

      updateSummary(amount);
    });
  });

  // ── Manual Input ──────────────────────────────────────────────────────────

  paymentAmountInput.addEventListener('focus', function () {
    this.classList.add('is-manual-active');
    clearActiveButtons();
  });

  paymentAmountInput.addEventListener('blur', function () {
    if (!paymentAmountRaw.value || paymentAmountRaw.value === '0') {
      this.classList.remove('is-manual-active');
    }
  });

  paymentAmountInput.addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, '');

    // Clamp to max
    if (value && parseInt(value) > sisaTagihan) {
      value = sisaTagihan.toString();
    }

    if (value) {
      paymentAmountRaw.value = value;
      e.target.value = parseInt(value).toLocaleString('id-ID');
      updateSummary(parseInt(value));
    } else {
      paymentAmountRaw.value = 0;
      e.target.value = '';
      updateSummary(0);
    }
  });

  // ── Pay Button ────────────────────────────────────────────────────────────

  payButton.addEventListener('click', function () {
    const amount = parseInt(paymentAmountRaw.value);

    if (amount < 10000) {
      alert('Minimal pembayaran Rp 10.000');
      return;
    }

    if (amount > sisaTagihan) {
      alert('Jumlah melebihi sisa tagihan!');
      return;
    }

    payButton.disabled = true;
    payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

    fetch(`/payment/create/${tagihanId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ amount })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        snap.pay(data.snap_token, {
          onSuccess: () => { window.location.href = `/payment/finish?order_id=${data.order_id}`; },
          onPending: () => { window.location.href = `/payment/finish?order_id=${data.order_id}`; },
          onError:   () => { window.location.href = `/payment/error?order_id=${data.order_id}`; },
          onClose:   () => {
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
    .catch(() => {
      alert('Terjadi kesalahan. Silakan coba lagi.');
      payButton.disabled = false;
      payButton.innerHTML = '<i class="bi bi-credit-card me-2"></i>Bayar Sekarang';
    });
  });
</script>
@endsection