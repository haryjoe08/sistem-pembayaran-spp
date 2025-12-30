{{-- resources/views/admin/laporan/pembayaran.blade.php --}}
@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">

  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-cash-stack me-2 text-primary"></i>
            Laporan Pembayaran
          </h4>
          <p class="text-muted mb-0">Rincian transaksi pembayaran per periode</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
          </a>
          <a href="{{ route('laporan.export.pembayaran', request()->all()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics -->
  <div class="row mb-4">
    <div class="col-md-3 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <i class="bi bi-receipt display-6 text-primary mb-2"></i>
          <h3 class="fw-bold mb-0">{{ number_format($totalTransaksi) }}</h3>
          <p class="text-muted mb-0 small">Total Transaksi</p>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <i class="bi bi-currency-dollar display-6 text-success mb-2"></i>
          <h3 class="fw-bold mb-0 text-success">Rp {{ number_format($totalNominal, 0, ',', '.') }}</h3>
          <p class="text-muted mb-0 small">Total Nominal</p>
        </div>
      </div>
    </div>
    @foreach($groupByMetode as $metode => $data)
    <div class="col-md-3 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          @php
          $icons = ['cash' => 'cash', 'transfer' => 'bank', 'va' => 'credit-card', 'qris' => 'qr-code'];
          $names = ['cash' => 'Cash', 'transfer' => 'Transfer', 'va' => 'VA', 'qris' => 'QRIS'];
          @endphp
          <i class="bi bi-{{ $icons[$metode] ?? 'credit-card' }} display-6 text-info mb-2"></i>
          <h5 class="fw-bold mb-0">{{ $data['jumlah'] }}</h5>
          <p class="text-muted mb-0 small">{{ $names[$metode] ?? $metode }}</p>
          <small class="text-success">Rp {{ number_format($data['total'], 0, ',', '.') }}</small>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  <!-- Filter -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
      <h6 class="mb-0 fw-semibold">
        <i class="bi bi-funnel me-2"></i>
        Filter Laporan
      </h6>
    </div>
    <div class="card-body">
      <form action="{{ route('laporan.pembayaran') }}" method="GET">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" class="form-control" name="dari_tanggal" value="{{ $dariTanggal }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" class="form-control" name="sampai_tanggal" value="{{ $sampaiTanggal }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Kelas</label>
            <select class="form-select" name="kelas_id">
              <option value="">Semua Kelas</option>
              @foreach($kelasList as $k)
              <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->kelas }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Jenis Tagihan</label>
            <select class="form-select" name="jenis_tagihan_id">
              <option value="">Semua Jenis</option>
              @foreach($jenisPembayaranList as $jp)
              <option value="{{ $jp->id }}" {{ request('jenis_tagihan_id') == $jp->id ? 'selected' : '' }}>{{ $jp->nama }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Metode</label>
            <select class="form-select" name="metode">
              <option value="">Semua Metode</option>
              <option value="cash" {{ request('metode') == 'cash' ? 'selected' : '' }}>Cash</option>
              <option value="transfer" {{ request('metode') == 'transfer' ? 'selected' : '' }}>Transfer</option>
              <option value="va" {{ request('metode') == 'va' ? 'selected' : '' }}>VA</option>
              <option value="qris" {{ request('metode') == 'qris' ? 'selected' : '' }}>QRIS</option>
            </select>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-search me-1"></i> Terapkan Filter
            </button>
            <a href="{{ route('laporan.pembayaran') }}" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-clockwise me-1"></i> Reset
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
      <h6 class="mb-0 fw-semibold">
        <i class="bi bi-list-check me-2"></i>
        Data Transaksi ({{ $totalTransaksi }} transaksi)
      </h6>
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
              <th class="py-3 text-end">Jumlah</th>
              <th class="py-3">Metode</th>
            </tr>
          </thead>
          <tbody>
            @forelse($transaksi as $index => $t)
            <tr>
              <td class="px-4 py-3">{{ $transaksi->firstItem() + $index }}</td>
              <td class="py-3">{{ $t->tanggal->format('d/m/Y H:i') }}</td>
              <td class="py-3">{{ $t->siswa_nis }}</td>
              <td class="py-3">{{ $t->siswa->nama }}</td>
              <td class="py-3">{{ $t->siswa->kelas->kelas ?? '-' }}</td>
              <td class="py-3">{{ $t->tagihan->jenisTagihan->nama ?? '-' }}</td>
              <td class="py-3 text-end fw-bold text-success">Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}</td>
              <td class="py-3">
                @php
                $badge = ['cash' => 'success', 'transfer' => 'primary', 'va' => 'info', 'qris' => 'warning'];
                @endphp
                <span class="badge bg-{{ $badge[$t->metode] ?? 'secondary' }}">{{ strtoupper($t->metode) }}</span>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center py-5">
                <i class="bi bi-inbox display-6 text-muted"></i>
                <p class="text-muted mb-0 mt-2">Tidak ada data transaksi</p>
              </td>
            </tr>
            @endforelse
          </tbody>
          @if($transaksi->count() > 0)
          <tfoot class="bg-light">
            <tr>
              <th colspan="6" class="px-4 py-3 text-end">TOTAL:</th>
              <th class="py-3 text-end text-success">Rp {{ number_format($totalNominal, 0, ',', '.') }}</th>
              <th></th>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>
    </div>
    
    {{-- Pagination --}}
    @if($transaksi->hasPages())
    <div class="card-footer bg-white border-top">
      <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted small">
          Menampilkan {{ $transaksi->firstItem() }} sampai {{ $transaksi->lastItem() }} dari {{ $transaksi->total() }} transaksi
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
  @media print {
    .btn, .card-header, nav {
      display: none !important;
    }
    .card {
      box-shadow: none !important;
      border: 1px solid #ddd !important;
    }
  }
</style>
@endsection