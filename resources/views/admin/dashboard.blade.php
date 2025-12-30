@extends('layouts.adminMaster')

@section('content')
<h1 class="mx-5">
  Sealamat Datang 
</h1>

<div class="mx-5">
  <!-- Info boxes -->
  <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-primary shadow-sm">
          <i class="bi bi-people-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Jumlah Siswa</span>
          <span class="info-box-number">{{ $jumlahSiswa }}</span>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-success shadow-sm">
          <i class="bi bi-check-circle-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Jumlah Tagihan Lunas</span>
          <span class="info-box-number">{{ $jumlahLunas }}</span>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-danger shadow-sm">
          <i class="bi bi-x-circle-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Jumlah Tagihan Belum Lunas</span>
          <span class="info-box-number">{{ $jumlahBelumLunas }}</span>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-warning shadow-sm">
          <i class="bi bi-cash-coin"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Tunggakan</span>
          <span class="info-box-number">
            Rp{{ number_format($totalTunggakan, 0, ',', '.') }}
          </span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row m-4">
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

    <!-- Top Jenis Tagihan -->
    <div class="col-lg-4 mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
          <h6 class="mb-0 fw-semibold">
            <i class="bi bi-trophy me-2"></i>
            Top 5 Jenis Tagihan
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