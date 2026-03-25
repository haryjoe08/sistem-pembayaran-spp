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
    <div class="col-lg-9">

      @if($errors->has('error'))
      <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Error!</strong> {{ $errors->first('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      @if($errors->any() && !$errors->has('error'))
      <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i>
        <strong>Perhatian!</strong> Ada beberapa kesalahan pada form:
        <ul class="mb-0 mt-2">
          @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
          <h5 class="mb-0 fw-semibold">
            <i class="bi bi-file-earmark-text me-2"></i>
            Form Tagihan
          </h5>
        </div>

        <form action="{{ route('tagihan.store') }}" method="POST" id="formTagihan">
          @csrf
          <div class="card-body p-4">

            <!-- Step Indicator -->
            <div class="row mb-4">
              <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <div class="step-indicator active" id="step-1">
                    <div class="step-circle">1</div>
                    <span>Mode</span>
                  </div>
                  <div class="step-line"></div>
                  <div class="step-indicator" id="step-2">
                    <div class="step-circle">2</div>
                    <span>Tahun Ajaran</span>
                  </div>
                  <div class="step-line"></div>
                  <div class="step-indicator" id="step-3">
                    <div class="step-circle">3</div>
                    <span>Target</span>
                  </div>
                  <div class="step-line"></div>
                  <div class="step-indicator" id="step-4">
                    <div class="step-circle">4</div>
                    <span>Pembayaran</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- STEP 1: MODE -->
            <div class="mb-4" id="mode-section">
              <label class="form-label fw-semibold mb-3">
                <i class="bi bi-gear me-2"></i>
                Pilih Mode Tagihan <span class="text-danger">*</span>
              </label>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="mode-option">
                    <input type="radio" class="btn-check" name="mode" value="siswa" id="mode-siswa"
                      {{ old('mode') == 'siswa' ? 'checked' : '' }}>
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
                  <div class="mode-option">
                    <input type="radio" class="btn-check" name="mode" value="kelas" id="mode-kelas"
                      {{ old('mode') == 'kelas' ? 'checked' : '' }}>
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
              @error('mode')
              <div class="text-danger small">{{ $message }}</div>
              @enderror
            </div>

            <!-- STEP 2: TAHUN AJARAN -->
            <div class="alert alert-info">
              <strong>ℹ️ Tahun Ajaran Aktif:</strong> {{ $tahunAjaranAktif->tahun ?? 'Belum ada' }}
              <br>
              <small>Tagihan akan otomatis dibuat untuk tahun ajaran yang sedang aktif.</small>
            </div>

            <!-- STEP 3: TARGET -->
            <div class="mb-4" id="target-section" style="display: none;">
              <div class="mode-content mode-siswa" style="display: none;">
                <label class="form-label fw-semibold mb-3">
                  <i class="bi bi-person me-2"></i>
                  Pilih Siswa <span class="text-danger">*</span>
                </label>
                <select class="form-select form-select-lg @error('siswa_nis') is-invalid @enderror"
                  id="siswa_nis" name="siswa_nis">
                  <option value="">-- Pilih Siswa --</option>
                  @foreach($siswa->sortBy('nis') as $s)
                  <option value="{{ $s->nis }}" {{ old('siswa_nis') == $s->nis ? 'selected' : '' }}>
                    {{ $s->nis }} - {{ $s->nama }} ({{ $s->kelas->kelas ?? 'Belum ada kelas' }})
                  </option>
                  @endforeach
                </select>
                @error('siswa_nis')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mode-content mode-kelas" style="display: none;">
                <label class="form-label fw-semibold mb-3">
                  <i class="bi bi-people me-2"></i>
                  Pilih Kelas
                </label>
                <select class="form-select form-select-lg @error('kelas_id') is-invalid @enderror"
                  id="kelas_id" name="kelas_id">
                  <option value="">-- Semua Kelas --</option>
                  @foreach($kelas as $k)
                  <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                    {{ $k->kelas }}
                  </option>
                  @endforeach
                </select>
                @error('kelas_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                  <i class="bi bi-info-circle me-1"></i>
                  Kosongkan untuk membuat tagihan ke semua siswa aktif
                </div>
              </div>
            </div>

            <!-- STEP 4: PAYMENT -->
            <div class="mb-4" id="payment-section" style="display: none;">
              <label for="jenis_tagihan_id" class="form-label fw-semibold mb-3">
                <i class="bi bi-credit-card me-2"></i>
                Jenis Tagihan <span class="text-danger">*</span>
              </label>
              <select class="form-select form-select-lg @error('jenis_tagihan_id') is-invalid @enderror"
                id="jenis_tagihan_id" name="jenis_tagihan_id">
                <option value="">-- Pilih Jenis Tagihan --</option>
                @foreach($jenisTagihan as $jp)
                <option value="{{ $jp->id }}" {{ old('jenis_tagihan_id') == $jp->id ? 'selected' : '' }}>
                  {{ $jp->nama }} ({{ ucfirst($jp->tipe) }})
                </option>
                @endforeach
              </select>
              @error('jenis_tagihan_id')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">
                <i class="bi bi-info-circle me-1"></i>
                Nominal akan otomatis dimuat berdasarkan tarif yang sudah diset
              </div>

              <!-- LOADING TARIF -->
              <div class="mt-4" id="tarif-loading" style="display: none;">
                <div class="alert alert-info d-flex align-items-center">
                  <div class="spinner-border spinner-border-sm me-3" role="status"></div>
                  <span>Memuat tarif pembayaran...</span>
                </div>
              </div>

              <!-- NOMINAL PREVIEW -->
              <div class="mt-4" id="nominal-preview" style="display: none;">
                <div class="alert alert-success d-flex align-items-center">
                  <i class="bi bi-check-circle me-3 fs-4"></i>
                  <div class="flex-grow-1">
                    <h6 class="mb-1">Tarif Berhasil Dimuat</h6>
                    <div class="d-flex align-items-center">
                      <span class="text-muted me-2">Nominal Tagihan:</span>
                      <span class="fw-bold h5 mb-0 text-success" id="total_tagihan_display">Rp 0</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ERROR TARIF -->
              <div class="mt-4" id="tarif-error" style="display: none;">
                <div class="alert alert-danger d-flex align-items-center">
                  <i class="bi bi-exclamation-triangle me-3 fs-4"></i>
                  <div>
                    <h6 class="mb-1">Tarif Belum Diset!</h6>
                    <p class="mb-2 small">Tarif untuk Jenis Tagihan dan tahun ajaran ini belum tersedia.</p>
                    <a href="{{ route('tarif-tagihan.create') }}" class="btn btn-sm btn-danger" target="_blank">
                      <i class="bi bi-plus-circle me-1"></i> Set Tarif Sekarang
                    </a>
                  </div>
                </div>
              </div>

              <!-- PILIH BULAN (hanya muncul jika tipe = bulanan) -->
              <div class="mt-4" id="bulan-section" style="display: none;">
                <label class="form-label fw-semibold">
                  <i class="bi bi-calendar-month me-2"></i>
                  Bulan Tagihan <span class="text-danger">*</span>
                </label>
                <select class="form-select form-select-lg @error('bulan') is-invalid @enderror"
                  id="bulan" name="bulan">
                  <option value="">-- Pilih Bulan --</option>
                  @foreach(['Januari','Februari','Maret','April','Mei','Juni',
                  'Juli','Agustus','September','Oktober','November','Desember']
                  as $i => $namaBulan)
                  <option value="{{ $i + 1 }}" {{ old('bulan') == $i + 1 ? 'selected' : '' }}>
                    {{ $namaBulan }}
                  </option>
                  @endforeach
                </select>
                @error('bulan')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                  <i class="bi bi-info-circle me-1"></i>
                  Pilih bulan untuk tagihan SPP ini
                </div>
              </div>

              <!-- JATUH TEMPO MANUAL (non-bulanan) -->
              <div class="mt-4" id="jatuh-tempo-wrapper">
                <label for="jatuh_tempo" class="form-label fw-semibold">
                  <i class="bi bi-calendar-event me-2"></i>
                  Tanggal Jatuh Tempo
                </label>
                <input type="date"
                  class="form-control form-control-lg @error('jatuh_tempo') is-invalid @enderror"
                  id="jatuh_tempo"
                  name="jatuh_tempo"
                  value="{{ old('jatuh_tempo', now()->addDays(30)->format('Y-m-d')) }}">
                @error('jatuh_tempo')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                  <i class="bi bi-info-circle me-1"></i>
                  Default: 30 hari dari sekarang
                </div>
              </div>

              <!-- INFO JATUH TEMPO OTOMATIS (bulanan) -->
              <div class="mt-4" id="jatuh-tempo-auto" style="display: none;">
                <div class="alert alert-secondary d-flex align-items-center">
                  <i class="bi bi-calendar-check me-3 fs-5 text-primary"></i>
                  <div>
                    <strong>Jatuh Tempo Otomatis</strong>
                    <div class="small mt-1">
                      Akan diset ke <strong>tanggal 10</strong> dari bulan yang dipilih.
                      <span id="jatuh-tempo-preview" class="text-primary fw-semibold ms-1"></span>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- SUMMARY -->
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
                        <span class="text-muted">Tahun Ajaran:</span>
                        <span class="fw-semibold" id="summary-tahun">-</span>
                      </div>
                      <div class="summary-item">
                        <span class="text-muted">Target:</span>
                        <span class="fw-semibold" id="summary-target">-</span>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="summary-item">
                        <span class="text-muted">Jenis Tagihan:</span>
                        <span class="fw-semibold" id="summary-payment">-</span>
                      </div>
                      <div class="summary-item" id="summary-bulan-row" style="display:none;">
                        <span class="text-muted">Bulan:</span>
                        <span class="fw-semibold" id="summary-bulan">-</span>
                      </div>
                      <div class="summary-item">
                        <span class="text-muted">Jatuh Tempo:</span>
                        <span class="fw-semibold" id="summary-jatuh-tempo">-</span>
                      </div>
                      <div class="summary-item border-0">
                        <span class="text-muted">Nominal:</span>
                        <span class="fw-bold text-success fs-5" id="summary-nominal">-</span>
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
                <i class="bi bi-arrow-left me-1"></i> Kembali
              </a>
              <button type="submit" class="btn btn-primary px-4" id="submit-btn" disabled>
                <i class="bi bi-check-circle me-1"></i> Simpan Tagihan
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
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
    margin: 0 10px;
    margin-top: 20px;
    transition: all 0.3s ease;
  }

  .step-line.completed {
    background: #198754;
  }

  .mode-option .btn {
    transition: all 0.3s ease;
    border: 2px solid #e9ecef;
  }

  .mode-option .btn:hover {
    border-color: #0d6efd;
    background: rgba(13, 110, 253, 0.1);
  }

  .btn-check:checked+.btn {
    background: #0d6efd !important;
    border-color: #0d6efd !important;
    color: white !important;
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
  }

  .fade-in {
    animation: fadeIn 0.3s ease-in;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {

    // ===================== ELEMENTS =====================
    const modeInputs = document.querySelectorAll('input[name="mode"]');
    const targetSection = document.getElementById('target-section');
    const paymentSection = document.getElementById('payment-section');
    const summarySection = document.getElementById('summary-section');
    const submitBtn = document.getElementById('submit-btn');
    const siswaContent = document.querySelector('.mode-siswa');
    const kelasContent = document.querySelector('.mode-kelas');
    const siswaSelect = document.getElementById('siswa_nis');
    const kelasSelect = document.getElementById('kelas_id');
    const paymentSelect = document.getElementById('jenis_tagihan_id');
    const jatuhTempoInput = document.getElementById('jatuh_tempo');
    const bulanSelect = document.getElementById('bulan');
    const bulanSection = document.getElementById('bulan-section');
    const jatuhTempoWrapper = document.getElementById('jatuh-tempo-wrapper');
    const jatuhTempoAuto = document.getElementById('jatuh-tempo-auto');
    const jatuhTempoPreview = document.getElementById('jatuh-tempo-preview');
    const tarifLoading = document.getElementById('tarif-loading');
    const nominalPreview = document.getElementById('nominal-preview');
    const tarifError = document.getElementById('tarif-error');
    const totalDisplay = document.getElementById('total_tagihan_display');

    const tahunAjaranAktif = "{{ $tahunAjaranAktif->id ?? '' }}";
    const tahunAjaranNama = "{{ $tahunAjaranAktif->tahun ?? '-' }}";

    // Ambil tahun pertama dari format "2025/2026" → 2025
    const tahunAjaranString = "{{ $tahunAjaranAktif->tahun ?? date('Y') }}";

    const [tahunAwal, tahunAkhir] = tahunAjaranString.includes('/') ?
      tahunAjaranString.split('/') : [tahunAjaranString, tahunAjaranString];

    const TAHUN_AWAL = parseInt(tahunAwal);
    const TAHUN_AKHIR = parseInt(tahunAkhir);


    const NAMA_BULAN = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    function getTahunDariBulan(bulan) {
      bulan = parseInt(bulan);

      // Juli (7) – Desember (12) → tahun pertama
      if (bulan >= 7) {
        return TAHUN_AWAL;
      }

      // Januari (1) – Juni (6) → tahun kedua
      return TAHUN_AKHIR;
    }

    let currentTarif = null;
    let currentStep = 1;

    const steps = [1, 2, 3, 4].map(i => document.getElementById(`step-${i}`));
    const stepLines = document.querySelectorAll('.step-line');

    // ===================== STEP INDICATOR =====================
    function updateStepIndicator(step) {
      steps.forEach((s, i) => {
        s.classList.remove('active', 'completed');
        if (i < step - 1) s.classList.add('completed');
        else if (i === step - 1) s.classList.add('active');
      });
      stepLines.forEach((line, i) => line.classList.toggle('completed', i < step - 1));
    }

    // ===================== JATUH TEMPO MODE =====================
    function applyJatuhTempoMode(tipe) {
      if (tipe === 'bulanan') {
        // Sembunyikan input manual, tampilkan info otomatis
        jatuhTempoWrapper.style.display = 'none';
        jatuhTempoAuto.style.display = 'block';
        updateJatuhTempoPreview(); // update preview sesuai bulan yg terpilih
      } else {
        // Tampilkan input manual, sembunyikan info otomatis
        jatuhTempoWrapper.style.display = 'block';
        jatuhTempoAuto.style.display = 'none';
        jatuhTempoPreview.textContent = '';
        // Reset ke default 30 hari dari sekarang
        const d = new Date();
        d.setDate(d.getDate() + 30);
        jatuhTempoInput.value = d.toISOString().split('T')[0];
      }
    }

    function updateJatuhTempoPreview() {

      if (!bulanSelect.value || !currentTarif || currentTarif.tipe !== 'bulanan') {
        return;
      }

      const bulan = parseInt(bulanSelect.value);
      const tahun = getTahunDariBulan(bulan);

      const jatuhTempo = `${tahun}-${String(bulan).padStart(2, '0')}-10`;

      jatuhTempoInput.value = jatuhTempo;
    }


    // ===================== MODE SELECTION =====================
    modeInputs.forEach(input => {
      input.addEventListener('change', function() {
        if (!this.checked) return;

        targetSection.style.display = 'block';
        targetSection.classList.add('fade-in');

        siswaContent.style.display = 'none';
        kelasContent.style.display = 'none';

        const modeContent = document.querySelector(`.mode-${this.value}`);
        if (modeContent) {
          modeContent.style.display = 'block';
          modeContent.classList.add('fade-in');
        }

        currentStep = 3;
        updateStepIndicator(currentStep);

        if (this.value === 'kelas') {
          setTimeout(() => {
            paymentSection.style.display = 'block';
            paymentSection.classList.add('fade-in');
            currentStep = 4;
            updateStepIndicator(currentStep);
            updateSummary();
            checkFormCompletion();
          }, 150);
        } else {
          paymentSection.style.display = 'none';
          resetTarif();
        }

        updateSummary();
        checkFormCompletion();
      });
    });

    // ===================== TARGET SELECTION =====================
    [siswaSelect, kelasSelect].forEach(select => {
      select.addEventListener('change', function() {
        const selectedMode = document.querySelector('input[name="mode"]:checked');
        const isValid = selectedMode && (
          (selectedMode.value === 'siswa' && siswaSelect.value) ||
          (selectedMode.value === 'kelas')
        );
        if (isValid) {
          paymentSection.style.display = 'block';
          paymentSection.classList.add('fade-in');
          currentStep = 4;
          updateStepIndicator(currentStep);
          updateSummary();
        }
        checkFormCompletion();
      });
    });

    // ===================== PAYMENT SELECTION =====================
    paymentSelect.addEventListener('change', function() {
      this.value && tahunAjaranAktif ? loadTarif() : resetTarif();
    });

    // ===================== BULAN SELECTION =====================
    bulanSelect.addEventListener('change', function() {
      updateJatuhTempoPreview();
      updateSummary();
      checkFormCompletion();
    });

    // ===================== LOAD TARIF =====================
    function loadTarif() {
      tarifLoading.style.display = 'block';
      nominalPreview.style.display = 'none';
      tarifError.style.display = 'none';
      summarySection.style.display = 'none';
      bulanSection.style.display = 'none';
      bulanSelect.value = '';
      currentTarif = null;
      applyJatuhTempoMode('non-bulanan'); // reset dulu ke mode manual

      fetch(`{{ route('tagihan.get-tarif') }}?jenis_tagihan_id=${paymentSelect.value}&tahun_ajaran_id=${tahunAjaranAktif}`)
        .then(r => r.json())
        .then(data => {
          tarifLoading.style.display = 'none';

          if (data.success) {
            currentTarif = data;
            totalDisplay.textContent = data.nominal_format;
            nominalPreview.style.display = 'block';
            nominalPreview.classList.add('fade-in');
            summarySection.style.display = 'block';
            summarySection.classList.add('fade-in');

            if (data.tipe === 'bulanan') {
              bulanSection.style.display = 'block';
              bulanSection.classList.add('fade-in');
              applyJatuhTempoMode('bulanan');
            } else {
              bulanSection.style.display = 'none';
              bulanSelect.value = '';
              applyJatuhTempoMode('non-bulanan');
            }

            updateSummary();
            checkFormCompletion();
          } else {
            tarifError.style.display = 'block';
            tarifError.classList.add('fade-in');
            checkFormCompletion();
          }
        })
        .catch(err => {
          console.error('Error:', err);
          tarifLoading.style.display = 'none';
          tarifError.style.display = 'block';
          checkFormCompletion();
        });
    }

    // ===================== RESET TARIF =====================
    function resetTarif() {
      currentTarif = null;
      tarifLoading.style.display = 'none';
      nominalPreview.style.display = 'none';
      tarifError.style.display = 'none';
      summarySection.style.display = 'none';
      bulanSection.style.display = 'none';
      bulanSelect.value = '';
      applyJatuhTempoMode('non-bulanan');
      checkFormCompletion();
    }

    // ===================== UPDATE SUMMARY =====================
    function updateSummary() {
      const selectedMode = document.querySelector('input[name="mode"]:checked');

      if (selectedMode) {
        document.getElementById('summary-mode').textContent =
          selectedMode.value === 'siswa' ? 'Per Siswa' : 'Per Kelas';

        if (selectedMode.value === 'siswa') {
          const opt = siswaSelect.options[siswaSelect.selectedIndex];
          document.getElementById('summary-target').textContent = opt.value ? opt.text : '-';
        } else {
          const opt = kelasSelect.options[kelasSelect.selectedIndex];
          document.getElementById('summary-target').textContent = opt.value ? opt.text : 'Semua Kelas';
        }
      }

      document.getElementById('summary-tahun').textContent = tahunAjaranNama;

      const payOpt = paymentSelect.options[paymentSelect.selectedIndex];
      document.getElementById('summary-payment').textContent = payOpt.value ? payOpt.text : '-';

      // Bulan row
      const summaryBulanRow = document.getElementById('summary-bulan-row');
      if (currentTarif && currentTarif.tipe === 'bulanan') {
        summaryBulanRow.style.display = 'flex';
        const bulanOpt = bulanSelect.options[bulanSelect.selectedIndex];
        document.getElementById('summary-bulan').textContent = bulanOpt.value ? bulanOpt.text : '-';
      } else {
        summaryBulanRow.style.display = 'none';
      }

      // Jatuh tempo — parse manual supaya tidak geser karena timezone
      const jt = jatuhTempoInput.value;
      if (jt) {
        const [y, m, d] = jt.split('-');
        const tgl = new Date(y, m - 1, d);
        document.getElementById('summary-jatuh-tempo').textContent =
          tgl.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
          });
      } else {
        document.getElementById('summary-jatuh-tempo').textContent = '-';
      }

      document.getElementById('summary-nominal').textContent =
        currentTarif ? currentTarif.nominal_format : '-';
    }

    // ===================== CHECK FORM COMPLETION =====================
    function checkFormCompletion() {
      const selectedMode = document.querySelector('input[name="mode"]:checked');
      const paymentSelected = paymentSelect.value !== '';
      const tarifLoaded = currentTarif !== null;
      const bulanValid = !currentTarif ||
        currentTarif.tipe !== 'bulanan' ||
        bulanSelect.value !== '';

      let targetSelected = false;
      if (selectedMode) {
        targetSelected = selectedMode.value === 'siswa' ?
          siswaSelect.value !== '' :
          true;
      }

      submitBtn.disabled = !(selectedMode && targetSelected && paymentSelected && tarifLoaded && bulanValid);
    }

    // Jatuh tempo manual change
    jatuhTempoInput.addEventListener('change', updateSummary);

    // ===================== INIT ON PAGE LOAD =====================
    const checkedMode = document.querySelector('input[name="mode"]:checked');
    if (checkedMode) checkedMode.dispatchEvent(new Event('change'));

    if (siswaSelect.value || kelasSelect.value) {
      (siswaSelect.value ? siswaSelect : kelasSelect).dispatchEvent(new Event('change'));
    }

    if (paymentSelect.value && tahunAjaranAktif) loadTarif();
  });
</script>
@endsection