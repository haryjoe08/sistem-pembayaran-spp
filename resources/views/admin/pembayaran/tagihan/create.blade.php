@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">
  
  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-plus-circle me-2 text-primary"></i>
            Tambah Tagihan Baru
          </h4>
          <p class="text-muted mb-0">Buat tagihan pembayaran untuk siswa atau kelas</p>
        </div>
        <div class="badge bg-light text-dark px-3 py-2">
          <i class="bi bi-calendar3 me-1"></i>
          {{ date('d F Y') }}
        </div>
      </div>
    </div>
  </div>

  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
          <h5 class="mb-0 fw-semibold">
            <i class="bi bi-file-earmark-text me-2"></i>
            Form Tagihan
          </h5>
        </div>

        <form action="{{ route('tagihan.store') }}" method="POST">
          @csrf
          <div class="card-body p-4">

            <!-- Step Indicator -->
            <div class="row mb-4">
              <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <div class="step-indicator active" id="step-1">
                    <div class="step-circle">1</div>
                    <span>Mode Tagihan</span>
                  </div>
                  <div class="step-line"></div>
                  <div class="step-indicator" id="step-2">
                    <div class="step-circle">2</div>
                    <span>Target</span>
                  </div>
                  <div class="step-line"></div>
                  <div class="step-indicator" id="step-3">
                    <div class="step-circle">3</div>
                    <span>Jenis & Nominal</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- MODE PILIH -->
            <div class="mb-4" id="mode-section">
              <label for="mode" class="form-label fw-semibold mb-3">
                <i class="bi bi-gear me-2"></i>
                Pilih Mode Tagihan <span class="text-danger">*</span>
              </label>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="mode-option" data-value="siswa">
                    <input type="radio" class="btn-check" name="mode" value="siswa" id="mode-siswa" {{ old('mode') == 'siswa' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary w-100 p-3 h-100" for="mode-siswa">
                      <div class="text-center">
                        <i class="bi bi-person display-6 d-block mb-2"></i>
                        <h6 class="mb-1">Per Siswa</h6>
                        <small class="text-muted">Buat tagihan untuk satu siswa tertentu</small>
                      </div>
                    </label>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="mode-option" data-value="kelas">
                    <input type="radio" class="btn-check" name="mode" value="kelas" id="mode-kelas" {{ old('mode') == 'kelas' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary w-100 p-3 h-100" for="mode-kelas">
                      <div class="text-center">
                        <i class="bi bi-people display-6 d-block mb-2"></i>
                        <h6 class="mb-1">Per Kelas</h6>
                        <small class="text-muted">Buat tagihan untuk seluruh siswa dalam kelas</small>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <!-- TARGET SECTION -->
            <div class="mb-4" id="target-section" style="display: none;">
              
              <!-- FORM SISWA -->
              <div class="mode-content mode-siswa" style="display: none;">
                <label class="form-label fw-semibold mb-3">
                  <i class="bi bi-person me-2"></i>
                  Pilih Siswa <span class="text-danger">*</span>
                </label>
                <div class="position-relative">
                  <select class="form-select form-select-lg @error('siswa_nis') is-invalid @enderror"
                    id="siswa_nis" name="siswa_nis">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswa as $s)
                      <option value="{{ $s->nis }}" {{ old('siswa_nis') == $s->nis ? 'selected' : '' }}>
                        {{ $s->nis }} - {{ $s->nama }} ({{ $s->kelas->kelas ?? 'Belum ada kelas' }})
                      </option>
                    @endforeach
                  </select>
                  <i class="bi bi-chevron-down position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                </div>
                @error('siswa_nis')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                  <i class="bi bi-info-circle me-1"></i>
                  Pilih siswa yang akan dibuatkan tagihan
                </div>
              </div>

              <!-- FORM KELAS -->
              <div class="mode-content mode-kelas" style="display: none;">
                <label class="form-label fw-semibold mb-3">
                  <i class="bi bi-people me-2"></i>
                  Pilih Kelas
                </label>
                <div class="position-relative">
                  <select class="form-select form-select-lg @error('kelas_id') is-invalid @enderror"
                    id="kelas_id" name="kelas_id">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelas as $k)
                      <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->kelas }}
                      </option>
                    @endforeach
                  </select>
                  <i class="bi bi-chevron-down position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                </div>
                @error('kelas_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                  <i class="bi bi-info-circle me-1"></i>
                  Kosongkan untuk membuat tagihan ke semua siswa di sekolah
                </div>
              </div>
            </div>

            <!-- PAYMENT TYPE SECTION -->
            <div class="mb-4" id="payment-section" style="display: none;">
              <label for="jenis_pembayaran_id" class="form-label fw-semibold mb-3">
                <i class="bi bi-credit-card me-2"></i>
                Jenis Pembayaran <span class="text-danger">*</span>
              </label>
              <div class="position-relative">
                <select class="form-select form-select-lg @error('jenis_pembayaran_id') is-invalid @enderror"
                  id="jenis_pembayaran_id" name="jenis_pembayaran_id">
                  <option value="">-- Pilih Jenis Pembayaran --</option>
                  @foreach($jenisPembayaran as $jp)
                    <option value="{{ $jp->id }}" data-nominal="{{ $jp->nominal }}"
                      {{ old('jenis_pembayaran_id') == $jp->id ? 'selected' : '' }}>
                      {{ $jp->nama }} (Rp{{ number_format($jp->nominal, 0, ',', '.') }})
                    </option>
                  @endforeach
                </select>
                <i class="bi bi-chevron-down position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
              </div>
              @error('jenis_pembayaran_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">
                <i class="bi bi-info-circle me-1"></i>
                Nominal akan otomatis terisi sesuai jenis pembayaran
              </div>

              <!-- JATUH TEMPO -->
              <div class="mb-4">
                <label for="jatuh_tempo" class="form-label fw-semibold">
                  <i class="bi bi-calendar-event me-2"></i>
                  Tanggal Jatuh Tempo (Opsional)
                </label>
                <input type="date" 
                       class="form-control form-control-lg" 
                       id="jatuh_tempo" 
                       name="jatuh_tempo"
                       value="{{ old('jatuh_tempo', now()->addDays(30)->format('Y-m-d')) }}">
                <div class="form-text">
                  <i class="bi bi-info-circle me-1"></i>
                  Default: 30 hari dari sekarang. Kosongkan untuk menggunakan default.
                </div>
              </div>

              <!-- NOMINAL PREVIEW -->
              <div class="mt-4" id="nominal-preview" style="display: none;">
                <div class="alert alert-info d-flex align-items-center">
                  <i class="bi bi-calculator me-3 fs-4"></i>
                  <div>
                    <h6 class="mb-1">Preview Nominal</h6>
                    <div class="d-flex align-items-center">
                      <span class="text-muted me-2">Total Tagihan:</span>
                      <span class="fw-bold h5 mb-0 text-primary" id="total_tagihan_display">Rp 0</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- SUMMARY SECTION -->
            <div class="mt-4" id="summary-section" style="display: none;">
              <div class="card bg-light border-0">
                <div class="card-body">
                  <h6 class="card-title mb-3">
                    <i class="bi bi-check-circle me-2"></i>
                    Ringkasan Tagihan
                  </h6>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="summary-item">
                        <span class="text-muted">Mode:</span>
                        <span class="fw-semibold" id="summary-mode">-</span>
                      </div>
                      <div class="summary-item">
                        <span class="text-muted">Target:</span>
                        <span class="fw-semibold" id="summary-target">-</span>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="summary-item">
                        <span class="text-muted">Jenis Pembayaran:</span>
                        <span class="fw-semibold" id="summary-payment">-</span>
                      </div>
                      <div class="summary-item">
                        <span class="text-muted">Nominal:</span>
                        <span class="fw-semibold text-primary" id="summary-nominal">-</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between">
              <a href="{{ route('tagihan.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
              </a>
              <button type="submit" class="btn btn-primary px-4" id="submit-btn" disabled>
                <i class="bi bi-check-circle me-1"></i>
                Simpan Tagihan
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
/* Step Indicator */
.step-indicator {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  position: relative;
}

.step-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: #6c757d;
  margin-bottom: 8px;
  transition: all 0.3s ease;
}

.step-indicator.active .step-circle {
  background: #0d6efd;
  color: white;
}

.step-indicator.completed .step-circle {
  background: #198754;
  color: white;
}

.step-indicator span {
  font-size: 0.875rem;
  text-align: center;
  color: #6c757d;
  font-weight: 500;
}

.step-indicator.active span,
.step-indicator.completed span {
  color: #212529;
  font-weight: 600;
}

.step-line {
  height: 2px;
  background: #e9ecef;
  flex: 1;
  margin: 0 20px;
  margin-top: 20px;
  transition: all 0.3s ease;
}

.step-line.completed {
  background: #198754;
}

/* Mode Options */
.mode-option {
  height: 100%;
}

.mode-option .btn {
  transition: all 0.3s ease;
  border: 2px solid #e9ecef;
}

.mode-option .btn:hover {
  border-color: #0d6efd;
  background: rgba(13, 110, 253, 0.1);
}

.btn-check:checked + .btn {
  background: #0d6efd !important;
  border-color: #0d6efd !important;
  color: white !important;
}

/* Form Styling */
.form-select-lg {
  padding: 12px 16px;
  font-size: 1rem;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  padding-bottom: 8px;
  border-bottom: 1px solid #dee2e6;
}

.summary-item:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

/* Animation */
.fade-in {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 768px) {
  .step-indicator {
    margin-bottom: 20px;
  }
  
  .step-line {
    display: none;
  }
  
  .container-fluid {
    padding-left: 15px;
    padding-right: 15px;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const modeInputs = document.querySelectorAll('input[name="mode"]');
  const targetSection = document.getElementById('target-section');
  const paymentSection = document.getElementById('payment-section');
  const summarySection = document.getElementById('summary-section');
  const nominalPreview = document.getElementById('nominal-preview');
  const submitBtn = document.getElementById('submit-btn');
  
  const siswaContent = document.querySelector('.mode-siswa');
  const kelasContent = document.querySelector('.mode-kelas');
  
  const siswaSelect = document.getElementById('siswa_nis');
  const kelasSelect = document.getElementById('kelas_id');
  const paymentSelect = document.getElementById('jenis_pembayaran_id');
  
  // Step indicators
  const step1 = document.getElementById('step-1');
  const step2 = document.getElementById('step-2');
  const step3 = document.getElementById('step-3');
  const stepLines = document.querySelectorAll('.step-line');
  
  // Summary elements
  const summaryMode = document.getElementById('summary-mode');
  const summaryTarget = document.getElementById('summary-target');
  const summaryPayment = document.getElementById('summary-payment');
  const summaryNominal = document.getElementById('summary-nominal');
  const totalDisplay = document.getElementById('total_tagihan_display');
  
  let currentStep = 1;
  
  // Update step indicators
  function updateStepIndicator(step) {
    // Reset all
    [step1, step2, step3].forEach(s => {
      s.classList.remove('active', 'completed');
    });
    stepLines.forEach(line => line.classList.remove('completed'));
    
    // Mark completed steps
    for (let i = 1; i < step; i++) {
      document.getElementById(`step-${i}`).classList.add('completed');
      if (i < 3) stepLines[i-1].classList.add('completed');
    }
    
    // Mark current step
    if (step <= 3) {
      document.getElementById(`step-${step}`).classList.add('active');
    }
  }
  
  // Mode selection
  modeInputs.forEach(input => {
    input.addEventListener('change', function() {
      if (this.checked) {
        // Show target section
        targetSection.style.display = 'block';
        targetSection.classList.add('fade-in');
        
        // Hide all mode contents
        siswaContent.style.display = 'none';
        kelasContent.style.display = 'none';
        
        // Show selected mode content
        const modeContent = document.querySelector(`.mode-${this.value}`);
        if (modeContent) {
          modeContent.style.display = 'block';
          modeContent.classList.add('fade-in');
        }
        
        currentStep = 2;
        updateStepIndicator(currentStep);
        updateSummary();
        checkFormCompletion();
      }
    });
  });
  
  // Target selection
  [siswaSelect, kelasSelect].forEach(select => {
    select.addEventListener('change', function() {
      if (this.value || this === kelasSelect) { // kelas bisa kosong (semua kelas)
        paymentSection.style.display = 'block';
        paymentSection.classList.add('fade-in');
        currentStep = 3;
        updateStepIndicator(currentStep);
        updateSummary();
        checkFormCompletion();
      }
    });
  });
  
  // Payment selection
  paymentSelect.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const nominal = selected.getAttribute('data-nominal');
    
    if (nominal) {
      totalDisplay.textContent = 'Rp ' + parseInt(nominal).toLocaleString('id-ID');
      nominalPreview.style.display = 'block';
      nominalPreview.classList.add('fade-in');
      
      summarySection.style.display = 'block';
      summarySection.classList.add('fade-in');
      
      updateStepIndicator(4); // All steps completed
      updateSummary();
      checkFormCompletion();
    } else {
      nominalPreview.style.display = 'none';
      summarySection.style.display = 'none';
    }
  });
  
  // Update summary
  function updateSummary() {
    const selectedMode = document.querySelector('input[name="mode"]:checked');
    if (selectedMode) {
      summaryMode.textContent = selectedMode.value === 'siswa' ? 'Per Siswa' : 'Per Kelas';
      
      if (selectedMode.value === 'siswa') {
        const siswaOption = siswaSelect.options[siswaSelect.selectedIndex];
        summaryTarget.textContent = siswaOption.value ? siswaOption.text : '-';
      } else {
        const kelasOption = kelasSelect.options[kelasSelect.selectedIndex];
        summaryTarget.textContent = kelasOption.value ? kelasOption.text : 'Semua Kelas';
      }
    }
    
    const paymentOption = paymentSelect.options[paymentSelect.selectedIndex];
    summaryPayment.textContent = paymentOption.value ? paymentOption.text.split(' (')[0] : '-';
    
    const nominal = paymentOption.getAttribute('data-nominal');
    summaryNominal.textContent = nominal ? 'Rp ' + parseInt(nominal).toLocaleString('id-ID') : '-';
  }
  
  // Check form completion
  function checkFormCompletion() {
    const selectedMode = document.querySelector('input[name="mode"]:checked');
    let targetSelected = false;
    let paymentSelected = paymentSelect.value !== '';
    
    if (selectedMode) {
      if (selectedMode.value === 'siswa') {
        targetSelected = siswaSelect.value !== '';
      } else {
        targetSelected = true; // kelas bisa kosong
      }
    }
    
    submitBtn.disabled = !(selectedMode && targetSelected && paymentSelected);
  }
  
  // Initialize on page load
  const checkedMode = document.querySelector('input[name="mode"]:checked');
  if (checkedMode) {
    checkedMode.dispatchEvent(new Event('change'));
  }
  
  // Auto-select target if old value exists
  if (siswaSelect.value) {
    siswaSelect.dispatchEvent(new Event('change'));
  }
  if (kelasSelect.value !== '' || document.querySelector('input[name="mode"]:checked')?.value === 'kelas') {
    kelasSelect.dispatchEvent(new Event('change'));
  }
  
  // Auto-select payment if old value exists
  if (paymentSelect.value) {
    paymentSelect.dispatchEvent(new Event('change'));
  }
});
</script>
@endsection