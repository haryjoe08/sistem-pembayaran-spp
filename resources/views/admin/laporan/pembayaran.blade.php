{{-- resources/views/admin/laporan/pembayaran.blade.php --}}
@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">

  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
      <div>
        <h4 class="fw-bold mb-1">
          <i class="bi bi-cash-stack me-2 text-primary"></i>
          Laporan Pembayaran
        </h4>
        <p class="text-muted mb-0">Rincian transaksi pembayaran per periode</p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('laporan.export.pembayaran', request()->all()) }}" class="btn btn-success">
          <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        <a href="{{ route('laporan.pembayaran.print', request()->all()) }}"
          class="btn btn-primary"
          target="_blank">
          <i class="bi bi-printer"></i> Print
        </a>
      </div>
    </div>
  </div>

  <!-- Statistik -->
  <div class="row mb-4">
    <div class="col-md-3 mb-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-receipt display-6 text-primary mb-2"></i>
          <h3 class="fw-bold">{{ number_format($totalTransaksi) }}</h3>
          <small class="text-muted">Total Transaksi</small>
        </div>
      </div>
    </div>

    <div class="col-md-3 mb-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-cash-coin display-6 mb-2"></i>

          <h3 class="fw-bold text-success">
            Rp {{ number_format($totalNominal, 0, ',', '.') }}
          </h3>
          <small class="text-muted">Total Nominal</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter -->
  <div class="card border-0 shadow-sm mb-4 no-print">
    <div class="card-header bg-white border-bottom">
      <h6 class="mb-0 fw-semibold"> <i class="bi bi-funnel me-2"></i> Filter Laporan </h6>
    </div>
    <div class="card-body">
      <form action="{{ route('laporan.pembayaran') }}" method="GET">
        <div class="row g-3">
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
          <div class="col-md-3"> <label class="form-label">Dari Tanggal</label> <input type="date" class="form-control" name="dari_tanggal" value="{{ $dariTanggal }}"> </div>
          <div class="col-md-3"> <label class="form-label">Sampai Tanggal</label> <input type="date" class="form-control" name="sampai_tanggal" value="{{ $sampaiTanggal }}"> </div>
          <div class="col-md-2"> <label class="form-label">Kelas</label> <select class="form-select" name="kelas_id">
              <option value="">Semua Kelas</option> @foreach($kelasList as $k) <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->kelas }}</option> @endforeach
            </select> </div>
          <div class="col-md-2"> <label class="form-label">Jenis Tagihan</label> <select class="form-select" name="jenis_tagihan_id">
              <option value="">Semua Jenis</option> @foreach($jenisPembayaranList as $jp) <option value="{{ $jp->id }}" {{ request('jenis_tagihan_id') == $jp->id ? 'selected' : '' }}>{{ $jp->nama }}</option> @endforeach
            </select> </div>
          <div class="col-md-2"> <label class="form-label">Metode</label> <select class="form-select" name="metode">
              <option value="">Semua Metode</option>
              <option value="cash" {{ request('metode') == 'cash' ? 'selected' : '' }}>Cash</option>
              <option value="cstore" {{ request('metode') == 'cstore' ? 'selected' : '' }}>Indomaret/Alfamart</option>
              <option value="va" {{ request('metode') == 'va' ? 'selected' : '' }}>VA</option>
              <option value="qris" {{ request('metode') == 'qris' ? 'selected' : '' }}>QRIS</option>
            </select> </div>
          <div class="col-12"> <button type="submit" class="btn btn-primary"> <i class="bi bi-search me-1"></i> Terapkan Filter </button> <a href="{{ route('laporan.pembayaran') }}" class="btn btn-outline-secondary"> <i class="bi bi-arrow-clockwise me-1"></i> Reset </a> </div>
        </div>
      </form>
    </div>
  </div>
  <!-- Table -->
  <div class="card shadow-sm">
    <div class="card-header bg-white">
      <h6 class="mb-0 fw-semibold">
        <i class="bi bi-list-check me-2"></i>
        Data Transaksi ({{ $totalTransaksi }} transaksi)
      </h6>
    </div>

    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th width="40">No</th>
            <th>Tanggal</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Jenis Tagihan</th>
            <th class="text-end">Jumlah</th>
            <th class="text-center">Metode</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transaksi as $t)
          <tr>
            <td>{{ $transaksi->firstItem() + $loop->index }}</td>
            <td>{{ $t->tanggal->format('d/m/Y H:i') }}</td>
            <td>{{ $t->siswa_nis }}</td>
            <td>{{ $t->siswa->nama }}</td>
            <td>{{ $t->siswa->kelas->kelas ?? '-' }}</td>
            <td class="text-uppercase">{{ $t->tagihan->jenisTagihan->nama ?? '-' }} {{ $t->tagihan->periode }}</td>
            <td class="text-end fw-bold text-success">
              Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}
            </td>
            <td class="text-center">
              <span class="badge bg-secondary">
                {{ $t->metode === 'cstore' ? 'Indomaret / Alfamart' : strtoupper($t->metode) }}
              </span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-4 text-muted">
              Tidak ada data transaksi
            </td>
          </tr>
          @endforelse
        </tbody>

        @if($transaksi->count())
        <tfoot class="table-light">
          <tr>
            <th colspan="6" class="text-end">TOTAL</th>
            <th class="text-end text-success">
              Rp {{ number_format($totalNominal, 0, ',', '.') }}
            </th>
            <th></th>
          </tr>
        </tfoot>
        @endif
      </table>
    </div>

    @if($transaksi->hasPages())
    <div class="card-footer bg-white d-flex justify-content-between">
      <small class="text-muted">
        Menampilkan {{ $transaksi->firstItem() }}–{{ $transaksi->lastItem() }}
        dari {{ $transaksi->total() }} transaksi
      </small>
      {{ $transaksi->links() }}
    </div>
    @endif
  </div>

</div>
@endsection