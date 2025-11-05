@extends('layouts.siswaMaster')

@section('content')
<div class="container-fluid p-4">
  
  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-clock-history me-2 text-primary"></i>
            Riwayat Tagihan
          </h4>
          <p class="text-muted mb-0">Daftar tagihan yang belum lunas berdasarkan bulan</p>
        </div>
        <div>
          <a href="{{ route('siswa.tagihan') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-receipt me-1"></i> Semua Tagihan
          </a>
          <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary">
            <i class="bi bi-house me-1"></i> Dashboard
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Summary Card -->
  @if($totalTunggakan > 0)
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm bg-danger text-white">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-1 me-3"></i>
            <div>
              <small class="text-white-50">Total Tunggakan</small>
              <h4 class="fw-bold mb-0">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm bg-warning text-white">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <i class="bi bi-receipt fs-1 me-3"></i>
            <div>
              <small class="text-white-50">Total Tagihan</small>
              <h4 class="fw-bold mb-0">{{ $tagihans->count() }} Tagihan</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm bg-primary text-white">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <i class="bi bi-cash-stack fs-1 me-3"></i>
            <div>
              <small class="text-white-50">Sudah Dibayar</small>
              <h4 class="fw-bold mb-0">Rp {{ number_format($tagihans->sum('sudah_dibayar'), 0, ',', '.') }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- Grouped by Month -->
  @php
    // Group tagihan by month-year
    $groupedTagihan = $tagihans->groupBy(function($item) {
      return $item->created_at->format('Y-m');
    })->sortKeysDesc();
  @endphp

  @forelse($groupedTagihan as $monthYear => $tagihanGroup)
    @php
      $monthName = \Carbon\Carbon::createFromFormat('Y-m', $monthYear)->locale('id')->isoFormat('MMMM YYYY');
      $totalMonth = $tagihanGroup->sum(function($t) {
        return $t->total_tagihan - $t->sudah_dibayar;
      });
    @endphp

    <!-- Month Section -->
    <div class="mb-4">
      <div class="d-flex align-items-center mb-3">
        <div class="flex-grow-1">
          <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-calendar3 me-2 text-primary"></i>
            {{ $monthName }}
          </h5>
        </div>
        <span class="badge bg-danger rounded-pill px-3 py-2">
          {{ $tagihanGroup->count() }} tagihan • Rp {{ number_format($totalMonth, 0, ',', '.') }}
        </span>
      </div>

      <!-- Table -->
      <div class="card border-0 shadow-sm mb-3">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="bg-light">
              <tr>
                <th class="py-3" style="width: 5%">#</th>
                <th class="py-3" style="width: 25%">Jenis Pembayaran</th>
                <th class="py-3 text-end" style="width: 15%">Total Tagihan</th>
                <th class="py-3 text-end" style="width: 15%">Sudah Dibayar</th>
                <th class="py-3 text-end" style="width: 15%">Sisa</th>
                <th class="py-3 text-center" style="width: 15%">Jatuh Tempo</th>
                <th class="py-3 text-center" style="width: 10%">Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($tagihanGroup as $index => $t)
                @php
                  $sisa = $t->total_tagihan - $t->sudah_dibayar;
                  $progress = $t->total_tagihan > 0 ? ($t->sudah_dibayar / $t->total_tagihan * 100) : 0;
                @endphp
                <tr class="{{ $t->isJatuhTempo() ? 'table-danger' : ($t->isMendekatJatuhTempo() ? 'table-warning' : '') }}">
                  <td class="align-middle">
                    <span class="badge bg-secondary rounded-circle" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                      {{ $index + 1 }}
                    </span>
                  </td>
                  <td class="align-middle">
                    <div class="d-flex align-items-center">
                      <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                        <i class="bi bi-receipt text-primary"></i>
                      </div>
                      <div>
                        <div class="fw-bold">{{ $t->jenisPembayaran->nama }}</div>
                        <small class="text-muted">{{ $t->created_at->format('d M Y') }}</small>
                      </div>
                    </div>
                  </td>
                  <td class="align-middle text-end">
                    <strong>Rp {{ number_format($t->total_tagihan, 0, ',', '.') }}</strong>
                  </td>
                  <td class="align-middle text-end">
                    <span class="text-success fw-bold">Rp {{ number_format($t->sudah_dibayar, 0, ',', '.') }}</span>
                    <div class="progress mt-1" style="height: 6px;">
                      <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
                    </div>
                  </td>
                  <td class="align-middle text-end">
                    <span class="text-danger fw-bold">Rp {{ number_format($sisa, 0, ',', '.') }}</span>
                  </td>
                  <td class="align-middle text-center">
                    @if($t->jatuh_tempo)
                      <div class="fw-bold {{ $t->isJatuhTempo() ? 'text-danger' : ($t->isMendekatJatuhTempo() ? 'text-warning' : '') }}">
                        {{ $t->jatuh_tempo->format('d/m/Y') }}
                      </div>
                      <small class="text-muted">
                        @if($t->isJatuhTempo())
                          <i class="bi bi-exclamation-triangle text-danger"></i> Lewat {{ $t->jatuh_tempo->diffForHumans() }}
                        @elseif($t->isMendekatJatuhTempo())
                          <i class="bi bi-clock text-warning"></i> {{ $t->jatuh_tempo->diffForHumans() }}
                        @else
                          {{ $t->jatuh_tempo->diffForHumans() }}
                        @endif
                      </small>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td class="align-middle text-center">
                    @if($t->isJatuhTempo())
                      <span class="badge bg-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Lewat JT
                      </span>
                    @elseif($t->isMendekatJatuhTempo())
                      <span class="badge bg-warning text-dark">
                        <i class="bi bi-clock me-1"></i>
                        Segera
                      </span>
                    @else
                      <span class="badge bg-secondary">
                        <i class="bi bi-hourglass-split me-1"></i>
                        Aktif
                      </span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
            <tfoot class="bg-light">
              <tr>
                <td colspan="4" class="py-3 text-end fw-bold">Total {{ $monthName }}:</td>
                <td class="py-3 text-end fw-bold text-danger">
                  Rp {{ number_format($totalMonth, 0, ',', '.') }}
                </td>
                <td colspan="2"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  @empty
    <!-- Empty State -->
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center py-5">
        <i class="bi bi-check-circle display-1 text-success mb-3"></i>
        <h5 class="text-success mb-2">Semua Tagihan Sudah Lunas!</h5>
        <p class="text-muted mb-3">Anda tidak memiliki tagihan yang belum dibayar</p>
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary">
          <i class="bi bi-house me-1"></i> Kembali ke Dashboard
        </a>
      </div>
    </div>
  @endforelse

  <!-- Info Footer -->
  <div class="alert alert-info mt-4">
    <div class="d-flex align-items-start">
      <i class="bi bi-info-circle-fill me-3 fs-4"></i>
      <div>
        <h6 class="alert-heading mb-2">Informasi Pembayaran</h6>
        <ul class="mb-0 ps-3">
          <li>Untuk melakukan pembayaran, silakan hubungi bagian <strong>Tata Usaha</strong></li>
          <li>Atau transfer ke rekening sekolah dan konfirmasi ke TU</li>
          <li>Simpan bukti pembayaran sebagai arsip</li>
        </ul>
      </div>
    </div>
  </div>

</div>

<style>
.table {
  font-size: 0.95rem;
}

.table tbody tr {
  transition: all 0.2s;
}

.table tbody tr:hover {
  transform: scale(1.01);
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.progress {
  border-radius: 10px;
  overflow: hidden;
}

.card {
  transition: transform 0.2s;
}

/* Sticky header untuk table */
.table-responsive {
  max-height: 600px;
  overflow-y: auto;
}

@media (max-width: 768px) {
  .table {
    font-size: 0.85rem;
  }
}
</style>
@endsection