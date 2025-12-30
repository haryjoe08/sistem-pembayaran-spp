@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">

  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-person-plus me-2 text-primary"></i>
            Tambah Siswa Baru
          </h4>
          <p class="text-muted mb-0">
            Lengkapi formulir data siswa baru
          </p>
        </div>
        <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
      </div>
    </div>
  </div>

  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
          <h6 class="mb-0 fw-semibold">
            <i class="bi bi-file-earmark-person me-2"></i>
            Form Data Siswa Baru
          </h6>
        </div>

        <form action="{{ route('siswa.store') }}" method="POST">
          @csrf

          <div class="card-body p-4">

            <!-- Info Alert -->
            <div class="alert alert-info d-flex align-items-start mb-4">
              <i class="bi bi-info-circle me-3 fs-5"></i>
              <div>
                <strong>Informasi Penting:</strong>
                <ul class="mb-0 mt-2 ps-3">
                  <li>NIS akan digunakan sebagai <strong>username login</strong> siswa</li>
                  <li>Password otomatis di-generate dari <strong>tanggal lahir</strong> (format: ddmmyyyy)</li>
                  <li>Siswa dapat login ke sistem setelah data tersimpan</li>
                </ul>
              </div>
            </div>

            <!-- Data Identitas -->
            <h6 class="fw-bold mb-3 pb-2 border-bottom">
              <i class="bi bi-person-badge me-2 text-primary"></i>
              Data Identitas
            </h6>

            <div class="row mb-3">
              <div class="col-md-6 mb-3">
                <label for="nis" class="form-label fw-semibold">
                  NIS <span class="text-danger">*</span>
                </label>
                <input type="text"
                  class="form-control @error('nis') is-invalid @enderror"
                  id="nis"
                  name="nis"
                  value="{{ old('nis') }}"
                  placeholder="Contoh: 2024001"
                  inputmode="numeric"
                  pattern="[0-9]*"
                  maxlength="10"
                  oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                  required
                  autofocus>
                @error('nis')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                  <i class="bi bi-key me-1"></i>
                  NIS akan menjadi username login siswa
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="nama" class="form-label fw-semibold">
                  Nama Lengkap <span class="text-danger">*</span>
                </label>
                <input type="text"
                  class="form-control @error('nama') is-invalid @enderror"
                  id="nama"
                  name="nama"
                  type="text"
                  oninput="this.value = this.value.replace(/[^A-Za-z]/g, '')"
                  value="{{ old('nama') }}"
                  placeholder="Nama lengkap siswa"
                  required>
                @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="tgl_lahir" class="form-label fw-semibold">
                  Tanggal Lahir <span class="text-danger">*</span>
                </label>
                <input type="date"
                  class="form-control @error('tgl_lahir') is-invalid @enderror"
                  id="tgl_lahir"
                  name="tgl_lahir"
                  value="{{ old('tgl_lahir') }}"
                  max="{{ date('Y-m-d') }}"
                  required>
                @error('tgl_lahir')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                  <i class="bi bi-lock me-1"></i>
                  Password akan di-generate dari tanggal lahir (ddmmyyyy)
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="jenis_kelamin" class="form-label fw-semibold">
                  Jenis Kelamin <span class="text-danger">*</span>
                </label>
                <div class="d-flex gap-4 mt-2">
                  <div class="form-check">
                    <input class="form-check-input"
                      type="radio"
                      name="jenis_kelamin"
                      id="jk_l"
                      value="L"
                      {{ old('jenis_kelamin') == 'L' ? 'checked' : '' }}
                      required>
                    <label class="form-check-label" for="jk_l">
                      <i class="bi bi-gender-male text-primary"></i> Laki-laki
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input"
                      type="radio"
                      name="jenis_kelamin"
                      id="jk_p"
                      value="P"
                      {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }}
                      required>
                    <label class="form-check-label" for="jk_p">
                      <i class="bi bi-gender-female text-danger"></i> Perempuan
                    </label>
                  </div>
                </div>
                @error('jenis_kelamin')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Data Akademik -->
            <h6 class="fw-bold mb-3 pb-2 border-bottom mt-4">
              <i class="bi bi-book me-2 text-primary"></i>
              Data Akademik
            </h6>

            <div class="row mb-3">
              <div class="col-md-4 mb-3">
                <label for="kelas_id" class="form-label fw-semibold">
                  Kelas <span class="text-danger">*</span>
                </label>
                <select class="form-select @error('kelas_id') is-invalid @enderror"
                  id="kelas_id"
                  name="kelas_id"
                  required>
                  <option value="">-- Pilih Kelas --</option>
                  @foreach($kelas as $k)
                  <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                    {{ $k->kelas }}
                  </option>
                  @endforeach
                </select>
                @error('kelas_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-4 mb-3">
                <label for="jurusan_id" class="form-label fw-semibold">
                  Jurusan <span class="text-danger">*</span>
                </label>
                <select class="form-select @error('jurusan_id') is-invalid @enderror"
                  id="jurusan_id"
                  name="jurusan_id"
                  required>
                  <option value="">-- Pilih Jurusan --</option>
                  @foreach($jurusan as $j)
                  <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>
                    {{ $j->nama }}
                  </option>
                  @endforeach
                </select>
                @error('jurusan_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-4 mb-3">
                <label for="tahun_masuk" class="form-label fw-semibold">
                  Tahun Masuk <span class="text-danger">*</span>
                </label>
                <input type="text"
                  class="form-control @error('tahun_masuk') is-invalid @enderror"
                  id="tahun_masuk"
                  name="tahun_masuk"
                  inputmode="numeric"
                  pattern="[0-9]*"
                  maxlength="4"
                  oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                  value="{{ old('tahun_masuk') }}"
                  placeholder="Tahun Masuk siswa"
                  required>
                @error('tahun_masuk')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Data Kontak -->
            <h6 class="fw-bold mb-3 pb-2 border-bottom mt-4">
              <i class="bi bi-telephone me-2 text-primary"></i>
              Data Kontak & Wali
            </h6>

            <div class="row mb-3">
              <div class="col-md-12 mb-3">
                <label for="alamat" class="form-label fw-semibold">
                  Alamat <span class="text-danger">*</span>
                </label>
                <textarea class="form-control @error('alamat') is-invalid @enderror"
                  id="alamat"
                  name="alamat"
                  rows="3"
                  placeholder="Jl. Contoh No. 123, Kota, Provinsi"
                  required>{{ old('alamat') }}</textarea>
                @error('alamat')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="wali" class="form-label fw-semibold">
                  Nama Wali <span class="text-danger">*</span>
                </label>
                <input type="text"
                  class="form-control @error('wali') is-invalid @enderror"
                  id="wali"
                  name="wali"
                  value="{{ old('wali') }}"
                  placeholder="Nama orang tua/wali siswa"
                  required>
                @error('wali')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="kontak" class="form-label fw-semibold">
                  Nomor Kontak <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-whatsapp"></i>
                  </span>
                  <input type="text"
                    class="form-control @error('kontak') is-invalid @enderror"
                    id="kontak"
                    name="kontak"
                    value="{{ old('kontak') }}"
                    placeholder="08xxxxxxxxxx"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    minlength="10"
                    maxlength="12"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    required>

                  @error('kontak')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="form-text">
                  <i class="bi bi-info-circle me-1"></i>
                  Nomor WhatsApp orang tua/wali
                </div>
              </div>
            </div>

            <!-- Preview Info -->
            <div class="alert alert-light border mt-4">
              <div class="row">
                <div class="col-md-6">
                  <small class="text-muted d-block mb-1"><strong>Preview Login:</strong></small>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-person-circle me-2 text-primary"></i>
                    <div>
                      <small class="text-muted d-block">Username</small>
                      <span class="fw-bold" id="preview-nis">-</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <small class="text-muted d-block mb-1">&nbsp;</small>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-lock-fill me-2 text-primary"></i>
                    <div>
                      <small class="text-muted d-block">Password</small>
                      <span class="fw-bold" id="preview-password">-</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between">
              <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i> Batal
              </a>
              <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check-circle me-1"></i>
                Simpan Data Siswa
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Info Card -->
      <div class="card border-0 shadow-sm mt-3">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3">
            <i class="bi bi-lightbulb text-warning me-2"></i>
            Tips Pengisian Form
          </h6>
          <ul class="text-muted small mb-0 ps-3">
            <li>Pastikan <strong>NIS unik</strong> dan belum terdaftar</li>
            <li>Isi <strong>tanggal lahir</strong> dengan benar karena akan menjadi password</li>
            <li>Nomor kontak harus <strong>aktif</strong> untuk komunikasi dengan wali</li>
            <li>Data dapat diedit kembali setelah disimpan</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
  // Live Preview Login Credentials
  document.getElementById('nis').addEventListener('input', function() {
    const nis = this.value;
    document.getElementById('preview-nis').textContent = nis || '-';
  });

  document.getElementById('tgl_lahir').addEventListener('change', function() {
    const tglLahir = this.value; // Format: YYYY-MM-DD
    if (tglLahir) {
      // Convert to ddmmyyyy
      const [year, month, day] = tglLahir.split('-');
      const password = day + month + year;
      document.getElementById('preview-password').textContent = password;
    } else {
      document.getElementById('preview-password').textContent = '-';
    }
  });

  // Form validation before submit
  document.querySelector('form').addEventListener('submit', function(e) {
    const nis = document.getElementById('nis').value;
    const nama = document.getElementById('nama').value;
    const tglLahir = document.getElementById('tgl_lahir').value;

    if (!nis || !nama || !tglLahir) {
      e.preventDefault();
      alert('Mohon lengkapi data identitas siswa!');
      return;
    }

    // Confirm submit
    const confirmMsg = `Simpan data siswa baru?\n\n` +
      `NIS: ${nis}\n` +
      `Nama: ${nama}\n\n` +
      `Data login akan otomatis dibuat.`;

    if (!confirm(confirmMsg)) {
      e.preventDefault();
    }
  });

  // Auto capitalize nama
  document.getElementById('nama').addEventListener('blur', function() {
    this.value = this.value.toUpperCase();
  });
</script>

<style>
  .form-label {
    margin-bottom: 0.5rem;
  }

  .form-control:focus,
  .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
  }

  #preview-nis,
  #preview-password {
    font-family: monospace;
    font-size: 1.1rem;
    color: #0d6efd;
  }
</style>
@endsection