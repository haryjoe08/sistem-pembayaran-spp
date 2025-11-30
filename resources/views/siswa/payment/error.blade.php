@extends('layouts.siswaMaster')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      
      <!-- Icon & Message -->
      <div class="text-center mb-4">
        <i class="bi bi-x-circle display-1 text-danger"></i>
        <h3 class="fw-bold text-danger mt-3">Pembayaran Gagal</h3>
        <p class="text-muted">Terjadi kesalahan saat memproses pembayaran Anda</p>
      </div>

      <!-- Error Info Card -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Penyebab Kemungkinan:</strong>
            <ul class="mb-0 mt-2 ps-3">
              <li>Saldo atau limit kartu tidak mencukupi</li>
              <li>Transaksi ditolak oleh bank</li>
              <li>Terjadi masalah teknis pada sistem pembayaran</li>
              <li>Waktu transaksi habis (timeout)</li>
              <li>Data pembayaran tidak valid</li>
            </ul>
          </div>

          @if(isset($orderId))
          <div class="mb-3">
            <small class="text-muted d-block">Order ID</small>
            <p class="fw-bold mb-0 font-monospace">{{ $orderId }}</p>
            <small class="text-muted">Simpan ID ini untuk keperluan konfirmasi</small>
          </div>
          @endif

          <hr>

          <h6 class="fw-bold mb-2">
            <i class="bi bi-lightbulb me-2"></i>
            Langkah Selanjutnya
          </h6>
          <div class="list-group list-group-flush">
            <div class="list-group-item px-0">
              <div class="d-flex align-items-start">
                <span class="badge bg-primary me-3">1</span>
                <div class="flex-grow-1">
                  <strong>Periksa saldo atau limit kartu Anda</strong>
                  <p class="text-muted small mb-0">Pastikan dana mencukupi untuk transaksi</p>
                </div>
              </div>
            </div>
            <div class="list-group-item px-0">
              <div class="d-flex align-items-start">
                <span class="badge bg-primary me-3">2</span>
                <div class="flex-grow-1">
                  <strong>Coba metode pembayaran lain</strong>
                  <p class="text-muted small mb-0">Gunakan metode pembayaran yang berbeda</p>
                </div>
              </div>
            </div>
            <div class="list-group-item px-0">
              <div class="d-flex align-items-start">
                <span class="badge bg-primary me-3">3</span>
                <div class="flex-grow-1">
                  <strong>Hubungi bank Anda</strong>
                  <p class="text-muted small mb-0">Jika masalah berlanjut, konfirmasi dengan bank penerbit</p>
                </div>
              </div>
            </div>
          </div>
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
              Coba Lagi dengan Metode Lain
            </a>
          @endif
        @endif
        
        <a href="{{ route('siswa.tagihan.belum-lunas') }}" 
           class="btn btn-outline-primary">
          <i class="bi bi-receipt me-2"></i>
          Lihat Tagihan Lainnya
        </a>
        
        <a href="{{ route('siswa.dashboard') }}" 
           class="btn btn-outline-secondary">
          <i class="bi bi-house me-2"></i>
          Kembali ke Dashboard
        </a>
      </div>

      <!-- Alternative Payment Info -->
      <div class="card border-0 bg-light mt-4">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3">
            <i class="bi bi-info-circle text-primary me-2"></i>
            Alternatif Pembayaran
          </h6>
          <p class="text-muted small mb-3">
            Jika pembayaran online mengalami kendala, Anda dapat melakukan pembayaran secara langsung:
          </p>
          <ul class="text-muted small ps-3 mb-3">
            <li>Datang langsung ke bagian <strong>Tata Usaha</strong></li>
            <li>Transfer manual ke rekening sekolah dan konfirmasi</li>
            <li>Hubungi kami untuk bantuan lebih lanjut</li>
          </ul>
          
          <div class="d-flex justify-content-center gap-2">
            <a href="tel:+6281234567890" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-telephone me-1"></i>
              Telepon
            </a>
            <a href="https://wa.me/6281234567890" class="btn btn-sm btn-outline-success">
              <i class="bi bi-whatsapp me-1"></i>
              WhatsApp
            </a>
            <a href="mailto:tu@school.com" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-envelope me-1"></i>
              Email
            </a>
          </div>
        </div>
      </div>

      <!-- Technical Support Note -->
      <div class="alert alert-light border mt-3">
        <small class="text-muted">
          <i class="bi bi-shield-check me-1"></i>
          <strong>Catatan Keamanan:</strong> Kami tidak pernah meminta informasi kartu kredit, PIN, atau OTP melalui telepon atau email. Selalu lakukan pembayaran melalui halaman resmi.
        </small>
      </div>

    </div>
  </div>
</div>
@endsection