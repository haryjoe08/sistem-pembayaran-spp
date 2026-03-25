<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - SPP Management</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            overflow-x: hidden;
        }
        

        /* Sidebar Styles - Updated for Professional and Elegant Look */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            padding: 2rem 0;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            z-index: 1000;
            box-shadow: 6px 0 20px rgba(0, 0, 0, 0.15);
            overflow-y: auto;
            border-radius: 0 15px 15px 0;
        }

        .sidebar.collapsed {
            left: -280px;
        }

        /* Logo Section - More Elegant */
        .sidebar-logo {
            padding: 0 2rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 2rem;
            text-align: center;
        }

        .sidebar-logo h4 {
            color: #ecf0f1;
            font-weight: 700;
            margin: 0;
            font-size: 1.5rem;
            letter-spacing: 0.5px;
        }

        .sidebar-logo small {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
            font-style: italic;
        }

        /* Profile Section - Enhanced */
        .sidebar-profile {
            padding: 0 2rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 2rem;
            text-align: center;
        }

        .sidebar-profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid #ecf0f1;
            margin-bottom: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .sidebar-profile h6 {
            color: #ecf0f1;
            margin: 0 0 0.5rem;
            font-size: 1rem;
            font-weight: 600;
        }

        .sidebar-profile small {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
        }

        /* Menu Items - Professional Styling */
        .sidebar-menu {
            padding: 0;
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 1rem 2rem;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1rem;
            border-radius: 0 25px 25px 0;
            margin-right: 1rem;
            position: relative;
        }

        .sidebar-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ecf0f1;
            transform: translateX(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .sidebar-menu a.active {
            background: rgba(52, 152, 219, 0.3);
            color: #ecf0f1;
            border-left: 5px solid #3498db;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .sidebar-menu i {
            width: 24px;
            margin-right: 1.5rem;
            font-size: 1.2rem;
        }

        .sidebar-menu .badge {
            margin-left: auto;
            background: #e74c3c;
            color: white;
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-weight: 600;
        }

        /* Logout Button - Elegant Design */
        .sidebar-logout {
            position: absolute;
            bottom: 2rem;
            left: 0;
            right: 0;
            padding: 0 2rem;
        }

        .sidebar-logout a {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            background: rgba(231, 76, 60, 0.1);
            color: #ecf0f1;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 1rem;
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        .sidebar-logout a:hover {
            background: rgba(231, 76, 60, 0.2);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* Top Navigation - Refined */
        .top-nav {
            background: #ffffff;
            padding: 1.25rem 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid #e9ecef;
        }

        .menu-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #2c3e50;
            cursor: pointer;
            padding: 0.75rem;
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .menu-toggle:hover {
            color: #34495e;
            background: #f8f9fa;
            transform: scale(1.05);
        }

        .top-nav-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .notification-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #2c3e50;
            cursor: pointer;
            padding: 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .notification-btn:hover {
            background: #f8f9fa;
            transform: scale(1.05);
        }

        .notification-btn .badge {
            position: absolute;
            top: 0.25rem;
            right: 0.25rem;
            background: #e74c3c;
            color: white;
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            border-radius: 50%;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 3px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .user-info .user-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 1rem;
        }

        .user-info .user-role {
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                left: -280px;
                border-radius: 0;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .top-nav {
                padding: 1rem 1.5rem;
            }

            .user-info .user-details {
                display: none;
            }

            .sidebar-logout {
                position: relative;
                margin-top: 3rem;
            }
        }

        @media (max-width: 576px) {
            .top-nav {
                padding: 0.875rem 1rem;
            }

            .menu-toggle {
                font-size: 1.25rem;
            }

            .notification-btn {
                font-size: 1.125rem;
            }
        }

        /* Custom Scrollbar - Refined */
        .sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.4);
            border-radius: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.6);
        }
    </style>
</head>

<body>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- Logo -->
        <div class="sidebar-logo">
            <h4><i class="fas fa-graduation-cap me-2"></i>SPP System</h4>
            <small>Sistem Pembayaran SPP</small>
        </div>

        <!-- Profile -->
        <div class="sidebar-profile">
            <img src="{{ asset('assets/img/avatar.png') }}" alt="Profile">
            <h6>{{ Auth::user()->siswa->nama ?? 'Nama Siswa' }}</h6>
            <small>{{ Auth::user()->siswa->nis ?? '-' }}</small>
        </div>

        <!-- Menu -->
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('siswa.dashboard') }}" class="nav-link {{ Request::is('siswa/dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                @php
                $tagihanAktif = \App\Models\Tagihan::where('siswa_nis', Auth::user()->siswa->nis)
                ->where('status', 'belum lunas')
                ->count();
                @endphp
                <a href="{{ route('siswa.tagihan') }}" class="nav-link {{ Request::is('siswa-side/tagihan/*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i>
                    <span>Tagihan Saya</span>
                    <span class="badge">{{ $tagihanAktif ?? 0 }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('siswa.history') }}"  class="nav-link {{ Request::is('siswa-side/history') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span>Riwayat Transaksi</span>
                </a>
            </li>
            <li>
                <a href="{{route('siswa.profil') }}"  class="nav-link {{ Request::is('siswa-side/profil') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i>
                    <span>Profil Saya</span>
                </a>
            </li>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-4 px-3">
                @csrf
                <button type="submit" class="btn btn-danger w-100">
                    Logout
                </button>
            </form>
        </ul>


    </aside>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <nav class="top-nav">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="top-nav-right">
            

                <div class="user-info">
                    <img src="{{ asset('assets/img/avatar.png') }}" alt="User">
                    <div class="user-details">
                        <div class="user-name">{{ Auth::user()->siswa->nama ?? 'Nama Siswa' }}</div>
                        <div class="user-role">Siswa</div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script>
        // Toggle Sidebar
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        menuToggle.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                // Mobile: Toggle sidebar with overlay
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
            } else {
                // Desktop: Toggle sidebar collapse
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        });

        // Close sidebar when clicking overlay
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });

        // Close sidebar when clicking menu item on mobile
        const menuLinks = document.querySelectorAll('.sidebar-menu a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>