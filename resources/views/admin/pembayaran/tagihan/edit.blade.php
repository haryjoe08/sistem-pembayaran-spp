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
        <div class="col-md-6">
          @if($tagihan->jatuh_tempo)
          <div class="alert {{ $tagihan->isJatuhTempo() ? 'alert-danger' : ($tagihan->isMendekatJatuhTempo() ? 'alert-warning' : 'alert-success') }}">
            <h6>
              <i class="fas fa-calendar-alt"></i> Status Jatuh Tempo
            </h6>
            <p class="mb-1">
              <strong>Tanggal:</strong> {{ $tagihan->jatuh_tempo->format('d/m/Y') }}
            </p>
            <p class="mb-0">
              @if($tagihan->isJatuhTempo())
              <span class="badge bg-danger">
                <i class="fas fa-exclamation-triangle"></i>
                Sudah Lewat {{ $tagihan->jatuh_tempo->diffForHumans() }}
              </span>
              @elseif($tagihan->isMendekatJatuhTempo())
              <span class="badge bg-warning">
                <i class="fas fa-clock"></i>
                {{ $tagihan->jatuh_tempo->diffForHumans() }}
              </span>
              @else
              <span class="badge bg-success">
                <i class="fas fa-check-circle"></i>
                {{ $tagihan->jatuh_tempo->diffForHumans() }}
              </span>
              @endif
            </p>
          </div>
          @endif
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <!-- SISWA (READONLY) -->
          <div class="mb-3">
            <label class="form-label">Siswa</label>

            <select class="form-select" disabled>
              @foreach($siswa as $s)
              <option value="{{ $s->nis }}"
                {{ $tagihan->siswa_nis == $s->nis ? 'selected' : '' }}>
                {{ $s->nis }} - {{ $s->nama }} ({{ $s->kelas->kelas ?? 'Belum ada kelas' }})
              </option>
              @endforeach
            </select>

            <input type="hidden" name="siswa_nis" value="{{ $tagihan->siswa_nis }}">
          </div>


          <!-- JENIS TAGIHAN -->
          <div class="mb-3">
            <label for="jenis_tagihan_id" class="form-label">Jenis Tagihan <span class="text-danger">*</span></label>
            <select
              class="form-select @error('jenis_tagihan_id') is-invalid @enderror"
              id="jenis_tagihan_id"
              name="jenis_tagihan_id"
              required
              {{ $isReadonly ? 'disabled' : '' }}>

              <option value="">-- Pilih Jenis Tagihan --</option>
              @foreach($jenisTagihan as $jp)
              <option value="{{ $jp->id }}"
                data-nominal="{{ $jp->nominal }}"
                {{ (old('jenis_tagihan_id') ?? $tagihan->jenis_tagihan_id) == $jp->id ? 'selected' : '' }}>
                {{ $jp->nama }} (Rp{{ number_format($jp->nominal, 0, ',', '.') }})
              </option>
              @endforeach
            </select>
            @error('jenis_tagihan_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if($isReadonly)
            <input
              type="hidden"
              name="jenis_tagihan_id"
              value="{{ $tagihan->jenis_tagihan_id }}">
            @endif
          </div>

          <!-- JATUH TEMPO (EDITABLE) -->
          <div class="mb-3">
            <label for="jatuh_tempo" class="form-label">
              Jatuh Tempo <span class="text-danger">*</span>
            </label>
            <input type="date"
              class="form-control @error('jatuh_tempo') is-invalid @enderror"
              id="jatuh_tempo"
              name="jatuh_tempo"
              value="{{ old('jatuh_tempo') ?? ($tagihan->jatuh_tempo ? $tagihan->jatuh_tempo->format('Y-m-d') : now()->addDays(30)->format('Y-m-d')) }}"
              min="{{ now()->format('Y-m-d') }}"
              required>
            @error('jatuh_tempo')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">
              <i class="fas fa-info-circle"></i>
              Pilih tanggal jatuh tempo pembayaran
            </div>
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
            <div class="form-text">Otomatis mengikuti Jenis Tagihan yang dipilih</div>
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

          <!-- QUICK ACTIONS untuk Jatuh Tempo -->
          <div class="mb-3">
            <label class="form-label">Atur Jatuh Tempo Cepat</label>
            <div class="btn-group d-flex" role="group">
              <button type="button" class="btn btn-outline-primary btn-sm" onclick="setJatuhTempo(7)">
                +7 Hari
              </button>
              <button type="button" class="btn btn-outline-primary btn-sm" onclick="setJatuhTempo(14)">
                +14 Hari
              </button>
              <button type="button" class="btn btn-outline-primary btn-sm" onclick="setJatuhTempo(30)">
                +30 Hari
              </button>
              <button type="button" class="btn btn-outline-primary btn-sm" onclick="setJatuhTempo(60)">
                +60 Hari
              </button>
            </div>
            <div class="form-text">
              <i class="fas fa-lightbulb"></i>
              Klik untuk mengatur jatuh tempo dari hari ini
            </div>
          </div>
        </div>
      </div>

      <!-- WARNING -->
      <div class="alert alert-info" role="alert">
        <i class="fas fa-info-circle"></i>
        <strong>Informasi:</strong> Form ini untuk mengubah Jenis Tagihan dan jatuh tempo tagihan. Untuk mencatat pembayaran dari siswa, gunakan menu "Terima Pembayaran".
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
  // Auto update total tagihan berdasarkan Jenis Tagihan
  document.getElementById('jenis_tagihan_id').addEventListener('change', function() {
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

  // Set Jatuh Tempo Quick Action
  function setJatuhTempo(days) {
    const today = new Date();
    today.setDate(today.getDate() + days);

    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');

    document.getElementById('jatuh_tempo').value = `${year}-${month}-${day}`;

    // Visual feedback
    const input = document.getElementById('jatuh_tempo');
    input.classList.add('border-success');
    setTimeout(() => {
      input.classList.remove('border-success');
    }, 500);
  }

  // Validasi jatuh tempo (warning jika sudah lewat)
  document.getElementById('jatuh_tempo').addEventListener('change', function() {
    const selectedDate = new Date(this.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (selectedDate < today) {
      if (!confirm('Tanggal jatuh tempo yang dipilih sudah lewat. Yakin ingin melanjutkan?')) {
        this.value = today.toISOString().split('T')[0];
      }
    }
  });

  // Konfirmasi submit
  document.querySelector('form').addEventListener('submit', function(e) {
    const siswaSelect = document.getElementById('siswa_nis');
    const siswaText = siswaSelect.options[siswaSelect.selectedIndex].text;
    const jenisSelect = document.getElementById('jenis_tagihan_id');
    const jenisText = jenisSelect.options[jenisSelect.selectedIndex].text;
    const jatuhTempo = document.getElementById('jatuh_tempo').value;

    const message = `Yakin ingin memperbarui tagihan?\n\n` +
      `Siswa: ${siswaText}\n` +
      `Jenis: ${jenisText}\n` +
      `Jatuh Tempo: ${new Date(jatuhTempo).toLocaleDateString('id-ID')}`;

    if (!confirm(message)) {
      e.preventDefault();
    }
  });

  // Initial calculation
  calculateSisa();
</script>

<style>
  /* Smooth border transition for date input */
  #jatuh_tempo.border-success {
    border-color: #28a745 !important;
    transition: border-color 0.3s ease;
  }

  /* Button hover effect */
  .btn-group button:hover {
    transform: translateY(-2px);
    transition: transform 0.2s;
  }
</style>
@endsection