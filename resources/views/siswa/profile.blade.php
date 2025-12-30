@extends('layouts.siswaMaster')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Profil Siswa -->
            <div class="card border-0 shadow-sm rounded-3 mb-5">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                        <!-- Avatar -->
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 90px; height: 90px;">
                            <img src="{{ asset('assets/img/avatar.png') }}" alt="Foto Profil" class="img-fluid rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                        </div>

                        <!-- Info Siswa -->
                        <div class="text-center text-md-start">
                            <h4 class="mb-2 fw-bold text-dark">{{ $user->siswa?->nama ?? 'Siswa' }}</h4>
                            <p class="text-muted mb-1">
                                NIS : {{ $user->username }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Ubah Password -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-light py-3 px-4 border-0">
                    <h5 class="mb-0 text-dark fw-semibold">
                        <i class="fas fa-key me-2 text-purple"></i>Ubah Kata Sandi
                    </h5>
                </div>
                <div class="card-body p-4">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    {{ $error }}<br>
                                @endforeach
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('profil.password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Kata Sandi Baru</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8">
                            <div class="form-text text-muted small">Minimal 8 karakter.</div>
                        </div>
                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success rounded-pill py-2">
                                <i class="fas fa-sync-alt me-2"></i>Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection