<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pembayaran SPP</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-primary">Sistem Pembayaran SPP</h4>
            <p class="text-muted">Madrasah Aliyah NU BATANG</p>
        </div>

        <!-- Error Handling -->
        @if ($errors->any())
            <div class="alert alert-danger py-2">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Form Login -->
        <form method="POST" action="{{ route('login.process') }}">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">© 2025 Sistem Pembayaran SPP</small>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Toggle Password Script -->
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function() {
            // Toggle tipe input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>