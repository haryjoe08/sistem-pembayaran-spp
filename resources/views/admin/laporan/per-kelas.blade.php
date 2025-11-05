@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">

  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-building me-2 text-success"></i>
            Laporan Per Kelas
          </h4>
          <p class="text-muted mb-0">Ringkasan pembayaran dan tunggakan per kelas</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
          </a>
          <a href="{{ route('laporan.export.per-kelas') }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
          </a>
          <button class="btn btn-success" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="row mb-4">
    @php
    $totalSemuaSiswa = $laporanKelas->sum('jumlah_siswa');
    $totalSemuaTagihan = $laporanKelas->sum('total_tagihan');
    $totalSemuaTunggakan = $laporanKelas->sum('total_tunggakan');
    $persentaseLunas = $totalSemuaTagihan > 0 ? ($laporanKelas->sum('tagihan_lunas') / $totalSemuaTagihan * 100) : 0;
    @endphp

    <div class="col-md-3 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <i class="bi bi-building display-6 text-primary mb-2"></i>
          <h3 class="fw-bold mb-0">{{ $laporanKelas->count() }}</h3>
          <p class="text-muted mb-0 small">Total Kelas</p>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <i class="bi bi-people display-6 text-info mb-2"></i>
          <h3 class="fw-bold mb-0">{{ number_format($totalSemuaSiswa) }}</h3>
          <p class="text-muted mb-0 small">Total Siswa</p>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <i class="bi bi-check-circle display-6 text-success mb-2"></i>
          <h3 class="fw-bold mb-0">{{ number_format($persentaseLunas, 1) }}%</h3>
          <p class="text-muted mb-0 small">Tingkat Pelunasan</p>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <i class="bi bi-currency-dollar display-6 text-danger mb-2"></i>
          <h3 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalSemuaTunggakan, 0, ',', '.') }}</h3>
          <p class="text-muted mb-0 small">Total Tunggakan</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Table per Kelas -->
  @foreach($laporanKelas as $laporan)
  @php
  $persenLunas = $laporan['total_tagihan'] > 0 ? ($laporan['tagihan_lunas'] / $laporan['total_tagihan'] * 100) : 0;
  $persenBelumLunas = 100 - $persenLunas;
  @endphp

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0 fw-bold">{{ $laporan['kelas']->kelas }}</h5>
          <small class="text-muted">{{ $laporan['jumlah_siswa'] }} Siswa</small>
        </div>
        <div class="text-end">
          <span class="badge bg-{{ $persenLunas >= 80 ? 'success' : ($persenLunas >= 50 ? 'warning' : 'danger') }} px-3 py-2">
            {{ number_format($persenLunas, 1) }}% Lunas
          </span>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <!-- Statistics -->
        <div class="col-md-6 mb-3">
          <div class="row g-2">
            <div class="col-6">
              <div class="p-3 bg-light rounded">
                <small class="text-muted d-block">Total Tagihan</small>
                <h6 class="fw-bold mb-0">{{ $laporan['total_tagihan'] }}</h6>
              </div>
            </div>
            <div class="col-6">
              <div class="p-3 bg-success bg-opacity-10 rounded">
                <small class="text-muted d-block">Lunas</small>
                <h6 class="fw-bold mb-0 text-success">{{ $laporan['tagihan_lunas'] }}</h6>
              </div>
            </div>
            <div class="col-6">
              <div class="p-3 bg-warning bg-opacity-10 rounded">
                <small class="text-muted d-block">Belum Lunas</small>
                <h6 class="fw-bold mb-0 text-warning">{{ $laporan['tagihan_belum_lunas'] }}</h6>
              </div>
            </div>
            <div class="col-6">
              <div class="p-3 bg-danger bg-opacity-10 rounded">
                <small class="text-muted d-block">Tunggakan</small>
                <h6 class="fw-bold mb-0 text-danger">Rp {{ number_format($laporan['total_tunggakan'] / 1000, 0) }}K</h6>
              </div>
            </div>
          </div>
        </div>

        <!-- Financial Details -->
        <div class="col-md-6 mb-3">
          <div class="p-3 border rounded">
            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
              <span class="text-muted">Total Nominal Tagihan:</span>
              <strong>Rp {{ number_format($laporan['total_nominal_tagihan'], 0, ',', '.') }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
              <span class="text-muted">Sudah Dibayar:</span>
              <strong class="text-success">Rp {{ number_format($laporan['total_sudah_dibayar'], 0, ',', '.') }}</strong>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted">Sisa Tunggakan:</span>
              <strong class="text-danger">Rp {{ number_format($laporan['total_tunggakan'], 0, ',', '.') }}</strong>
            </div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="col-12">
          <div class="mb-2">
            <small class="text-muted">Progress Pembayaran</small>
          </div>
          <div class="progress" style="height: 25px;">
            <div class="progress-bar bg-success"
              style="width: {{ $persenLunas }}%"
              role="progressbar">
              {{ number_format($persenLunas, 1) }}%
            </div>
            <div class="progress-bar bg-warning"
              style="width: {{ $persenBelumLunas }}%"
              role="progressbar">
              {{ number_format($persenBelumLunas, 1) }}%
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endforeach

  @if($laporanKelas->isEmpty())
  <div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
      <i class="bi bi-inbox display-1 text-muted"></i>
      <h5 class="text-muted mt-3">Tidak Ada Data Kelas</h5>
      <p class="text-muted mb-0">Belum ada data kelas yang terdaftar</p>
    </div>
  </div>
  @endif

</div>

<style>
  .progress {
    border-radius: 10px;
    overflow: hidden;
  }

  .progress-bar {
    transition: width 0.6s ease;
    font-weight: 600;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  @media print {

    .btn,
    nav {
      display: none !important;
    }

    .card {
      box-shadow: none !important;
      border: 1px solid #ddd !important;
      page-break-inside: avoid;
    }

    .card-body {
      padding: 15px !important;
    }
  }
</style>
@endsection