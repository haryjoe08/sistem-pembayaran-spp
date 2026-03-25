<!doctype html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Sistem Pembayaran SPP | Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
  <meta name="color-scheme" content="light dark" />

  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  <!-- Google Fonts -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">

  <!-- OverlayScrollbars CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css">

  <!-- AdminLTE CSS CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">

  <!-- ApexCharts CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css">

  <!-- Toastr CSS CDN -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />


  <!-- SweetAlert2 JS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">


  <!-- Custom CSS (jika ada file lokal) -->
  @if(config('app.env') === 'local')
  <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}" />
  @endif

  @stack('styles')
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
  <div class="app-wrapper">

    <!-- Header -->
    <nav class="app-header navbar navbar-expand bg-body">
      <div class="container-fluid">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
              <i class="bi bi-list"></i>
            </a>
          </li>
        </ul>

        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="#" data-lte-toggle="fullscreen">
              <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
              <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
            </a>
          </li>

          <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
              <svg xmlns="http://www.w3.org/2000/svg"
                width="48"
                height="48"
                fill="currentColor"
                class="user-image rounded-circle shadow text-primary"
                viewBox="0 0 16 16"
                aria-label="User Image">
                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.488-.424-.655C12.82 11.225 12.42 11 12 11s-.82.225-1.176.526c-.27.167-.423.409-.424.655z" />
              </svg>

              <span class="d-none d-md-inline">
                @if(Auth::user()->role === 'admin')
                {{ Auth::user()->admin->nama }}
                @elseif(Auth::user()->role === 'siswa')
                {{ Auth::user()->siswa->nama }}
                @endif
              </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
              <li><a class="dropdown-item" href="/profil">Profil Saya</a></li>
              <li><a class="dropdown-item" href="{{ route('logout') }}"
                  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  Logout
                </a></li>
            </ul>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Sidebar -->
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
      <div class="sidebar-brand my-3">
        <a href="/admin/dashboard" class="brand-link">
          <div class="row mx-2 justify-content-center text-center">
            <h4><i class="fas fa-graduation-cap me-2"></i>SPP System</h4>
            <small>Sistem Pembayaran SPP</small>
          </div>
        </a>
      </div>

      <div class="sidebar-wrapper">
        <nav class="mt-2">
          <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation">

            <li class="nav-item">
              <a href="/admin/dashboard" class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-fill"></i>
                <p>Dashboard</p>
              </a>
            </li>

            <li class="nav-item has-treeview {{ Request::is('siswa*') || Request::is('tahun-ajaran') || Request::is('kelas') || Request::is('jurusan') ? 'menu-open' : '' }}">
              <a href="javascript:void(0)" class="nav-link">
                <i class="bi bi-database-fill"></i>
                <p> Master Data <i class="nav-arrow bi bi-chevron-right"></i> </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('siswa') }}" class="nav-link {{ Request::is('siswa') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Data Siswa</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('siswa.naik-kelas') }}" class="nav-link {{ Request::is('siswa/naik-kelas') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Kenaikan Kelas</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('tahun-ajaran') }}" class="nav-link {{ Request::is('tahun-ajaran') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Tahun Ajaran</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('kelas') }}" class="nav-link {{ Request::is('kelas') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Data Kelas</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('jurusan') }}" class="nav-link {{ Request::is('jurusan') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Jurusan</p>
                  </a>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview {{ Request::is('pembayaran*') || Request::is('jenis-pembayaran') || Request::is('tarif-tagihan') ||  Request::is('tagihan*') || Request::is('catat-pembayaran*') ||Request::is('transaksi') ? 'menu-open' : '' }}">
              <a href="javascript:void(0)" class="nav-link">
                <i class="bi bi-wallet-fill"></i>
                <p> Pembayaran <i class="nav-arrow bi bi-chevron-right"></i> </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('jenis-pembayaran') }}" class="nav-link {{ Request::is('jenis-pembayaran') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Jenis Tagihan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('tarif-tagihan') }}" class="nav-link {{ Request::is('tarif-tagihan') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Tarif Tagihan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('tagihan') }}" class="nav-link {{ Request::is('tagihan') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Buat Tagihan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a a href="{{ route('pembayaran.index') }}" class="nav-link {{ Request::is('catat-pembayaran*') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Catat Pembayaran</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('transaksi.index') }}" class="nav-link {{ Request::is('transaksi*') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Riwayat Transaksi</p>
                  </a>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview {{ Request::is('laporan*')  ? 'menu-open' : '' }}" >
              <a href="#" class="nav-link">
                <i class="bi bi-graph-up"></i>
                <p> Laporan <i class="nav-arrow bi bi-chevron-right"></i> </p>
              </a>
              <ul class="nav nav-treeview">
                <!-- 
                <li class="nav-item">
                  <a href="{{ route('laporan.index') }}" class="nav-link {{ Request::is('laporan') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Ringkasan</p>
                  </a>
                </li>
                  </li>
                <li class="nav-item">
                  <a href="{{ route('laporan.per-kelas') }}" class="nav-link {{ Request::is('laporan/per-kelas') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Laporan Per Kelas</p>
                  </a>
                </li>
                -->
                <li class="nav-item">
                  <a href="{{ route('laporan.pembayaran') }}" class="nav-link {{ Request::is('laporan/pembayaran') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Laporan Pembayaran</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('laporan.tunggakan') }}" class="nav-link {{ Request::is('laporan/tunggakan') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Laporan Tunggakan</p>
                  </a>
              </ul>
            </li>

            <li class="nav-item">
              <a href="/profil" class="nav-link {{ Request::is('profil') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i>
                <p>Pengaturan Akun</p>
              </a>
            </li>
          </ul>

          <form method="POST" action="{{ route('logout') }}" class="mt-4 px-3">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
              <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button>
          </form>
        </nav>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
      @yield('content')
    </div>

    <!-- Footer -->
    <footer class="app-footer">
      <div class="float-end d-none d-sm-inline">v1.0.0</div>
      <strong>Copyright &copy; 2025 <a href="#" class="text-decoration-none">MA NU Batang</a>.</strong>
      All rights reserved.
    </footer>
  </div>

  <!-- Scripts CDN -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>

  <!-- AdminLTE JS CDN -->
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js"></script>

  <!-- Toastr JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- ApexCharts -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"></script>

  <!-- Sortable -->
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

  <!-- OverlayScrollbars Configure -->
  <script>
    const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
    const Default = {
      scrollbarTheme: 'os-theme-light',
      scrollbarAutoHide: 'leave',
      scrollbarClickScroll: true,
    };
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
      if (sidebarWrapper && typeof OverlayScrollbarsGlobal !== 'undefined') {
        OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
          scrollbars: {
            theme: Default.scrollbarTheme,
            autoHide: Default.scrollbarAutoHide,
            clickScroll: Default.scrollbarClickScroll,
          },
        });
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @stack('scripts')
</body>

</html>