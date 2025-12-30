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
            Tambah Tarif Pembayaran
          </h4>
          <p class="text-muted mb-0">Tambahkan tarif untuk Jenis Tagihan tertentu</p>
        </div>
        <a href="{{ route('tarif-tagihan.index') }}" class="btn btn-outline-secondary">
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

  <!-- Form -->
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <form action="{{ route('tarif-tagihan.store') }}" method="POST">
            @csrf

            <!-- Info -->
            <div class="alert alert-info mb-4">
              <i class="bi bi-info-circle me-2"></i>
              <strong>Petunjuk:</strong> 
              Tarif ini akan digunakan saat membuat tagihan untuk tahun ajaran yang dipilih.
              Pastikan kombinasi Jenis Tagihan dan tahun ajaran belum ada.
            </div>

            <!-- Jenis Tagihan -->
            <div class="mb-4">
              <label class="form-label fw-bold">
                Jenis Tagihan <span class="text-danger">*</span>
              </label>
              <select class="form-select form-select-lg @error('jenis_tagihan_id') is-invalid @enderror" 
                      name="jenis_tagihan_id" 
                      required>
                <option value="">-- Pilih Jenis Tagihan --</option>
                @foreach($jenisTagihan as $jp)
                  <option value="{{ $jp->id }}" {{ old('jenis_tagihan_id') == $jp->id ? 'selected' : '' }}>
                    {{ $jp->nama }} 
                  </option>
                @endforeach
              </select>
              @error('jenis_tagihan_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Tahun Ajaran -->
            <div class="mb-4">
              <label class="form-label fw-bold">
                Tahun Ajaran <span class="text-danger">*</span>
              </label>
              <select class="form-select form-select-lg @error('tahun_ajaran_id') is-invalid @enderror" 
                      name="tahun_ajaran_id" 
                      required>
                <option value="">-- Pilih Tahun Ajaran --</option>
                @foreach($tahunAjaran as $ta)
                  <option value="{{ $ta->id }}" {{ old('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                    {{ $ta->tahun }}
                    @if($ta->status === 'aktif') (Aktif) @endif
                  </option>
                @endforeach
              </select>
              @error('tahun_ajaran_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Nominal -->
            <div class="mb-4">
              <label class="form-label fw-bold">
                Nominal <span class="text-danger">*</span>
              </label>
              <div class="input-group input-group-lg">
                <span class="input-group-text bg-light">Rp</span>
                <input type="text" 
                       class="form-control @error('nominal') is-invalid @enderror" 
                       id="nominal-display"
                       placeholder="0"
                       oninput="formatRupiah(this)"
                       value="{{ old('nominal') ? number_format(old('nominal'), 0, ',', '.') : '' }}"
                       required>
                <input type="hidden" name="nominal" id="nominal-raw" value="{{ old('nominal') }}">
                @error('nominal')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <small class="text-muted">Masukkan nominal tarif untuk Jenis Tagihan ini</small>
            </div>

            <!-- Keterangan -->
            <div class="mb-4">
              <label class="form-label fw-bold">Keterangan (Optional)</label>
              <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                        name="keterangan" 
                        rows="3" 
                        placeholder="Misal: Tarif baru untuk tahun ajaran 2025/2026">{{ old('keterangan') }}</textarea>
              @error('keterangan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Preview -->
            <div class="card bg-light border-0 mb-4">
              <div class="card-body">
                <h6 class="fw-bold mb-3">Preview:</h6>
                <div class="row">
                  <div class="col-6">
                    <small class="text-muted d-block">Jenis Tagihan</small>
                    <strong id="preview-jenis">-</strong>
                  </div>
                  <div class="col-6">
                    <small class="text-muted d-block">Tahun Ajaran</small>
                    <strong id="preview-tahun">-</strong>
                  </div>
                  <div class="col-12 mt-3">
                    <small class="text-muted d-block">Nominal</small>
                    <h4 class="text-primary mb-0" id="preview-nominal">Rp 0</h4>
                  </div>
                </div>
              </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-save me-1"></i> Simpan Tarif
              </button>
              <a href="{{ route('tarif-tagihan.index') }}" class="btn btn-outline-secondary btn-lg">
                Batal
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
// Format Rupiah
function formatRupiah(input) {
    let value = input.value.replace(/\D/g, '');
    document.getElementById('nominal-raw').value = value;
    
    if (value) {
        input.value = parseInt(value).toLocaleString('id-ID');
        document.getElementById('preview-nominal').textContent = 'Rp ' + parseInt(value).toLocaleString('id-ID');
    } else {
        input.value = '';
        document.getElementById('preview-nominal').textContent = 'Rp 0';
    }
}

// Update preview Jenis Tagihan
document.querySelector('select[name="jenis_tagihan_id"]').addEventListener('change', function() {
    const text = this.options[this.selectedIndex].text;
    document.getElementById('preview-jenis').textContent = text !== '-- Pilih Jenis Tagihan --' ? text : '-';
});

// Update preview tahun ajaran
document.querySelector('select[name="tahun_ajaran_id"]').addEventListener('change', function() {
    const text = this.options[this.selectedIndex].text;
    document.getElementById('preview-tahun').textContent = text !== '-- Pilih Tahun Ajaran --' ? text : '-';
});
</script>
@endsection