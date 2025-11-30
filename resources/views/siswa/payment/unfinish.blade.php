@extends('layouts.siswaMaster')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      
      <!-- Icon & Message -->
      <div class="text-center mb-4">
        <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
        <h3 class="fw-bold text-warning mt-3">Pembayaran Belum Selesai</h3>
        <p class="text-muted">Anda menutup halaman pembayaran sebelum menyelesaikan transaksi</p>
      </div>

      <!-- Info Card -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
          <div class="alert alert-warning">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Apa yang terjadi?</strong>
            <ul class="mb-0 mt-2 ps-3">
              <li>Anda membatalkan proses pembayaran</li>
              <li>Waktu pembayaran habis (timeout)</li>
              <li>Halaman pembayaran ditutup sebelum selesai</li>
            </ul>
          </div>

          @if(isset($orderId))
          <div class="mb-3">
            <small class="text-muted d-block">Order ID</small>
            <p class="fw-bold mb-0">{{ $orderId }}</p>
          </div>
          @endif

          <hr>

          <h6 class="fw-bold mb-2">
            <i class="bi bi-question-circle me-2"></i>
            Apa yang harus dilakukan?
          </h6>
          <ul class="text-muted ps-3">
            <li>Anda dapat mencoba melakukan pembayaran kembali</li>
            <li>Pastikan koneksi internet stabil</li>
            <li>Jika masalah berlanjut, hubungi bagian Tata Usaha</li>
          </ul>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="d-grid gap-2">
        @if(isset($orderId))
          @php
            $paymentOrder = \App\Models\PaymentOrder::where('order_id', $orderId)->first();
          @endphp
          @if($paymentOrder)
            <a href="{{ route('payment.index', $paymentOrder->tagihan_id) }}" 
               class="btn btn-primary btn-lg">
              <i class="bi bi-arrow-repeat me-2"></i>
              Coba Bayar Lagi
            </a>
          @endif
        @endif
        
        <a href="{{ route('siswa.tagihan.belum-lunas') }}" 
           class="btn btn-outline-primary">
          <i class="bi bi-receipt me-2"></i>
          Lihat Tagihan
        </a>
        
        <a href="{{ route('siswa.dashboard') }}" 
           class="btn btn-outline-secondary">
          <i class="bi bi-house me-2"></i>
          Kembali ke Dashboard
        </a>
      </div>

      <!-- Help Section -->
      <div class="card border-0 bg-light mt-4">
        <div class="card-body text-center p-4">
          <i class="bi bi-headset fs-1 text-primary mb-3"></i>
          <h6 class="fw-bold">Butuh Bantuan?</h6>
          <p class="text-muted small mb-3">
            Hubungi bagian Tata Usaha untuk bantuan lebih lanjut
          </p>
          <div class="d-flex justify-content-center gap-2">
            <a href="tel:+6281234567890" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-telephone me-1"></i>
              Telepon
            </a>
            <a href="https://wa.me/6281234567890" class="btn btn-sm btn-outline-success">
              <i class="bi bi-whatsapp me-1"></i>
              WhatsApp
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection