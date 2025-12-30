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
            Edit Tarif Pembayaran
          </h4>
          <p class="text-muted mb-0">Perbarui tarif pembayaran</p>
        </div>
        <a href="{{ route('tarif-tagihan.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
      </div>
    </div>
  </div>

  <!-- Form -->
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

          <form action="{{ route('tarif-tagihan.update', $tarif->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Jenis Tagihan -->
            <div class="mb-4">
              <label class="form-label fw-bold">Jenis Tagihan</label>
              <select class="form-select form-select-lg" name="jenis_tagihan_id" required>
                @foreach($jenisTagihan as $jp)
                <option value="{{ $jp->id }}"
                  {{ old('jenis_tagihan_id', $tarif->jenis_tagihan_id) == $jp->id ? 'selected' : '' }}>
                  {{ $jp->nama }} ({{ ucfirst($jp->tipe) }})
                </option>
                @endforeach
              </select>
            </div>

            <!-- Tahun Ajaran -->
            <div class="mb-4">
              <label class="form-label fw-bold">Tahun Ajaran</label>
              <select name="tahun_ajaran_id" class="form-control">
                @foreach ($tahunAjaran as $ta)
                <option value="{{ $ta->id }}"
                  {{ old('tahun_ajaran_id', $tarif->tahun_ajaran_id) == $ta->id ? 'selected' : '' }}>
                  {{ $ta->tahun }}
                </option>
                @endforeach
              </select>
            </div>

            <!-- Nominal -->
            <div class="mb-4">
              <label class="form-label fw-bold">Nominal</label>
              <div class="input-group input-group-lg">
                <span class="input-group-text">Rp</span>
                <input type="text"
                  class="form-control"
                  id="nominal-display"
                  oninput="formatRupiah(this)"
                  value="{{ number_format(old('nominal', $tarif->nominal), 0, ',', '.') }}"
                  required>
                <input type="hidden"
                  name="nominal"
                  id="nominal-raw"
                  value="{{ old('nominal', $tarif->nominal) }}">
              </div>
            </div>

            <!-- Keterangan -->
            <div class="mb-4">
              <label class="form-label fw-bold">Keterangan</label>
              <textarea class="form-control"
                name="keterangan"
                rows="3">{{ old('keterangan', $tarif->keterangan) }}</textarea>
            </div>

            <!-- Preview -->
            <div class="card bg-light border-0 mb-4">
              <div class="card-body">
                <h6 class="fw-bold mb-3">Preview:</h6>
                <strong id="preview-jenis">{{ $tarif->jenisTagihan->nama }}</strong><br>
                <strong id="preview-tahun">{{ $tarif->tahunAjaran->tahun_ajaran }}</strong>
                <h4 class="text-primary mt-2" id="preview-nominal">
                  Rp {{ number_format($tarif->nominal, 0, ',', '.') }}
                </h4>
              </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-warning btn-lg">
                <i class="bi bi-save me-1"></i> Update Tarif
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
  function formatRupiah(input) {
    let value = input.value.replace(/\D/g, '');
    document.getElementById('nominal-raw').value = value;
    input.value = value ? parseInt(value).toLocaleString('id-ID') : '';
    document.getElementById('preview-nominal').textContent =
      value ? 'Rp ' + parseInt(value).toLocaleString('id-ID') : 'Rp 0';
  }
</script>
@endsection