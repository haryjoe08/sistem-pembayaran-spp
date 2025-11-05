@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">
  
  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-graph-up me-2 text-primary"></i>
            Dashboard Laporan
          </h4>
          <p class="text-muted mb-0">Ringkasan dan statistik pembayaran SPP</p>
        </div>
        <div class="badge bg-light text-dark px-3 py-2">
          <i class="bi bi-calendar3 me-1"></i>
          {{ date('d F Y') }}
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Navigation -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('laporan.pembayaran') }}" class="btn btn-outline-primary">
              <i class="bi bi-cash-stack me-1"></i> Laporan Pembayaran
            </a>
            <a href="{{ route('laporan.tunggakan') }}" class="btn btn-outline-danger">
              <i class="bi bi-exclamation-triangle me-1"></i> Laporan Tunggakan
            </a>
            <a href="{{ route('laporan.per-kelas') }}" class="btn btn-outline-success">
              <i class="bi bi-building me-1"></i> Laporan Per Kelas
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
              <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                <i class="bi bi-people text-primary fs-4"></i>
              </div>
            </div>
            <div class="flex-grow-1">
              <p class="text-muted mb-1 small">Total Siswa</p>
              <h3 class="mb-0 fw-bold">{{ number_format($totalSiswa) }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
              <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                <i class="bi bi-file-earmark-text text-warning fs-4"></i>
              </div>
            </div>
            <div class="flex-grow-1">
              <p class="text-muted mb-1 small">Tagihan Aktif</p>
              <h3 class="mb-0 fw-bold">{{ number_format($totalTagihanAktif) }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
              <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                <i class="bi bi-exclamation-circle text-danger fs-4"></i>
              </div>
            </div>
            <div class="flex-grow-1">
              <p class="text-muted mb-1 small">Total Tunggakan</p>
              <h3 class="mb-0 fw-bold text-danger">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
              <div class="rounded-circle bg-success bg-opacity-10 p-3">
                <i class="bi bi-calendar-check text-success fs-4"></i>
              </div>
            </div>
            <div class="flex-grow-1">
              <p class="text-muted mb-1 small">Pembayaran Bulan Ini</p>
              <h3 class="mb-0 fw-bold text-success">Rp {{ number_format($totalPembayaranBulanIni, 0, ',', '.') }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Chart Pembayaran -->
    <div class="col-lg-8 mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
          <h6 class="mb-0 fw-semibold">
            <i class="bi bi-bar-chart me-2"></i>
            Grafik Pembayaran 6 Bulan Terakhir
          </h6>
        </div>
        <div class="card-body">
          <canvas id="paymentChart" height="100"></canvas>
        </div>
      </div>
    </div>

    <!-- Top Jenis Pembayaran -->
    <div class="col-lg-4 mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
          <h6 class="mb-0 fw-semibold">
            <i class="bi bi-trophy me-2"></i>
            Top 5 Jenis Pembayaran
          </h6>
        </div>
        <div class="card-body p-0">
          @forelse($topJenisPembayaran as $index => $jp)
            <div class="p-3 border-bottom {{ $index === 0 ? 'bg-primary bg-opacity-10' : '' }}">
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                  <div class="badge bg-primary me-3">{{ $index + 1 }}</div>
                  <div>
                    <div class="fw-semibold">{{ $jp->nama }}</div>
                    <small class="text-muted">{{ $jp->jumlah_transaksi }} transaksi</small>
                  </div>
                </div>
                <div class="text-end">
                  <div class="fw-bold text-success">Rp {{ number_format($jp->total_nominal, 0, ',', '.') }}</div>
                </div>
              </div>
            </div>
          @empty
            <div class="text-center py-4">
              <i class="bi bi-inbox display-6 text-muted"></i>
              <p class="text-muted mb-0 mt-2">Belum ada data</p>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('paymentChart');
  
  // Data dari controller
  const grafikData = @json($grafikPembayaran);
  
  const labels = grafikData.map(item => {
    const bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Oct', 'Nov', 'Des'];
    return bulan[item.bulan - 1] + ' ' + item.tahun;
  });
  
  const data = grafikData.map(item => item.total);
  
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Total Pembayaran',
        data: data,
        backgroundColor: 'rgba(102, 126, 234, 0.8)',
        borderColor: 'rgba(102, 126, 234, 1)',
        borderWidth: 1,
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
            }
          }
        }
      }
    }
  });
});
</script>
@endsection