@extends('layouts.siswaMaster')

@section('content')
<style>
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    .welcome-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        height: 100%;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: currentColor;
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .badge-status {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.75rem;
        white-space: nowrap;
    }
    .action-btn {
        border-radius: 10px;
        padding: 0.875rem 1.25rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.875rem;
    }
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    .table-modern {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.875rem;
    }
    .table-modern thead th {
        background: #f8f9fc;
        border: none;
        padding: 0.875rem;
        font-weight: 600;
        color: #5a5c69;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .table-modern tbody td {
        padding: 0.875rem;
        vertical-align: middle;
        border-top: 1px solid #e3e6f0;
    }
    .table-modern tbody tr {
        transition: all 0.2s ease;
    }
    .table-modern tbody tr:hover {
        background: #f8f9fc;
    }
    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 4px solid #e3e6f0;
        object-fit: cover;
        margin-bottom: 1rem;
    }
    .info-box {
        background: #f8f9fc;
        padding: 1rem;
        border-radius: 12px;
        text-align: center;
        margin-top: 1rem;
    }
    .alert-success-custom {
        background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        border: none;
        border-radius: 12px;
        padding: 1rem;
        color: #155724;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .section-title::before {
        content: '';
        width: 4px;
        height: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }
    
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .welcome-card {
            padding: 1.25rem;
        }
        .welcome-card h3 {
            font-size: 1.25rem;
        }
        .welcome-card .d-flex {
            flex-direction: column !important;
            gap: 0.75rem !important;
        }
        .stat-card {
            padding: 1rem;
        }
        .stat-card h3 {
            font-size: 1.25rem;
        }
        .stat-card .small {
            font-size: 0.7rem;
        }
        .stat-icon {
            width: 45px;
            height: 45px;
            font-size: 18px;
        }
        .action-btn {
            padding: 0.75rem 1rem;
            font-size: 0.813rem;
        }
        .table-modern {
            font-size: 0.75rem;
        }
        .table-modern thead th {
            padding: 0.625rem 0.5rem;
            font-size: 0.625rem;
        }
        .table-modern tbody td {
            padding: 0.625rem 0.5rem;
        }
        .badge-status {
            padding: 0.3rem 0.6rem;
            font-size: 0.688rem;
        }
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }
        .profile-avatar {
            width: 70px;
            height: 70px;
        }
        .info-box {
            padding: 0.875rem;
        }
        .section-title {
            font-size: 1rem;
        }
        .alert-success-custom {
            padding: 0.875rem;
            font-size: 0.813rem;
        }
        .alert-success-custom .fa-2x {
            font-size: 1.5rem;
        }
        .card-body {
            padding: 1rem !important;
        }
        .container-fluid {
            padding: 0.75rem !important;
        }
        /* Hide decorative icon on mobile */
        .welcome-card .col-md-4 {
            display: none !important;
        }
        /* Stack table content better on mobile */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }
    
    @media (max-width: 576px) {
        .welcome-card h3 {
            font-size: 1.1rem;
        }
        .stat-card h3 {
            font-size: 1.1rem;
        }
        .stat-card p {
            font-size: 0.688rem;
        }
        .table-modern thead th,
        .table-modern tbody td {
            padding: 0.5rem 0.375rem;
        }
        /* Make buttons full width on very small screens */
        .btn-sm {
            font-size: 0.688rem;
            padding: 0.313rem 0.5rem;
        }
    }
</style>

<div class="container-fluid p-3 p-md-4">
    <!-- Welcome Section -->
    <div class="welcome-card">
        <div class="row align-items-center">
            <div class="col-12 col-md-8">
                <h3 class="mb-2"><i class="fas fa-user-graduate me-2"></i>Selamat Datang, {{ Auth::user()->siswa->nama }}</h3>
                <div class="d-flex flex-wrap gap-3 gap-md-4 mt-3">
                    <div>
                        <small class="opacity-75 d-block">NIS</small>
                        <p class="mb-0 fw-bold">{{ Auth::user()->siswa->nis ?? '-' }}</p>
                    </div>
                    <div>
                        <small class="opacity-75 d-block">Kelas</small>
                        <p class="mb-0 fw-bold">{{ Auth::user()->siswa->kelas->kelas ?? '-' }}</p>
                    </div>
                    <div>
                        <small class="opacity-75 d-block">Jurusan</small>
                        <p class="mb-0 fw-bold">{{ Auth::user()->siswa->jurusan->nama ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end d-none d-md-block">
                <i class="fas fa-graduation-cap" style="font-size: 80px; opacity: 0.2;"></i>
            </div>
        </div>
    </div>

    @if($pembayaranTerbaru)
    <div class="alert-success-custom d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <i class="fas fa-check-circle fa-2x"></i>
            <div>
                <strong>Pembayaran Berhasil!</strong>
                <p class="mb-0 small">{{ $pembayaranTerbaru->jenis_pembayaran }} telah dikonfirmasi pada {{ $pembayaranTerbaru->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-3 mb-md-4">
        <div class="col-6 col-lg-3 mb-3 mb-lg-0">
            <div class="card stat-card" style="color: #e74c3c;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1 pe-2">
                        <p class="text-muted text-uppercase small mb-1 mb-md-2" style="font-weight: 600; letter-spacing: 0.5px; font-size: 0.688rem;">Total Tunggakan</p>
                        <h3 class="fw-bold mb-0" style="color: #2c3e50; font-size: 1.1rem;">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
                    </div>
                    <div class="stat-icon" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3 mb-3 mb-lg-0">
            <div class="card stat-card" style="color: #27ae60;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1 pe-2">
                        <p class="text-muted text-uppercase small mb-1 mb-md-2" style="font-weight: 600; letter-spacing: 0.5px; font-size: 0.688rem;">Sudah Dibayar</p>
                        <h3 class="fw-bold mb-0" style="color: #2c3e50; font-size: 1.1rem;">Rp {{ number_format($sudahDibayar, 0, ',', '.') }}</h3>
                    </div>
                    <div class="stat-icon" style="background: rgba(39, 174, 96, 0.1); color: #27ae60;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3 mb-3 mb-lg-0">
            <div class="card stat-card" style="color: #3498db;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1 pe-2">
                        <p class="text-muted text-uppercase small mb-1 mb-md-2" style="font-weight: 600; letter-spacing: 0.5px; font-size: 0.688rem;">Tagihan Aktif</p>
                        <h3 class="fw-bold mb-0" style="color: #2c3e50; font-size: 1.1rem;">{{ $tagihanAktif }}</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Tagihan</small>
                    </div>
                    <div class="stat-icon" style="background: rgba(52, 152, 219, 0.1); color: #3498db;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3 mb-3 mb-lg-0">
            <div class="card stat-card" style="color: #f39c12;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1 pe-2">
                        <p class="text-muted text-uppercase small mb-1 mb-md-2" style="font-weight: 600; letter-spacing: 0.5px; font-size: 0.688rem;">Belum Lunas</p>
                        <h3 class="fw-bold mb-0" style="color: #2c3e50; font-size: 1.1rem;">{{ $belumLunas }}</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Tagihan</small>
                    </div>
                    <div class="stat-icon" style="background: rgba(243, 156, 18, 0.1); color: #f39c12;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <h6 class="section-title">Aksi Cepat</h6>
            <div class="row g-2 g-md-3">
                <div class="col-6 col-md-3">
                    <a href="=" class="action-btn btn btn-primary w-100 d-flex flex-column flex-md-row align-items-center justify-content-center gap-2">
                        <i class="fas fa-list"></i>
                        <span class="d-none d-sm-inline">Lihat Semua Tagihan</span>
                        <span class="d-sm-none small">Tagihan</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="=" class="action-btn btn btn-success w-100 d-flex flex-column flex-md-row align-items-center justify-content-center gap-2">
                        <i class="fas fa-history"></i>
                        <span class="d-none d-sm-inline">History Pembayaran</span>
                        <span class="d-sm-none small">History</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="" class="action-btn btn btn-info w-100 d-flex flex-column flex-md-row align-items-center justify-content-center gap-2">
                        <i class="fas fa-download"></i>
                        <span class="d-none d-sm-inline">Download Kwitansi</span>
                        <span class="d-sm-none small">Kwitansi</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="" class="action-btn btn btn-secondary w-100 d-flex flex-column flex-md-row align-items-center justify-content-center gap-2">
                        <i class="fas fa-phone"></i>
                        <span class="d-none d-sm-inline">Hubungi Admin</span>
                        <span class="d-sm-none small">Admin</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Tagihan Terbaru -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="section-title mb-0">Tagihan Terbaru</h6>
                        <a href="" class="btn btn-outline-primary btn-sm">Lihat Semua <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Jenis Pembayaran</th>
                                    <th>Total</th>
                                    <th>Sudah Bayar</th>
                                    <th>Sisa</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tagihanTerbaru as $tagihan)
                                <tr>
                                    <td>
                                        <strong>{{ $tagihan->jenisPembayaran->nama ?? '-' }}</strong>
                                    </td>
                                    <td>Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</td>
                                    <td class="text-success">Rp {{ number_format($tagihan->sudah_dibayar, 0, ',', '.') }}</td>
                                    <td class="text-danger fw-bold">Rp {{ number_format($tagihan->total_tagihan - $tagihan->sudah_dibayar, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge-status {{ $tagihan->status == 'lunas' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($tagihan->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($tagihan->status != 'lunas')
                                        <a href="" class="btn btn-sm btn-primary">
                                            <i class="fas fa-credit-card me-1"></i>Bayar
                                        </a>
                                        @else
                                        <a href="" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-receipt me-1"></i>Kwitansi
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                                        Tidak ada tagihan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Profil Siswa -->
            <div class="card mb-4">
                <div class="card-body p-4 text-center">
                   <img src="{{ asset('assets/img/avatar.png') }}" alt="Profile">
                    <h5 class="mb-1 fw-bold">{{ Auth::user()->siswa->nama ?? Auth::user()->nama }}</h5>
                    <p class="text-muted small mb-3">{{ Auth::user()->siswa->nis ?? '-' }}</p>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="info-box">
                                <small class="text-muted d-block">Kelas</small>
                                <strong>{{ Auth::user()->siswa->kelas->kelas ?? '-' }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-box">
                                <small class="text-muted d-block">Jurusan</small>
                                <strong>{{ Auth::user()->siswa->jurusan->nama ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>
                    
                    <a href="" class="btn btn-outline-primary w-100">
                        <i class="fas fa-user-edit me-2"></i>Edit Profil
                    </a>
                </div>
            </div>

            <!-- Informasi Kontak -->
            <div class="card">
                <div class="card-body p-4">
                    <h6 class="section-title">Butuh Bantuan?</h6>
                    <div class="info-box">
                        <i class="fas fa-headset fa-3x mb-3" style="color: #667eea;"></i>
                        <p class="text-muted small mb-3">Hubungi bagian Tata Usaha untuk informasi lebih lanjut</p>
                        <div class="d-grid gap-2">
                            <a href="tel:0271123456" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-phone me-2"></i>0271-123456
                            </a>
                            <a href="mailto:admin@man-example.sch.id" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-envelope me-2"></i>admin@man-example.sch.id
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection