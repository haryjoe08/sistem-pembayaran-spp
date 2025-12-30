@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">
  
  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-file-earmark-arrow-up me-2 text-primary"></i>
            Import Data Siswa
          </h4>
          <p class="text-muted mb-0">Upload file Excel untuk import data siswa massal</p>
        </div>
        <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
      </div>
    </div>
  </div>

  <!-- Alert -->
  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  @if(session('import_failures'))
  <div class="alert alert-warning">
    <h6><i class="bi bi-exclamation-triangle me-2"></i>Terdapat kesalahan pada baris berikut:</h6>
    <ul class="mb-0">
      @foreach(session('import_failures') as $failure)
        <li>
          Baris {{ $failure->row() }}: {{ implode(', ', $failure->errors()) }}
        </li>
      @endforeach
    </ul>
  </div>
  @endif

  <div class="row justify-content-center">
    <div class="col-lg-8">
      
      <!-- Step 1: Download Template -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-info text-white py-3">
          <h6 class="mb-0 fw-bold">
            <span class="badge bg-white text-info me-2">1</span>
            Download Template Excel
          </h6>
        </div>
        <div class="card-body">
          <p class="mb-3">
            <i class="bi bi-info-circle me-2"></i>
            Download template Excel terlebih dahulu, lalu isi data siswa sesuai format.
          </p>
          <a href="{{ route('siswa.template') }}" class="btn btn-info">
            <i class="bi bi-download me-1"></i> Download Template
          </a>
        </div>
      </div>

      <!-- Step 2: Upload File -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-3">
          <h6 class="mb-0 fw-bold">
            <span class="badge bg-white text-primary me-2">2</span>
            Upload File Excel
          </h6>
        </div>
        <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="card-body">
            <div class="mb-4">
              <label class="form-label fw-bold">Pilih File Excel</label>
              <input type="file" 
                     class="form-control form-control-lg @error('file') is-invalid @enderror" 
                     name="file" 
                     accept=".xlsx,.xls"
                     required>
              @error('file')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">
                <i class="bi bi-info-circle me-1"></i>
                Format: .xlsx atau .xls, Maksimal 5MB
              </div>
            </div>

            <!-- Preview Info -->
            <div class="alert alert-warning" id="file-preview" style="display: none;">
              <div class="d-flex align-items-center">
                <i class="bi bi-file-earmark-excel fs-1 me-3"></i>
                <div>
                  <strong id="file-name"></strong>
                  <br>
                  <small id="file-size"></small>
                </div>
              </div>
            </div>
          </div>

          <div class="card-footer bg-white">
            <button type="submit" class="btn btn-primary btn-lg px-4">
              <i class="bi bi-upload me-1"></i> Upload & Import
            </button>
          </div>
        </form>
      </div>

      <!-- Instruksi -->
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
          <h6 class="mb-0 fw-bold">
            <i class="bi bi-book me-2"></i> Petunjuk Import
          </h6>
        </div>
        <div class="card-body">
          <ol class="mb-0">
            <li class="mb-2">Download template Excel menggunakan tombol di atas</li>
            <li class="mb-2">Buka file template dengan Microsoft Excel atau Google Sheets</li>
            <li class="mb-2">Hapus baris contoh (baris 2 dan 3)</li>
            <li class="mb-2">Isi data siswa sesuai format yang ada</li>
            <li class="mb-2">
              <strong>Kolom wajib diisi:</strong> NIS, Nama, Kelas, Jurusan, Tahun Ajaran, Jenis Kelamin
            </li>
            <li class="mb-2">Simpan file (tidak perlu mengubah nama)</li>
            <li class="mb-2">Upload file menggunakan form di atas</li>
          </ol>

          <hr class="my-3">

          <h6 class="fw-bold mb-2">
            <i class="bi bi-exclamation-circle me-2"></i> Catatan Penting:
          </h6>
          <ul class="mb-0 text-muted">
            <li>Jangan mengubah nama header kolom</li>
            <li>Format tanggal: dd-mm-yyyy (contoh: 15-03-2010)</li>
            <li>Jenis kelamin: L (Laki-laki) atau P (Perempuan)</li>
            <li>Status: aktif, tidak_aktif, lulus, pindah, keluar, cuti</li>
            <li>Jika NIS sudah ada, data akan di-update</li>
            <li>Sistem akan otomatis membuat akun login untuk siswa baru</li>
            <li>Password default: tanggal lahir format ddmmyyyy</li>
          </ul>
        </div>
      </div>

    </div>
  </div>

</div>

<script>
// Preview file
document.querySelector('input[type="file"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('file-preview');
    
    if (file) {
        const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
        document.getElementById('file-name').textContent = file.name;
        document.getElementById('file-size').textContent = `Ukuran: ${fileSize} MB`;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
});
</script>
@endsection