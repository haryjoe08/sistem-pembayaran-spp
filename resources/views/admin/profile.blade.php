@extends('layouts.adminMaster')

@section('content')
<div class="container mt-5 mb-5">
    <!-- Profil Card -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-center">
                <!-- Avatar: Gunakan SVG Person -->
                <div class="bg-primary bg-opacity-10 d-flex align-items-center justify-content-center rounded-circle me-md-4 mb-3 mb-md-0"
                     style="width: 100px; height: 100px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.488-.424-.655C12.82 11.225 12.42 11 12 11s-.82.225-1.176.526c-.27.167-.423.409-.424.655z"/>
                    </svg>
                </div>

                <!-- Informasi Akun -->
                <div class="text-center text-md-start">
                    <h3 class="fw-bold mb-2">{{ $user->admin?->nama ?? $user->siswa?->nama ?? 'User' }}</h3>
                    <div class="d-flex flex-column align-items-center align-items-md-start">
                        <span class="badge bg-primary text-white px-3 py-2">
                            <i class="fas fa-user-tag me-2"></i> {{ ucfirst($user->username) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ubah Password Card -->
    <div class="card shadow-sm border-0 rounded-4 mt-4">
        <div class="card-header bg-white py-3 px-4">
            <h4 class="mb-0 fw-semibold text-primary">
                <i class="fas fa-lock me-2"></i>Ubah Password
            </h4>
        </div>
        <div class="card-body p-4">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('profil.password') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="current_password" class="form-label fw-medium">Password Lama</label>
                    <input type="password" id="current_password" name="current_password" class="form-control form-control-lg" required>
                </div>
                <div class="mb-4">
                    <label for="new_password" class="form-label fw-medium">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" class="form-control form-control-lg" required minlength="8">
                    <div class="form-text">Minimal 8 karakter.</div>
                </div>
                <div class="mb-4">
                    <label for="new_password_confirmation" class="form-label fw-medium">Konfirmasi Password Baru</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control form-control-lg" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg rounded-3 py-2">
                        <i class="fas fa-sync-alt me-2"></i>Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection