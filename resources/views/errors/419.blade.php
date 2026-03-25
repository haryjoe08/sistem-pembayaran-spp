<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Tidak Aktif – 419</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Optional: Inter font for better typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 px-4">
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-md w-full text-center space-y-6">
        <!-- Icon / Visual Indicator -->
        <div class="mx-auto w-16 h-16 flex items-center justify-center bg-red-100 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
        </div>

        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-800">Sesi Halaman Telah Kedaluwarsa</h1>

        <!-- Description -->
        <p class="text-gray-600 leading-relaxed">
            Untuk alasan keamanan, halaman ini tidak dapat digunakan lagi.
            <br />
            <span class="font-medium text-gray-800">Anda masih dalam kondisi login.</span>
        </p>

        <!-- Action Button -->
        @auth
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-block w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200 shadow-sm">
                    Kembali ke Dashboard Admin
                </a>
            @elseif (auth()->user()->role === 'siswa')
                <a href="{{ route('siswa.dashboard') }}"
                   class="inline-block w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition duration-200 shadow-sm">
                    Kembali ke Dashboard Siswa
                </a>
            @endif
        @else
            <a href="{{ route('login') }}"
               class="inline-block w-full py-3 px-4 bg-gray-800 hover:bg-gray-900 text-white font-medium rounded-lg transition duration-200 shadow-sm">
                Masuk ke Akun Anda
            </a>
        @endauth

        <!-- Optional: Help Text -->
        <p class="text-xs text-gray-500 mt-4">
            Error 419 – Token CSRF tidak valid atau sesi telah berakhir.
        </p>
    </div>
</body>
</html>