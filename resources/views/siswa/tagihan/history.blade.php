@extends('layouts.siswaMaster')

@section('content')
<div class="container-fluid p-4">
  
  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-clock-history me-2 text-success"></i>
            History Pembayaran
          </h4>
          <p class="text-muted mb-0">Riwayat transaksi pembayaran Anda</p>
        </div>
        <div class="badge bg-light text-dark px-3 py-2">
          <i class="bi bi-calendar3 me-1"></i>
          {{ date('d F Y') }}
        </div>
      </div>
    </div>
  </div>

  <!-- Summary Card -->
  <div class="row mb-4">
    <div class="col-md-4 mx-auto">
      <div class="card border-0 shadow-sm bg-success text-white">
        <div class="card-body text-center">
          <i class="bi bi-cash-stack display-6 mb-2"></i>
          <p class="mb-1 opacity-75">Total Pembayaran</p>
          <h3 class="fw-bold mb-0">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</h3>
          <small class="opacity-75">{{ $transaksi->total() }} Transaksi</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Timeline Transaksi -->
  <div class="row">
    <div class="col-12">
      @forelse($transaksi as $t)
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body p-4">
            <div class="row align-items-center">
              <!-- Date -->
              <div class="col-lg-2 mb-3 mb-lg-0">
                <div class="text-center">
                  <div class="rounded-circle bg-success bg-opacity-10 p-3 d-inline-flex">
                    <i class="bi bi-check-circle text-success fs-4"></i>
                  </div>
                  <div class="mt-2">
                    <div class="fw-bold">{{ $t->tanggal->format('d M Y') }}</div>
                    <small class="text-muted">{{ $t->tanggal->format('H:i') }}</small>
                  </div>
                </div>
              </div>

              <!-- Details -->
              <div class="col-lg-6 mb-3 mb-lg-0">
                <h6 class="fw-bold mb-2">{{ $t->tagihan->jenisPembayaran->nama ?? 'Pembayaran' }}</h6>
                <div class="d-flex gap-3 flex-wrap">
                  <div>
                    <small class="text-muted d-block">ID Transaksi</small>
                    <span class="badge bg-secondary">#{{ str_pad($t->id, 6, '0', STR_PAD_LEFT) }}</span>
                  </div>
                  <div>
                    <small class="text-muted d-block">Metode</small>
                    @php
                      $metodeBadge = [
                        'cash' => ['class' => 'bg-success', 'text' => 'Cash'],
                        'transfer' => ['class' => 'bg-primary', 'text' => 'Transfer'],
                        'va' => ['class' => 'bg-info', 'text' => 'VA'],
                        'qris' => ['class' => 'bg-warning', 'text' => 'QRIS'],
                      ];
                      $badge = $metodeBadge[$t->metode] ?? ['class' => 'bg-secondary', 'text' => $t->metode];
                    @endphp
                    <span class="badge {{ $badge['class'] }}">{{ $badge['text'] }}</span>
                  </div>
                </div>
                @if($t->keterangan)
                  <div class="mt-2">
                    <small class="text-muted">
                      <i class="bi bi-chat-left-text me-1"></i>
                      {{ $t->keterangan }}
                    </small>
                  </div>
                @endif
              </div>

              <!-- Amount -->
              <div class="col-lg-3 mb-3 mb-lg-0">
                <div class="text-lg-end">
                  <small class="text-muted d-block">Jumlah Pembayaran</small>
                  <h4 class="fw-bold text-success mb-0">
                    Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}
                  </h4>
                </div>
              </div>

              <!-- Action -->
              <div class="col-lg-1 text-lg-center">
                <a href="{{ route('siswa.kwitansi', $t->id) }}" 
                   class="btn btn-outline-primary btn-sm"
                   target="_blank"
                   data-bs-toggle="tooltip"
                   title="Lihat Kwitansi">
                  <i class="bi bi-printer"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="card border-0 shadow-sm">
          <div class="card-body text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <h5 class="text-muted mt-3 mb-2">Belum Ada Transaksi</h5>
            <p class="text-muted mb-3">Anda belum melakukan pembayaran</p>
            <a href="{{ route('siswa.tagihan') }}" class="btn btn-primary">
              <i class="bi bi-file-earmark-text me-1"></i> Lihat Tagihan
            </a>
          </div>
        </div>
      @endforelse
    </div>
  </div>

  <!-- Pagination -->
  @if($transaksi->hasPages())
    <div class="row mt-4">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
              <div class="text-muted">
                Menampilkan {{ $transaksi->firstItem() }} - {{ $transaksi->lastItem() }} dari {{ $transaksi->total() }} transaksi
              </div>
              <div>
                {{ $transaksi->links() }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>
@endsection