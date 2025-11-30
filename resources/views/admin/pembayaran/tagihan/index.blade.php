@extends('layouts.adminMaster')

@section('content')
<div class="container">
    <h4 class="my-3">Daftar Tagihan Aktif</h4>
    <a href="{{ route('tagihan.create') }}" class="btn btn-primary mb-3">+ Tambah Tagihan</a>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Jenis Pembayaran</th>
                    <th>Total Tagihan</th>
                    <th>Sudah Dibayar</th>
                    <th>Sisa Tagihan</th>
                    <th>Jatuh Tempo</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $d)
                <tr>
                    <td>{{ ($data->currentPage()-1) * $data->perPage() + $loop->iteration }}</td>
                    <td>{{ $d->siswa_nis }}</td>
                    <td>{{ $d->siswa->nama ?? '-' }} </td>
                    <td>{{ $d->siswa->kelas?->kelas ?? '-' }}</td>
                    <td>{{ $d->jenisPembayaran->nama ?? '-' }}</td>
                    <td>Rp {{ number_format($d->total_tagihan,0,',','.') }}</td>
                    <td>Rp {{ number_format($d->sudah_dibayar,0,',','.') }}</td>
                    <td>
                        <span class="text-danger fw-bold">
                            Rp {{ number_format($d->total_tagihan - $d->sudah_dibayar,0,',','.') }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($d->jatuh_tempo)->translatedFormat('d F Y') }}</td>

                    <td>
                        <a href="{{ route('tagihan.edit', $d->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('tagihan.destroy', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada tagihan yang belum lunas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $data->links() }}
</div>


@if(session('tagihan_stats'))
  @php
    $stats = session('tagihan_stats');
  @endphp

  <!-- Statistics Alert -->
  <div class="row mb-4">
    <div class="col-12">
      
      <!-- Summary Cards -->
      <div class="row mb-3">
        @if($stats['created'] > 0)
        <div class="col-md-4">
          <div class="alert alert-success border-0 shadow-sm mb-0">
            <div class="d-flex align-items-center">
              <div class="rounded-circle bg-success bg-opacity-25 p-3 me-3">
                <i class="bi bi-check-circle-fill fs-3 text-success"></i>
              </div>
              <div>
                <h4 class="fw-bold mb-0">{{ $stats['created'] }}</h4>
                <small class="text-muted">Tagihan Berhasil Dibuat</small>
              </div>
            </div>
          </div>
        </div>
        @endif

        @if($stats['blocked'] > 0)
        <div class="col-md-4">
          <div class="alert alert-warning border-0 shadow-sm mb-0">
            <div class="d-flex align-items-center">
              <div class="rounded-circle bg-warning bg-opacity-25 p-3 me-3">
                <i class="bi bi-exclamation-triangle-fill fs-3 text-warning"></i>
              </div>
              <div>
                <h4 class="fw-bold mb-0">{{ $stats['blocked'] }}</h4>
                <small class="text-muted">Siswa Diblokir</small>
              </div>
            </div>
          </div>
        </div>
        @endif

        @if($stats['skipped'] > 0)
        <div class="col-md-4">
          <div class="alert alert-info border-0 shadow-sm mb-0">
            <div class="d-flex align-items-center">
              <div class="rounded-circle bg-info bg-opacity-25 p-3 me-3">
                <i class="bi bi-info-circle-fill fs-3 text-info"></i>
              </div>
              <div>
                <h4 class="fw-bold mb-0">{{ $stats['skipped'] }}</h4>
                <small class="text-muted">Siswa Dilewati</small>
              </div>
            </div>
          </div>
        </div>
        @endif
      </div>

      <!-- Blocked Details (Expandable) -->
      @if($stats['blocked'] > 0 && count($stats['blocked_details']) > 0)
      <div class="card border-0 shadow-sm border-warning border-2">
        <div class="card-header bg-warning bg-opacity-10">
          <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-warning">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              Siswa yang Diblokir ({{ count($stats['blocked_details']) }})
            </h6>
            <button class="btn btn-sm btn-warning" type="button" data-bs-toggle="collapse" data-bs-target="#blockedDetails">
              <i class="bi bi-chevron-down"></i> Lihat Detail
            </button>
          </div>
        </div>
        <div class="collapse" id="blockedDetails">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 15%">NIS</th>
                    <th style="width: 30%">Nama Siswa</th>
                    <th style="width: 20%">Jatuh Tempo Tagihan Aktif</th>
                    <th style="width: 20%">Sisa Tagihan</th>
                    <th style="width: 10%">Alasan</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($stats['blocked_details'] as $index => $detail)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><code>{{ $detail['nis'] }}</code></td>
                    <td>{{ $detail['siswa'] }}</td>
                    <td>
                      <span class="badge bg-warning text-dark">
                        <i class="bi bi-calendar-x me-1"></i>
                        {{ $detail['existing_jatuh_tempo'] }}
                      </span>
                    </td>
                    <td>
                      <strong class="text-danger">
                        Rp {{ number_format($detail['sisa'], 0, ',', '.') }}
                      </strong>
                    </td>
                    <td>
                      <small class="text-muted">Tagihan aktif belum lunas</small>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="card-footer bg-warning bg-opacity-10">
              <div class="d-flex align-items-start">
                <i class="bi bi-info-circle me-2 text-warning"></i>
                <small class="text-muted">
                  <strong>Catatan:</strong> Siswa ini tidak dapat dibuat tagihan baru karena masih memiliki tagihan yang belum lunas dan belum melewati jatuh tempo. Tagihan baru dapat dibuat setelah tagihan lama dilunasi atau jatuh tempo terlewati.
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif

      <!-- Skipped Details (Expandable) -->
      @if($stats['skipped'] > 0 && count($stats['skipped_details']) > 0)
      <div class="card border-0 shadow-sm border-info border-2 mt-3">
        <div class="card-header bg-info bg-opacity-10">
          <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-info">
              <i class="bi bi-info-circle-fill me-2"></i>
              Siswa yang Dilewati ({{ count($stats['skipped_details']) }})
            </h6>
            <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#skippedDetails">
              <i class="bi bi-chevron-down"></i> Lihat Detail
            </button>
          </div>
        </div>
        <div class="collapse" id="skippedDetails">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width: 10%">#</th>
                    <th style="width: 20%">NIS</th>
                    <th style="width: 40%">Nama Siswa</th>
                    <th style="width: 30%">Alasan</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($stats['skipped_details'] as $index => $detail)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><code>{{ $detail['nis'] }}</code></td>
                    <td>{{ $detail['siswa'] }}</td>
                    <td>
                      <span class="badge bg-info">
                        {{ $detail['reason'] }}
                      </span>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      @endif

    </div>
  </div>
@endif

<script>
// Auto-collapse after 10 seconds
document.addEventListener('DOMContentLoaded', function() {
  setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-success, .alert-warning, .alert-info');
    alerts.forEach(alert => {
      if (alert.querySelector('.fs-3')) { // Only summary cards
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
      }
    });
  }, 10000);
});
</script>

<style>
.border-2 {
  border-width: 2px !important;
}

.table code {
  background: #f8f9fa;
  padding: 2px 6px;
  border-radius: 3px;
  font-size: 0.9em;
}
</style>
@endsection