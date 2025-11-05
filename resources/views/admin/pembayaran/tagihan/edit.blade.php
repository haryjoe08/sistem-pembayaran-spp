@extends('layouts.adminMaster')

@section('content')
<div class="card card-warning card-outline mt-5 mx-5">
  <div class="card-header">
    <h4>Form Edit Tagihan</h4>
  </div>

  <form action="{{ route('tagihan.update', $tagihan->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card-body">

      <!-- INFO TAGIHAN -->
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="alert alert-info">
            <h6><i class="fas fa-info-circle"></i> Informasi Tagihan</h6>
            <p class="mb-1"><strong>Dibuat:</strong> {{ $tagihan->created_at->format('d/m/Y H:i') }}</p>
            <p class="mb-0"><strong>Terakhir Update:</strong> {{ $tagihan->updated_at->format('d/m/Y H:i') }}</p>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <!-- SISWA -->
          <div class="mb-3">
            <label for="siswa_nis" class="form-label">Siswa <span class="text-danger">*</span></label>
            <select class="form-select @error('siswa_nis') is-invalid @enderror"
              id="siswa_nis" name="siswa_nis" required>
              <option value="">-- Pilih Siswa --</option>
              @foreach($siswa as $s)
                <option value="{{ $s->nis }}" 
                  {{ (old('siswa_nis') ?? $tagihan->siswa_nis) == $s->nis ? 'selected' : '' }}
                  data-kelas="{{ $s->kelas->kelas ?? 'Belum ada kelas' }}">
                  {{ $s->nis }} - {{ $s->nama }} ({{ $s->kelas->kelas ?? 'Belum ada kelas' }})
                </option>
              @endforeach
            </select>
            @error('siswa_nis')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- JENIS PEMBAYARAN -->
          <div class="mb-3">
            <label for="jenis_pembayaran_id" class="form-label">Jenis Pembayaran <span class="text-danger">*</span></label>
            <select class="form-select @error('jenis_pembayaran_id') is-invalid @enderror"
              id="jenis_pembayaran_id" name="jenis_pembayaran_id" required>
              <option value="">-- Pilih Jenis Pembayaran --</option>
              @foreach($jenisPembayaran as $jp)
                <option value="{{ $jp->id }}" 
                  data-nominal="{{ $jp->nominal }}"
                  {{ (old('jenis_pembayaran_id') ?? $tagihan->jenis_pembayaran_id) == $jp->id ? 'selected' : '' }}>
                  {{ $jp->nama }} (Rp{{ number_format($jp->nominal, 0, ',', '.') }})
                </option>
              @endforeach
            </select>
            @error('jenis_pembayaran_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- TOTAL TAGIHAN (READ ONLY) -->
          <div class="mb-3">
            <label for="total_tagihan" class="form-label">Total Tagihan</label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="number" 
                class="form-control" 
                id="total_tagihan" 
                name="total_tagihan" 
                value="{{ old('total_tagihan') ?? $tagihan->total_tagihan }}" 
                readonly>
            </div>
            <div class="form-text">Otomatis mengikuti jenis pembayaran yang dipilih</div>
          </div>
        </div>

        <div class="col-md-6">
          <!-- SUDAH DIBAYAR (READ ONLY) -->
          <div class="mb-3">
            <label for="sudah_dibayar" class="form-label">Sudah Dibayar</label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="number" 
                class="form-control" 
                id="sudah_dibayar" 
                name="sudah_dibayar" 
                value="{{ old('sudah_dibayar') ?? $tagihan->sudah_dibayar }}" 
                readonly>
            </div>
            <div class="form-text">Data pembayaran dikelola di menu "Terima Pembayaran"</div>
          </div>

          <!-- SISA TAGIHAN (READ ONLY) -->
          <div class="mb-3">
            <label for="sisa_tagihan" class="form-label">Sisa Tagihan</label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="text" class="form-control" id="sisa_tagihan" readonly>
            </div>
            <div class="form-text">Otomatis dihitung dari Total - Sudah Dibayar</div>
          </div>

          <!-- STATUS (READ ONLY) -->
          <div class="mb-3">
            <label for="current_status" class="form-label">Status Saat Ini</label>
            <div>
              <span class="badge {{ $tagihan->status == 'lunas' ? 'bg-success' : 'bg-warning' }}">
                {{ ucfirst($tagihan->status) }}
              </span>
            </div>
            <div class="form-text">Status otomatis berubah saat pembayaran dilakukan</div>
          </div>
        </div>
      </div>

      <!-- WARNING -->
      <div class="alert alert-info" role="alert">
        <i class="fas fa-info-circle"></i> 
        <strong>Informasi:</strong> Form ini hanya untuk mengubah jenis pembayaran tagihan. Untuk mencatat pembayaran dari siswa, gunakan menu "Terima Pembayaran".
      </div>
    </div>

    <div class="card-footer">
      <button type="submit" class="btn btn-warning">
        <i class="fas fa-save"></i> Update Tagihan
      </button>
      <a href="{{ route('tagihan.index') }}" class="btn btn-light float-end">
        <i class="fas fa-arrow-left"></i> Kembali
      </a>
    </div>
  </form>
</div>

<script>
  // Auto update total tagihan berdasarkan jenis pembayaran
  document.getElementById('jenis_pembayaran_id').addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    const nominal = selected.getAttribute('data-nominal');
    
    if (nominal) {
      document.getElementById('total_tagihan').value = nominal;
      calculateSisa();
    }
  });

  // Hitung sisa tagihan
  function calculateSisa() {
    const total = parseFloat(document.getElementById('total_tagihan').value) || 0;
    const sudahBayar = parseFloat(document.getElementById('sudah_dibayar').value) || 0;
    const sisa = total - sudahBayar;
    
    document.getElementById('sisa_tagihan').value = sisa.toLocaleString('id-ID');
    
    // Update status preview
    const statusPreview = document.getElementById('status_preview');
    if (sisa <= 0 && total > 0) {
      statusPreview.textContent = 'Lunas';
      statusPreview.className = 'badge bg-success';
      document.getElementById('status').value = 'lunas';
    } else {
      statusPreview.textContent = 'Belum Lunas';
      statusPreview.className = 'badge bg-warning';
      document.getElementById('status').value = 'belum lunas';
    }
  }

  // Event listeners
  document.getElementById('total_tagihan').addEventListener('input', calculateSisa);
  document.getElementById('sudah_dibayar').addEventListener('input', calculateSisa);

  // Validasi real-time
  document.getElementById('sudah_dibayar').addEventListener('input', function() {
    const total = parseFloat(document.getElementById('total_tagihan').value) || 0;
    const sudahBayar = parseFloat(this.value) || 0;
    
    if (sudahBayar > total) {
      this.setCustomValidity('Jumlah yang sudah dibayar tidak boleh melebihi total tagihan');
      this.style.borderColor = '#dc3545';
    } else {
      this.setCustomValidity('');
      this.style.borderColor = '';
    }
  });

  // Konfirmasi submit
  document.querySelector('form').addEventListener('submit', function(e) {
    const siswaSelect = document.getElementById('siswa_nis');
    const siswaText = siswaSelect.options[siswaSelect.selectedIndex].text;
    const jenisSelect = document.getElementById('jenis_pembayaran_id');
    const jenisText = jenisSelect.options[jenisSelect.selectedIndex].text;
    
    if (!confirm(`Yakin ingin memperbarui tagihan untuk:\n${siswaText}\n${jenisText}?`)) {
      e.preventDefault();
    }
  });

  // Initial calculation
  calculateSisa();
</script>
@endsection