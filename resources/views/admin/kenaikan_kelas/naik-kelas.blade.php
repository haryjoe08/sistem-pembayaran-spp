@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">

  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-arrow-up-circle me-2 text-primary"></i>
            Naik Kelas
          </h4>
          <p class="text-muted mb-0">Proses kenaikan kelas siswa secara massal</p>
        </div>
        <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
      </div>
    </div>
  </div>

  <div class="row justify-content-center">
    <div class="col-lg-10">

      <!-- Form Naik Kelas -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-3">
          <h6 class="mb-0 fw-bold">
            <i class="bi bi-people me-2"></i>
            Pilih Siswa untuk Naik Kelas
          </h6>
        </div>

        <form action="{{ route('siswa.proses-naik-kelas') }}" method="POST" id="form-naik-kelas">
          @csrf

          <div class="card-body p-4">

            <!-- Info Alert -->
            <div class="alert alert-info d-flex align-items-start mb-4">
              <i class="bi bi-info-circle me-3 fs-5"></i>
              <div>
                <strong>Petunjuk:</strong>
                <ul class="mb-0 mt-2 ps-3">
                  <li>Pilih <strong>Kelas Asal</strong> untuk melihat daftar siswa</li>
                  <li>Pilih <strong>Kelas Tujuan</strong> untuk kenaikan kelas</li>
                  <li>Centang siswa yang akan dinaikkan kelasnya</li>
                  <li>Hanya siswa dengan status <span class="badge bg-success">Aktif</span> yang ditampilkan</li>
                  <li>Opsi <strong>Lulus</strong> hanya tersedia untuk siswa kelas XII</li>
                </ul>
              </div>
            </div>

            <!-- Pilih Kelas Asal -->
            <div class="row mb-4">
              <div class="col-md-6 mb-3">
                <label for="kelas_asal_id" class="form-label fw-bold">
                  Kelas Asal <span class="text-danger">*</span>
                </label>
                <select class="form-select form-select-lg @error('kelas_asal_id') is-invalid @enderror"
                  id="kelas_asal_id"
                  name="kelas_asal_id"
                  required>
                  <option value="">-- Pilih Kelas Asal --</option>
                  @foreach($kelas as $k)
                  <option value="{{ $k->id }}" data-kelas-nama="{{ $k->kelas }}">{{ $k->kelas }}</option>
                  @endforeach
                </select>
                @error('kelas_asal_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Pilih Tipe Aksi -->
            <div class="row mb-4 m-3">
              <div class="col-12">
                <label class="form-label fw-bold mb-3">
                  Tipe Aksi <span class="text-danger">*</span>
                </label>

                <div class="d-flex gap-3">

                  <div class="flex-fill">
                    <input type="radio"
                      class="btn-check"
                      name="action_type"
                      id="action_naik_kelas"
                      value="naik_kelas"
                      checked
                      onchange="toggleKelasTujuan()">

                    <label class="btn btn-outline-primary p-3 text-start w-100" for="action_naik_kelas">
                      <i class="bi bi-arrow-up-circle fs-5 me-2"></i>
                      <div class="fw-bold">Naik Kelas</div>
                      <small>Pindahkan siswa ke kelas lain</small>
                    </label>
                  </div>

                  <div class="flex-fill">
                    <input type="radio"
                      class="btn-check"
                      name="action_type"
                      id="action_lulus"
                      value="lulus"
                      onchange="toggleKelasTujuan()"
                      disabled>

                    <label class="btn btn-outline-success p-3 text-start w-100" for="action_lulus" id="label_lulus">
                      <i class="bi bi-mortarboard fs-5 me-2"></i>
                      <div class="fw-bold">Lulus</div>
                      <small>Ubah status siswa jadi lulus (Hanya untuk kelas XII)</small>
                    </label>
                  </div>

                </div>
              </div>
            </div>


            <!-- Kelas Tujuan (hanya untuk naik kelas) -->
            <div class="row mb-4" id="kelas-tujuan-section">
              <div class="col-md-6 mb-3">
                <label for="kelas_tujuan_id" class="form-label fw-bold">
                  Kelas Tujuan <span class="text-danger">*</span>
                </label>
                <select class="form-select form-select-lg @error('kelas_tujuan_id') is-invalid @enderror"
                  id="kelas_tujuan_id"
                  name="kelas_tujuan_id">
                  <option value="">-- Pilih Kelas Tujuan --</option>
                  @foreach($kelas as $k)
                  <option value="{{ $k->id }}">{{ $k->kelas }}</option>
                  @endforeach
                </select>
                @error('kelas_tujuan_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Daftar Siswa -->
            <div id="siswa-section" style="display: none;">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">
                  <i class="bi bi-list-check me-2"></i>
                  Daftar Siswa (<span id="total-siswa">0</span> siswa)
                </h6>
                <div>
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                    <i class="bi bi-check-all me-1"></i> Pilih Semua
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                    <i class="bi bi-x-square me-1"></i> Batal Pilih
                  </button>
                </div>
              </div>

              <!-- Loading -->
              <div id="loading" class="text-center py-5" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-2">Memuat data siswa...</p>
              </div>

              <!-- Error Message -->
              <div id="error-message" class="alert alert-danger" style="display: none;">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <span id="error-text"></span>
              </div>

              <!-- Siswa List -->
              <div id="siswa-list" class="row"></div>

              <!-- No Data -->
              <div id="no-data" class="alert alert-warning text-center" style="display: none;">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                <p class="mb-0">Tidak ada siswa aktif di kelas ini</p>
              </div>

              <!-- Selected Count -->
              <div id="selected-info" class="alert alert-primary border mt-3" style="display: none;">
                <div class="d-flex justify-content-between align-items-center">
                  <span>
                    <i class="bi bi-check-circle text-primary me-2"></i>
                    <strong><span id="selected-count">0</span> siswa dipilih</strong>
                  </span>
                  <button type="submit" class="btn btn-primary" id="btn-submit" disabled>
                    <i class="bi bi-arrow-up-circle me-1"></i>
                    <span id="btn-submit-text">Proses Naik Kelas</span>
                  </button>
                </div>
              </div>
            </div>

          </div>
        </form>
      </div>

    </div>
  </div>

</div>

@if(session('success'))
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {

    /* =======================
       Bootstrap Tooltip Init
    ======================== */
    const tooltipTriggerList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    /* =======================
       Toastr Success Message
    ======================== */
    toastr.options = {
      closeButton: true,
      progressBar: true,
      positionClass: "toast-top-right",
      timeOut: "3000"
    };
    toastr.success("{{ session('success') }}");

  });
</script>
@endpush
@endif


@push('scripts')
<script>
  // CSRF Token
  const csrfToken = '{{ csrf_token() }}';

  // Toggle kelas tujuan visibility
  function toggleKelasTujuan() {
    const actionType = document.querySelector('input[name="action_type"]:checked').value;
    const kelasTujuanSection = document.getElementById('kelas-tujuan-section');
    const kelasTujuanSelect = document.getElementById('kelas_tujuan_id');
    const btnSubmitText = document.getElementById('btn-submit-text');

    if (actionType === 'lulus') {
      kelasTujuanSection.style.display = 'none';
      kelasTujuanSelect.removeAttribute('required');
      kelasTujuanSelect.value = '';
      btnSubmitText.innerHTML = '<i class="bi bi-mortarboard me-1"></i> Proses Kelulusan';
    } else {
      kelasTujuanSection.style.display = 'block';
      kelasTujuanSelect.setAttribute('required', 'required');
      btnSubmitText.innerHTML = '<i class="bi bi-arrow-up-circle me-1"></i> Proses Naik Kelas';
    }
  }

  // Check if kelas is XII
  function isKelasXII(kelasNama) {
    return kelasNama && kelasNama.toUpperCase().includes('XII');
  }

  // Toggle Lulus option based on selected kelas
  function toggleLulusOption() {
    const kelasAsalSelect = document.getElementById('kelas_asal_id');
    const selectedOption = kelasAsalSelect.options[kelasAsalSelect.selectedIndex];
    const kelasNama = selectedOption.getAttribute('data-kelas-nama') || '';
    
    const actionLulusRadio = document.getElementById('action_lulus');
    const labelLulus = document.getElementById('label_lulus');
    const actionNaikKelasRadio = document.getElementById('action_naik_kelas');

    if (isKelasXII(kelasNama)) {
      // Enable lulus option for kelas XII
      actionLulusRadio.disabled = false;
      labelLulus.style.opacity = '1';
      labelLulus.style.cursor = 'pointer';
    } else {
      // Disable lulus option for non-XII classes
      actionLulusRadio.disabled = true;
      labelLulus.style.opacity = '0.5';
      labelLulus.style.cursor = 'not-allowed';
      
      // If lulus was selected, switch back to naik kelas
      if (actionLulusRadio.checked) {
        actionNaikKelasRadio.checked = true;
        toggleKelasTujuan();
      }
    }
  }

  // Load siswa when kelas asal selected
  document.getElementById('kelas_asal_id').addEventListener('change', function () {
    const kelasId = this.value;
    
    // Toggle lulus option based on selected kelas
    toggleLulusOption();
    
    if (kelasId) {
      loadSiswa(kelasId);
    } else {
      document.getElementById('siswa-section').style.display = 'none';
    }
  });

  // Load siswa from server
  function loadSiswa(kelasId) {
    const siswaSection = document.getElementById('siswa-section');
    const loading = document.getElementById('loading');
    const siswaList = document.getElementById('siswa-list');
    const noData = document.getElementById('no-data');
    const errorMessage = document.getElementById('error-message');

    siswaSection.style.display = 'block';
    loading.style.display = 'block';
    siswaList.innerHTML = '';
    noData.style.display = 'none';
    errorMessage.style.display = 'none';
    document.getElementById('selected-info').style.display = 'none';

    const url = "{{ route('siswa.by-kelas', ['kelasId' => ':kelasId']) }}"
      .replace(':kelasId', kelasId);

    fetch(url)
      .then(response => {
        if (!response.ok) {
          throw new Error('HTTP ' + response.status);
        }
        return response.json();
      })
      .then(data => {
        loading.style.display = 'none';

        if (data.success && data.data.length > 0) {
          renderSiswa(data.data);
          document.getElementById('total-siswa').textContent = data.count;
        } else {
          noData.style.display = 'block';
          document.getElementById('total-siswa').textContent = '0';
        }
      })
      .catch(error => {
        loading.style.display = 'none';
        errorMessage.style.display = 'block';
        document.getElementById('error-text').textContent =
          'Gagal memuat data siswa: ' + error.message;
      });
  }

  // Render siswa cards
  function renderSiswa(siswa) {
    const siswaList = document.getElementById('siswa-list');
    siswaList.innerHTML = '';

    siswa.forEach(s => {
      const genderIcon = s.jenis_kelamin === 'L' ? 'bi-gender-male' : 'bi-gender-female';
      const genderBadge = s.jenis_kelamin === 'L' ? 'bg-primary' : 'bg-danger';
      const genderText = s.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';

      siswaList.innerHTML += `
        <div class="col-md-6 col-lg-4 mb-3">
          <div class="card border siswa-card h-100">
            <div class="card-body p-3">
              <div class="form-check">
                <input class="form-check-input siswa-checkbox"
                       type="checkbox"
                       name="siswa_ids[]"
                       value="${s.nis}"
                       onchange="updateSelectedCount()">
                <label class="form-check-label w-100">
                  <strong>${s.nama}</strong><br>
                  <small class="text-muted">NIS: ${s.nis}</small><br>
                  <span class="badge ${genderBadge} mt-1">
                    <i class="bi ${genderIcon} me-1"></i>${genderText}
                  </span>
                </label>
              </div>
            </div>
          </div>
        </div>`;
    });
  }

  function selectAll() {
    document.querySelectorAll('.siswa-checkbox').forEach(cb => cb.checked = true);
    updateSelectedCount();
  }

  function deselectAll() {
    document.querySelectorAll('.siswa-checkbox').forEach(cb => cb.checked = false);
    updateSelectedCount();
  }

  function updateSelectedCount() {
    const checked = document.querySelectorAll('.siswa-checkbox:checked').length;
    document.getElementById('selected-count').textContent = checked;

    const info = document.getElementById('selected-info');
    const btn = document.getElementById('btn-submit');

    if (checked > 0) {
      info.style.display = 'block';
      btn.disabled = false;
    } else {
      info.style.display = 'none';
      btn.disabled = true;
    }
  }

  // Initialize on page load
  document.addEventListener('DOMContentLoaded', function() {
    toggleLulusOption();
  });
</script>
@endpush


<style>
  .siswa-card {
    transition: all 0.2s;
    cursor: pointer;
  }

  .siswa-card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
  }

  .siswa-checkbox:checked~label .card {
    background: rgba(13, 110, 253, 0.05);
    border-color: #0d6efd !important;
    border-width: 2px !important;
  }

  .form-check-input {
    cursor: pointer;
    width: 1.25em;
    height: 1.25em;
  }

  .form-check-label {
    cursor: pointer;
  }

  #toast-container>div {
    opacity: 1 !important;
    background-image: none !important;
  }

  .toast-success {
    background-color: #198754 !important;
  }

  .toast-error {
    background-color: #dc3545 !important;
  }

  .toast-info {
    background-color: #0dcaf0 !important;
  }

  .toast-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
  }


  .table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
  }

  .btn-group-sm .btn {
    padding: 4px 8px;
  }

  /* Style for disabled lulus option */
  #label_lulus {
    transition: opacity 0.3s ease;
  }
</style>


@endsection
