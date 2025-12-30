@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">

  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-pencil-square me-2 text-warning"></i>
            Edit Data Siswa
          </h4>
          <p class="text-muted mb-0">
            Perbarui informasi data siswa
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

      <!-- Student Info Card -->
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3 bg-light">
          <div class="row align-items-center">
            <div class="col-md-8">
              <div class="d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-25 p-3 me-3">
                  <i class="bi bi-person-fill fs-4 text-warning"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-1">{{ $siswa->nama }}</h6>
                  <p class="text-muted small mb-0">
                    <i class="bi bi-hash"></i> NIS: {{ $siswa->nis }} •
                    <i class="bi bi-book ms-2"></i> {{ $siswa->kelas->kelas ?? '-' }} - {{ $siswa->jurusan->nama ?? '-' }}
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-4 text-end">
              <span class="badge bg-warning text-dark px-3 py-2">
                <i class="bi bi-pencil me-1"></i>
                Mode Edit
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Form Card -->
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-warning text-white py-3">
          <h6 class="mb-0 fw-semibold">
            <i class="bi bi-file-earmark-person me-2"></i>
            Form Edit Data Siswa
          </h6>
        </div>

        <form action="{{ route('siswa.update', $siswa->nis) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="card-body p-4">

            <!-- Info Alert -->
            <div class="alert alert-warning d-flex align-items-start mb-4">
              <i class="bi bi-exclamation-triangle me-3 fs-5"></i>
              <div>
                <strong>Perhatian:</strong>
                <ul class="mb-0 mt-2 ps-3">
                  <li><strong>NIS tidak dapat diubah</strong> karena digunakan sebagai username login</li>
                  <li>Jika <strong>tanggal lahir diubah</strong>, password login akan di-generate ulang (ddmmyyyy)</li>
                  <li>Pastikan data yang diubah sudah benar sebelum menyimpan</li>
                </ul>
              </div>
            </div>

            <!-- Data Identitas -->
            <h6 class="fw-bold mb-3 pb-2 border-bottom">
              <i class="bi bi-person-badge me-2 text-warning"></i>
              Data Identitas
            </h6>

            <div class="row mb-3">
              <div class="col-md-6 mb-3">
                <label for="nis" class="form-label fw-semibold">
                  NIS <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light">
                    <i class="bi bi-lock-fill text-muted"></i>
                  </span>
                  <input type="text"
                    class="form-control bg-light"
                    id="nis"
                    name="nis"
                    value="{{ $siswa->nis }}"
                    readonly>
                </div>
                <div class="form-text">
                  <i class="bi bi-info-circle me-1"></i>
                  NIS tidak dapat diubah (username login)
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
                  value="{{ old('nama', $siswa->nama) }}"
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
                  value="{{ old('tgl_lahir', $siswa->tgl_lahir) }}"
                  max="{{ date('Y-m-d') }}"
                  required>
                @error('tgl_lahir')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text text-warning">
                  <i class="bi bi-exclamation-triangle me-1"></i>
                  Mengubah tanggal lahir akan mengubah password login
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
                      {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'checked' : '' }}
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
                      {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'checked' : '' }}
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
              <i class="bi bi-book me-2 text-warning"></i>
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
                  <option value="{{ $k->id }}"
                    {{ old('kelas_id', $siswa->kelas_id) == $k->id ? 'selected' : '' }}>
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
                  <option value="{{ $j->id }}"
                    {{ old('jurusan_id', $siswa->jurusan_id) == $j->id ? 'selected' : '' }}>
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
                  value="{{ old('tahun_masuk', $siswa->tahun_masuk) }}"
                  placeholder="Tahun Masuk Siswa"
                  required>
                @error('tahun_masuk')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Data Kontak -->
            <h6 class="fw-bold mb-3 pb-2 border-bottom mt-4">
              <i class="bi bi-telephone me-2 text-warning"></i>
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
                  placeholder="Alamat lengkap siswa"
                  required>{{ old('alamat', $siswa->alamat) }}</textarea>
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
                  value="{{ old('wali', $siswa->wali) }}"
                  placeholder="Nama orang tua/wali"
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
                    value="{{ old('kontak', $siswa->kontak) }}"
                    placeholder="08xxxxxxxxxx"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    maxlength="15"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    required>
                  @error('kontak')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Change Preview -->
            <div class="alert alert-light border mt-4">
              <h6 class="fw-bold mb-2">
                <i class="bi bi-eye text-warning me-2"></i>
                Preview Perubahan Password
              </h6>
              <div class="row">
                <div class="col-md-6">
                  <small class="text-muted d-block mb-1">Password Lama</small>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-lock-fill me-2 text-muted"></i>
                    <span class="fw-bold font-monospace" id="old-password">{{ \Carbon\Carbon::parse($siswa->tgl_lahir)->format('dmY') }}</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <small class="text-muted d-block mb-1">Password Baru (jika tanggal lahir diubah)</small>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-lock-fill me-2 text-warning"></i>
                    <span class="fw-bold font-monospace text-warning" id="new-password">{{ \Carbon\Carbon::parse($siswa->tgl_lahir)->format('dmY') }}</span>
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
              <button type="submit" class="btn btn-warning px-4">
                <i class="bi bi-check-circle me-1"></i>
                Update Data Siswa
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Metadata Card -->
      <div class="card border-0 shadow-sm mt-3">
        <div class="card-body p-3">
          <div class="row text-center">
            <div class="col-md-4">
              <small class="text-muted d-block mb-1">Dibuat</small>
              <p class="fw-bold mb-0 small">{{ $siswa->created_at->format('d M Y H:i') }}</p>
            </div>
            <div class="col-md-4">
              <small class="text-muted d-block mb-1">Terakhir Diupdate</small>
              <p class="fw-bold mb-0 small">{{ $siswa->updated_at->format('d M Y H:i') }}</p>
            </div>
            <div class="col-md-4">
              <small class="text-muted d-block mb-1">Username Login</small>
              <p class="fw-bold mb-0 small font-monospace">{{ $siswa->nis }}</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>

<script>
  // Track tanggal lahir changes untuk password preview
  const originalTglLahir = '{{ $siswa->tgl_lahir }}';

  document.getElementById('tgl_lahir').addEventListener('change', function() {
    const tglLahir = this.value;

    if (tglLahir && tglLahir !== originalTglLahir) {
      // Convert to ddmmyyyy
      const [year, month, day] = tglLahir.split('-');
      const newPassword = day + month + year;

      document.getElementById('new-password').textContent = newPassword;
      document.getElementById('new-password').parentElement.classList.add('bg-warning', 'bg-opacity-10', 'p-2', 'rounded');
    } else {
      // Reset to original
      const [year, month, day] = originalTglLahir.split('-');
      const oldPassword = day + month + year;
      document.getElementById('new-password').textContent = oldPassword;
      document.getElementById('new-password').parentElement.classList.remove('bg-warning', 'bg-opacity-10', 'p-2', 'rounded');
    }
  });

  // Auto capitalize nama
  document.getElementById('nama').addEventListener('blur', function() {
    this.value = this.value.toUpperCase();
  });

  // Confirmation before submit
  document.querySelector('form').addEventListener('submit', function(e) {
    const tglLahir = document.getElementById('tgl_lahir').value;

    let confirmMsg = `Simpan perubahan data siswa?\n\n` +
      `Nama: ${document.getElementById('nama').value}\n` +
      `NIS: ${document.getElementById('nis').value}`;

    if (tglLahir !== originalTglLahir) {
      const [year, month, day] = tglLahir.split('-');
      const newPassword = day + month + year;
      confirmMsg += `\n\n⚠️ PERHATIAN:\nPassword login akan berubah menjadi: ${newPassword}`;
    }

    if (!confirm(confirmMsg)) {
      e.preventDefault();
    }
  });

  // Highlight changed fields
  const formInputs = document.querySelectorAll('input:not([readonly]), select, textarea');
  formInputs.forEach(input => {
    const originalValue = input.value;

    input.addEventListener('change', function() {
      if (this.value !== originalValue) {
        this.classList.add('border-warning', 'border-2');
      } else {
        this.classList.remove('border-warning', 'border-2');
      }
    });
  });
</script>

<style>
  .bg-light input[readonly] {
    cursor: not-allowed;
  }

  .border-2 {
    border-width: 2px !important;
  }

  .form-control:focus,
  .form-select:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.15);
  }
</style>
@endsection