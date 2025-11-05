@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">
  
  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold text-dark mb-0">
          <i class="bi bi-credit-card-2-front me-2"></i>
          Pembayaran Siswa
        </h4>
        <div class="badge bg-light text-dark px-3 py-2">
          <i class="bi bi-calendar3 me-1"></i>
          {{ date('d F Y') }}
        </div>
      </div>
    </div>
  </div>

  <!-- Search Form -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <div class="row align-items-center">
            <div class="col-md-8">
              <form action="{{ route('pembayaran.cari') }}" method="GET" class="d-flex">
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-search text-muted"></i>
                  </span>
                  <input type="text" 
                         name="keyword" 
                         class="form-control border-start-0 ps-0" 
                         placeholder="Masukkan NIS atau Nama Siswa..." 
                         value="{{ request('keyword') }}"
                         required>
                  <button type="submit" class="btn btn-primary px-4">
                    Cari Data
                  </button>
                </div>
              </form>
            </div>
            <div class="col-md-4 text-end">
              <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Gunakan NIS atau nama lengkap siswa
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @isset($siswa)
  <!-- Student Identity Card -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body p-4 text-white">
          <div class="row align-items-center">
            <div class="col-auto">
              <div class="avatar-wrapper">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($siswa->nama) }}&background=ffffff&color=667eea&size=80&bold=true" 
                     class="rounded-circle border border-3 border-white shadow" 
                     width="80" height="80" 
                     alt="Avatar {{ $siswa->nama }}">
              </div>
            </div>
            <div class="col text-black">
              <h4 class="fw-bold mb-1">{{ $siswa->nama }}</h4>
              <div class="row">
                <div class="col-md-4">
                  <small class="opacity-75">Nomor Induk Siswa</small>
                  <div class="fw-semibold">{{ $siswa->nis }}</div>
                </div>
                <div class="col-md-4">
                  <small class="opacity-75">Kelas</small>
                  <div class="fw-semibold">{{ $siswa->kelas->kelas }}</div>
                </div>
                <div class="col-md-4">
                  <small class="opacity-75">Total Tagihan</small>
                  <div class="fw-semibold">
                    {{ $siswa->tagihan->count() }} Item
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Payment Bills -->
  <div class="row">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
              <i class="bi bi-list-check me-2 text-primary"></i>
              Daftar Tagihan
            </h5>
            <div class="d-flex gap-2">
              @php
                $lunas = $siswa->tagihan->where('status', 'lunas')->count();
                $belum = $siswa->tagihan->where('status', '!=', 'lunas')->count();
              @endphp
              <span class="badge bg-success">{{ $lunas }} Lunas</span>
              <span class="badge bg-warning">{{ $belum }} Belum Lunas</span>
            </div>
          </div>
        </div>
        
        <div class="card-body p-0">
          @forelse($siswa->tagihan as $index => $t)
            @php
              $sisa = $t->total_tagihan - $t->sudah_dibayar;
              $persentase = $t->total_tagihan > 0 ? ($t->sudah_dibayar / $t->total_tagihan) * 100 : 0;
            @endphp
            
            <div class="border-bottom p-4 {{ $index % 2 == 0 ? 'bg-light bg-opacity-25' : '' }}">
              <div class="row align-items-center">
                <div class="col-md-4">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                      <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                        <i class="bi bi-receipt text-primary"></i>
                      </div>
                    </div>
                    <div>
                      <h6 class="fw-bold mb-1">{{ $t->jenisPembayaran->nama }}</h6>
                      <small class="text-muted">Kode: {{ $t->id }}</small>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3">
                  <div class="text-center">
                    <small class="text-muted d-block">Total Tagihan</small>
                    <span class="fw-bold h6 mb-0">Rp{{ number_format($t->total_tagihan, 0, ',', '.') }}</span>
                  </div>
                </div>
                
                <div class="col-md-3">
                  <div class="text-center">
                    <small class="text-muted d-block">Progress Pembayaran</small>
                    <div class="progress mt-1 mb-1" style="height: 8px;">
                      <div class="progress-bar bg-{{ $t->status == 'lunas' ? 'success' : 'primary' }}" 
                           style="width: {{ $persentase ?? '0' }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format($persentase, 1) }}%</small>
                  </div>
                </div>
                
                <div class="col-md-2 text-end">
                  @if($t->status == 'lunas')
                    <span class="badge bg-success px-3 py-2">
                      <i class="bi bi-check-circle me-1"></i>
                      Lunas
                    </span>
                  @else
                    <div class="d-flex flex-column align-items-end">
                      <small class="text-muted">Sisa</small>
                      <span class="fw-bold text-danger">Rp{{ number_format($sisa, 0, ',', '.') }}</span>
                      <button class="btn btn-success btn-sm mt-2 payment-btn" 
                              data-tagihan-id="{{ $t->id }}"
                              data-nama="{{ $t->jenisPembayaran->nama }}"
                              data-sisa="{{ $sisa }}"
                              data-sisa-formatted="Rp{{ number_format($sisa, 0, ',', '.') }}">
                        <i class="bi bi-credit-card me-1"></i>
                        Bayar
                      </button>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          @empty
            <div class="text-center py-5">
              <div class="mb-3">
                <i class="bi bi-inbox display-1 text-muted"></i>
              </div>
              <h5 class="text-muted">Tidak Ada Tagihan</h5>
              <p class="text-muted mb-0">Siswa ini belum memiliki tagihan pembayaran</p>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
  @endisset

  @if(!isset($siswa) && request()->has('keyword'))
  <!-- No Results -->
  <div class="row">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
          <div class="mb-3">
            <i class="bi bi-search display-1 text-muted"></i>
          </div>
          <h5 class="text-muted">Data Tidak Ditemukan</h5>
          <p class="text-muted mb-3">
            Siswa dengan NIS atau nama "<strong>{{ request('keyword') }}</strong>" tidak ditemukan
          </p>
          <a href="{{ route('pembayaran.cari') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>
            Cari Lagi
          </a>
        </div>
      </div>
    </div>
  </div>
  @endif

</div>

<!-- Modal Pembayaran -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="paymentModalLabel">
          <i class="bi bi-credit-card me-2"></i>
          Proses Pembayaran
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <form id="paymentForm" method="POST" action="">
        @csrf
        <div class="modal-body p-4">
          <div class="alert alert-info d-flex align-items-center">
            <i class="bi bi-info-circle me-2"></i>
            <div>
              <strong>Jenis Pembayaran:</strong> <span id="payment-type"></span>
            </div>
          </div>
          
          <div class="row mb-3">
            <div class="col-6">
              <label class="form-label text-muted">Sisa Tagihan</label>
              <div class="h5 text-danger fw-bold" id="remaining-amount"></div>
            </div>
            <div class="col-6">
              <label class="form-label text-muted">Status</label>
              <div><span class="badge bg-warning">Belum Lunas</span></div>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="payment-amount" class="form-label fw-semibold">
              Jumlah Pembayaran <span class="text-danger">*</span>
            </label>
            <div class="input-group input-group-lg">
              <span class="input-group-text bg-light fw-semibold">Rp</span>
              <input type="text" 
                     id="payment-amount" 
                     name="jumlah_display" 
                     class="form-control" 
                     placeholder="0"
                     style="font-size: 1.1rem; font-weight: 600; text-align: left;"
                     required>
              <input type="hidden" id="payment-amount-raw" name="jumlah">
            </div>
            <div class="form-text">
              <i class="bi bi-info-circle me-1"></i>
              Masukkan jumlah yang akan dibayar (maksimal sesuai sisa tagihan)
            </div>
          </div>
          
          <div class="mb-3">
            <label for="payment-note" class="form-label fw-semibold">Catatan</label>
            <textarea id="payment-note" 
                      name="catatan" 
                      class="form-control" 
                      rows="3" 
                      placeholder="Catatan tambahan (opsional)"></textarea>
          </div>
        </div>
        
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>
            Batal
          </button>
          <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i>
            Proses Pembayaran
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
/* Fix modal backdrop issue */
.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5) !important;
}

.modal {
  background-color: transparent !important;
}

/* Custom styling */
.progress {
  border-radius: 10px;
  overflow: hidden;
}

.progress-bar {
  transition: width 0.6s ease;
}

.input-group-text {
  border-right: none !important;
  background-color: #f8f9fa !important;
}

.form-control:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Animation untuk modal */
.modal.fade .modal-dialog {
  transition: transform 0.3s ease-out;
  transform: translate(0, -50px);
}

.modal.show .modal-dialog {
  transform: none;
}

/* Responsive */
@media (max-width: 768px) {
  .container-fluid {
    padding-left: 15px;
    padding-right: 15px;
  }
  
  .card-body {
    padding: 20px !important;
  }
  
  .modal-dialog {
    margin: 10px;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Handle payment button clicks
  const paymentButtons = document.querySelectorAll('.payment-btn');
  const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
  const paymentForm = document.getElementById('paymentForm');
  const paymentType = document.getElementById('payment-type');
  const remainingAmount = document.getElementById('remaining-amount');
  const paymentAmountInput = document.getElementById('payment-amount');
  const paymentAmountRaw = document.getElementById('payment-amount-raw');
  let maxAmount = 0;
  
  paymentButtons.forEach(button => {
    button.addEventListener('click', function() {
      const tagihanId = this.getAttribute('data-tagihan-id');
      const nama = this.getAttribute('data-nama');
      const sisa = parseInt(this.getAttribute('data-sisa'));
      const sisaFormatted = this.getAttribute('data-sisa-formatted');
      
      // Store max amount
      maxAmount = sisa;
      
      // Set form action
      paymentForm.action = `{{ route('pembayaran.proses', '') }}/${tagihanId}`;
      
      // Fill modal content
      paymentType.textContent = nama;
      remainingAmount.textContent = sisaFormatted;
      
      // Clear inputs
      paymentAmountInput.value = '';
      paymentAmountRaw.value = '';
      
      // Show modal
      paymentModal.show();
    });
  });
  
  // Format rupiah function
  function formatRupiah(input) {
    // Remove all non-digit characters
    let value = input.value.replace(/\D/g, '');
    
    if (value) {
      // Store raw value
      paymentAmountRaw.value = value;
      
      // Format with thousand separators
      let formattedValue = parseInt(value).toLocaleString('id-ID');
      input.value = formattedValue;
    } else {
      paymentAmountRaw.value = '';
    }
  }
  
  // Apply formatting on input
  paymentAmountInput.addEventListener('input', function() {
    formatRupiah(this);
  });
  
  // Handle paste events
  paymentAmountInput.addEventListener('paste', function(e) {
    setTimeout(() => {
      formatRupiah(this);
    }, 10);
  });
  
  // Prevent non-numeric characters (except backspace, delete, arrow keys, etc.)
  paymentAmountInput.addEventListener('keydown', function(e) {
    // Allow: backspace, delete, tab, escape, enter
    if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
        // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
        (e.keyCode === 65 && e.ctrlKey === true) ||
        (e.keyCode === 67 && e.ctrlKey === true) ||
        (e.keyCode === 86 && e.ctrlKey === true) ||
        (e.keyCode === 88 && e.ctrlKey === true) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
      return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
      e.preventDefault();
    }
  });
  
  // Form validation
  paymentForm.addEventListener('submit', function(e) {
    const rawValue = paymentAmountRaw.value;
    const amount = parseInt(rawValue);
    
    if (!rawValue || amount <= 0) {
      e.preventDefault();
      alert('Jumlah pembayaran harus lebih dari 0');
      paymentAmountInput.focus();
      return;
    }
    
    if (amount > maxAmount) {
      e.preventDefault();
      alert('Jumlah pembayaran tidak boleh melebihi sisa tagihan (Rp ' + maxAmount.toLocaleString('id-ID') + ')');
      paymentAmountInput.focus();
      return;
    }
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Memproses...';
    submitBtn.disabled = true;
  });
  
  // Reset form when modal is hidden
  document.getElementById('paymentModal').addEventListener('hidden.bs.modal', function() {
    paymentForm.reset();
    paymentAmountInput.value = '';
    paymentAmountRaw.value = '';
    maxAmount = 0;
    
    const submitBtn = paymentForm.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Proses Pembayaran';
    submitBtn.disabled = false;
  });
});
</script>
@endsection