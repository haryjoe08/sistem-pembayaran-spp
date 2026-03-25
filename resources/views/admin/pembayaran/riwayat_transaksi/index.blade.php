@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">

  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-clock-history me-2 text-primary"></i>
            Riwayat Transaksi
          </h4>
          <p class="text-muted mb-0">Catatan lengkap semua aktivitas pembayaran siswa</p>
        </div>
        <div class="badge bg-light text-dark px-3 py-2">
          <i class="bi bi-calendar3 me-1"></i>
          {{ date('d F Y') }}
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-md-6 mb-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
              <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                <i class="bi bi-receipt-cutoff text-primary fs-4"></i>
              </div>
            </div>
            <div class="flex-grow-1">
              <p class="text-muted mb-1">Total Transaksi</p>
              <h3 class="mb-0 fw-bold">{{ number_format($totalTransaksi) }}</h3>
              <small class="text-muted">Transaksi tercatat</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 mb-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
              <div class="rounded-circle bg-success bg-opacity-10 p-3">
                <i class="bi bi-cash-stack text-success fs-4"></i>
              </div>
            </div>
            <div class="flex-grow-1">
              <p class="text-muted mb-1">Total Nominal</p>
              <h3 class="mb-0 fw-bold text-success">Rp {{ number_format($totalNominal, 0, ',', '.') }}</h3>
              <small class="text-muted">Total pembayaran diterima</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter Section -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">
          <i class="bi bi-funnel me-2"></i>
          Filter & Pencarian
        </h6>
        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
          <i class="bi bi-chevron-down"></i>
        </button>
      </div>
    </div>
    <div class="collapse {{ request()->query() ? 'show' : '' }}" id="filterCollapse">
      <div class="card-body">
        <form action="{{ route('transaksi.index') }}" method="GET">
          <div class="row g-3">
            <!-- Search Keyword -->
            <div class="col-md-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-search me-1"></i>
                Cari Siswa
              </label>
              <input type="text"
                class="form-control"
                name="keyword"
                placeholder="NIS atau Nama Siswa..."
                value="{{ request('keyword') }}">
            </div>

            <!-- Date Range -->
            <div class="col-md-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-calendar-range me-1"></i>
                Dari Tanggal
              </label>
              <input type="date"
                class="form-control"
                name="dari_tanggal"
                value="{{ request('dari_tanggal') }}">
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-calendar-check me-1"></i>
                Sampai Tanggal
              </label>
              <input type="date"
                class="form-control"
                name="sampai_tanggal"
                value="{{ request('sampai_tanggal') }}">
            </div>

            <!-- Filter Siswa -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">
                <i class="bi bi-person me-1"></i>
                Filter Siswa
              </label>
              <select class="form-select" name="siswa_nis">
                <option value="">-- Semua Siswa --</option>
                @foreach($siswaList as $s)
                <option value="{{ $s->nis }}" {{ request('siswa_nis') == $s->nis ? 'selected' : '' }}>
                  {{ $s->nis }} - {{ $s->nama }}
                </option>
                @endforeach
              </select>
            </div>

            <!-- Filter Metode -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">
                <i class="bi bi-credit-card me-1"></i>
                Metode Pembayaran
              </label>
              <select class="form-select" name="metode">
                <option value="">-- Semua Metode --</option>
                <option value="cash" {{ request('metode') == 'cash' ? 'selected' : '' }}>Cash/Tunai</option>
                <option value="cstore" {{ request('metode') == 'cstore' ? 'selected' : '' }}>Convenience Store</option>
                <option value="va" {{ request('metode') == 'va' ? 'selected' : '' }}>Virtual Account</option>
                <option value="qris" {{ request('metode') == 'qris' ? 'selected' : '' }}>QRIS</option>
              </select>
            </div>

            <!-- Buttons -->
            <div class="col-12">
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-search me-1"></i>
                  Terapkan Filter
                </button>
                <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary">
                  <i class="bi bi-arrow-clockwise me-1"></i>
                  Reset
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Transaction List -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">
          <i class="bi bi-list-check me-2"></i>
          Daftar Transaksi
        </h6>
        <span class="badge bg-primary">{{ $transaksi->total() }} Transaksi</span>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="bg-light">
            <tr>
              <th class="px-4 py-3">No</th>
              <th class="py-3">Tanggal</th>
              <th class="py-3">NIS</th>
              <th class="py-3">Nama Siswa</th>
              <th class="py-3">Kelas</th>
              <th class="py-3">Jenis Tagihan</th>
              <th class="py-3">Jumlah</th>
              <th class="py-3">Metode</th>
              <th class="py-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($transaksi as $index => $t)
            <tr>
              <td class="px-4 py-3">
                {{ ($transaksi->currentPage() - 1) * $transaksi->perPage() + $loop->iteration }}
              </td>
              <td class="py-3">
                <div class="d-flex flex-column">
                  <span class="fw-semibold">{{ $t->tanggal->format('d/m/Y') }}</span>
                  <small class="text-muted">{{ $t->tanggal->format('H:i') }}</small>
                </div>
              </td>
              <td class="py-3">
                <span class="badge bg-secondary">{{ $t->siswa_nis }}</span>
              </td>
              <td class="py-3">
                <div class="d-flex align-items-center">

                  <span class="fw-semibold">{{ $t->siswa->nama }}</span>
                </div>
              </td>
              <td class="py-3">{{ $t->siswa->kelas->kelas ?? '-' }}</td>
              <td class="py-3">
                <div class="d-flex align-items-center">
                  <i class="bi bi-receipt text-primary me-2"></i>
                  {{ $t->tagihan->jenisTagihan->nama ?? '-' }}
                </div>
              </td>
              <td class="py-3">
                <span class="fw-bold text-success">
                  Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}
                </span>
              </td>
              <td class="py-3">
                @php
                $metodeBadge = [
                'cash' => ['class' => 'bg-success', 'icon' => 'cash', 'text' => 'Cash/Tunai'],
                'va' => ['class' => 'bg-info', 'icon' => 'credit-card', 'text' => 'Virtual Account'],
                'qris' => ['class' => 'bg-warning', 'icon' => 'qr-code', 'text' => 'QRIS'],
                'cstore' => ['class' => 'bg-primary', 'icon' => 'shop', 'text' => 'Indomaret / Alfamart'],
                'credit_card' => ['class' => 'bg-dark', 'icon' => 'credit-card-2-front', 'text' => 'Credit Card'],
                'unknown' => ['class' => 'bg-secondary', 'icon' => 'question', 'text' => 'Unknown'],
                ];

                $badge = $metodeBadge[$t->metode] ?? ['class' => 'bg-secondary', 'icon' => 'question', 'text' => $t->metode];
                @endphp
                <span class="badge {{ $badge['class'] }}">
                  <i class="bi bi-{{ $badge['icon'] }} me-1"></i>
                  {{ $badge['text'] }}
                </span>
              </td>
              <td class="py-3 text-center">
                <div class="btn-group btn-group-sm">
                  <a href="{{ route('transaksi.show', $t->id) }}"
                    class="btn btn-outline-primary"
                    data-bs-toggle="tooltip"
                    title="Detail">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="{{ route('transaksi.kwitansi', $t->id) }}"
                    class="btn btn-outline-success"
                    target="_blank"
                    data-bs-toggle="tooltip"
                    title="Kwitansi">
                    <i class="bi bi-printer"></i>
                  </a>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="9" class="text-center py-5">
                <div class="mb-3">
                  <i class="bi bi-inbox display-1 text-muted"></i>
                </div>
                <h6 class="text-muted">Tidak Ada Data Transaksi</h6>
                <p class="text-muted mb-0">
                  @if(request()->hasAny(['keyword', 'dari_tanggal', 'sampai_tanggal', 'siswa_nis', 'metode']))
                  Coba ubah filter pencarian Anda
                  @else
                  Belum ada transaksi yang tercatat
                  @endif
                </p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($transaksi->hasPages())
    <div class="card-footer bg-white border-top">
      <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted">
          Menampilkan {{ $transaksi->firstItem() }} - {{ $transaksi->lastItem() }} dari {{ $transaksi->total() }} transaksi
        </div>
        <div>
          {{ $transaksi->links() }}
        </div>
      </div>
    </div>
    @endif
  </div>

</div>

<style>
  /* Table Hover Effect */
  .table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
  }

  /* Badge Styling */
  .badge {
    padding: 6px 12px;
    font-weight: 500;
  }

  /* Tooltip */
  .btn-group-sm .btn {
    padding: 4px 8px;
  }

  /* Avatar */
  .avatar-sm {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  /* Responsive Table */
  @media (max-width: 768px) {
    .table-responsive {
      font-size: 0.875rem;
    }

    .card-body.p-0 {
      overflow-x: auto;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
</script>
@endsection