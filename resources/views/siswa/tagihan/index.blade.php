@extends('layouts.siswaMaster')

@section('content')
<div class="container-fluid p-4">

  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-file-earmark-text me-2 text-primary"></i>
            Daftar Tagihan
          </h4>
          <p class="text-muted mb-0">Rincian semua tagihan pembayaran Anda</p>
        </div>
        <div class="badge bg-light text-dark px-3 py-2">
          <i class="bi bi-calendar3 me-1"></i>
          {{ date('d F Y') }}
        </div>
      </div>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
              <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                <i class="bi bi-file-earmark-text text-primary fs-5"></i>
              </div>
            </div>
            <div class="flex-grow-1">
              <p class="text-muted mb-1 small">Total Tagihan</p>
              <h5 class="mb-0 fw-bold">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h5>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
              <div class="rounded-circle bg-success bg-opacity-10 p-3">
                <i class="bi bi-check-circle text-success fs-5"></i>
              </div>
            </div>
            <div class="flex-grow-1">
              <p class="text-muted mb-1 small">Sudah Dibayar</p>
              <h5 class="mb-0 fw-bold text-success">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</h5>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
              <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                <i class="bi bi-exclamation-triangle text-danger fs-5"></i>
              </div>
            </div>
            <div class="flex-grow-1">
              <p class="text-muted mb-1 small">Sisa Tunggakan</p>
              <h5 class="mb-0 fw-bold text-danger">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h5>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
              <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                <i class="bi bi-clock text-warning fs-5"></i>
              </div>
            </div>
            <div class="flex-grow-1">
              <p class="text-muted mb-1 small">Belum Lunas</p>
              <h5 class="mb-0 fw-bold text-warning">{{ $jumlahBelumLunas }} Item</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Filter -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('siswa.tagihan') }}" class="btn btn-{{ !request()->routeIs('siswa.tagihan.semua-tagihan') ? 'primary' : 'outline-primary' }}">
              <i class="bi bi-list me-1"></i> Tagihan Aktif
            </a>
            <a href="{{ route('siswa.tagihan.semua-tagihan') }}" class="btn btn-{{ request()->routeIs('siswa.tagihan.semua-tagihan') ? 'warning' : 'outline-warning' }}">
              <i class="bi bi-exclamation-circle me-1"></i> Semua Tagihan
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Table Tagihan -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">
          <i class="bi bi-list-check me-2"></i>
          Rincian Tagihan
        </h6>
        <span class="badge bg-primary">{{ $tagihans->total() }} Tagihan</span>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="bg-light">
            <tr>
              <th class="px-4 py-3">No</th>
              <th class="py-3">Jenis Pembayaran</th>
              <th class="py-3">Total Tagihan</th>
              <th class="py-3">Sudah Dibayar</th>
              <th class="py-3">Sisa</th>
              <th class="py-3">Jatuh Tempo</th>
              <th class="py-3">Status</th>
              <th class="py-3 text-center">Progress</th>
              <th class="py-3 ">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tagihans as $t)
            @php
            $sisa = $t->total_tagihan - $t->sudah_dibayar;
            $progress = $t->total_tagihan > 0 ? ($t->sudah_dibayar / $t->total_tagihan * 100) : 0;
            @endphp
            <tr>
              <td class="px-4 py-3">
                {{ ($tagihans->currentPage() - 1) * $tagihans->perPage() + $loop->iteration }}
              </td>
              <td class="py-3">
                <div class="d-flex align-items-center">
                  <i class="bi bi-receipt text-primary me-2"></i>
                  <span class="fw-semibold">{{ $t->jenisPembayaran->nama }}</span>
                </div>
              </td>
              <td class="py-3">Rp {{ number_format($t->total_tagihan, 0, ',', '.') }}</td>
              <td class="py-3 text-success">Rp {{ number_format($t->sudah_dibayar, 0, ',', '.') }}</td>
              <td class="py-3">
                <span class="fw-bold text-danger">Rp {{ number_format($sisa, 0, ',', '.') }}</span>
              </td>
              <td class="py-3">
                @if($t->jatuh_tempo)
                <div class="d-flex flex-column">
                  <span class="{{ $t->isJatuhTempo() ? 'text-danger fw-bold' : ($t->isMendekatJatuhTempo() ? 'text-warning fw-bold' : '') }}">
                    {{ $t->jatuh_tempo->format('d/m/Y') }}
                  </span>
                  @if($t->isJatuhTempo())
                  <small class="badge bg-danger">Sudah Lewat</small>
                  @elseif($t->isMendekatJatuhTempo())
                  <small class="badge bg-warning">{{ $t->jatuh_tempo->diffForHumans() }}</small>
                  @endif
                </div>
                @else
                <span class="text-muted">-</span>
                @endif
              </td>
              <td class="py-3">
                <span class="badge {{ $t->status == 'lunas' ? 'bg-success' : 'bg-warning' }} px-3 py-2">
                  <i class="bi bi-{{ $t->status == 'lunas' ? 'check-circle' : 'clock' }} me-1"></i>
                  {{ ucfirst($t->status) }}
                </span>
              </td>
              <td class="py-3">
                <div class="progress" style="height: 25px; min-width: 120px;">
                  <div class="progress-bar {{ $t->status == 'lunas' ? 'bg-success' : 'bg-primary' }}"
                    style="width: {{ $progress }}%"
                    role="progressbar">
                    {{ number_format($progress, 0) }}%
                  </div>
                </div>
              </td>
              <td class="py-3">
                <!-- Di siswa/tagihan/belum-lunas.blade.php -->
                <a href="{{ route('payment.index', $t->id) }}"
                  class="btn btn-primary">
                  <i class="bi bi-credit-card me-1"></i>
                  Bayar Online
                </a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center py-5">
                <i class="bi bi-inbox display-6 text-muted"></i>
                <p class="text-muted mb-0 mt-2">Tidak ada tagihan</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($tagihans->hasPages())
    <div class="card-footer bg-white border-top">
      <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted">
          Menampilkan {{ $tagihans->firstItem() }} - {{ $tagihans->lastItem() }} dari {{ $tagihans->total() }} tagihan
        </div>
        <div>
          {{ $tagihans->links() }}
        </div>
      </div>
    </div>
    @endif
  </div>

</div>

<style>
  .progress {
    border-radius: 10px;
    overflow: hidden;
  }

  .progress-bar {
    font-weight: 600;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
  }
</style>
@endsection