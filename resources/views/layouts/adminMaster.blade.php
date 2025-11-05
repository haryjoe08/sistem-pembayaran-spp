<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Sistem Pembayaran SPP | Admin</title>
  <!--begin::Accessibility Meta Tags-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
  <meta name="color-scheme" content="light dark" />
  <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
  <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
  <!--end::Accessibility Meta Tags-->
  <!--begin::Primary Meta Tags-->
  <meta name="title" content="AdminLTE v4 | Dashboard" />
  <meta name="author" content="ColorlibHQ" />
  <meta
    name="description"
    content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance." />
  <meta
    name="keywords"
    content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant" />
  <!--end::Primary Meta Tags-->
  <!--begin::Accessibility Features-->
  <!-- Skip links will be dynamically added by accessibility.js -->
  <meta name="supported-color-schemes" content="light dark" />
  <link rel="preload" href="{{ asset('css/adminlte.css') }}" as="style" />
  <!--end::Accessibility Features-->
  <!--begin::Fonts-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
    integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
    crossorigin="anonymous"
    media="print"
    onload="this.media='all'" />
  <!--end::Fonts-->
  <!--begin::Third Party Plugin(OverlayScrollbars)-->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
    crossorigin="anonymous" />
  <!--end::Third Party Plugin(OverlayScrollbars)-->
  <!--begin::Third Party Plugin(Bootstrap Icons)-->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    crossorigin="anonymous" />
  <!--end::Third Party Plugin(Bootstrap Icons)-->
  <!--begin::Required Plugin(AdminLTE)-->
  <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}" />
  <!--end::Required Plugin(AdminLTE)-->
  <!-- apexcharts -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
    integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0="
    crossorigin="anonymous" />
  <!-- jsvectormap -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
    integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4="
    crossorigin="anonymous" />
</head>
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">

  <!--begin::App Wrapper-->
  <div class="app-wrapper">
    <!--begin::Header-->
    <nav class="app-header navbar navbar-expand bg-body">
      <!--begin::Container-->
      <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
              <i class="bi bi-list"></i>
            </a>
          </li>
        </ul>
        <!--end::Start Navbar Links-->
        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto">
          <!--begin::Fullscreen Toggle-->
          <li class="nav-item">
            <a class="nav-link" href="#" data-lte-toggle="fullscreen">
              <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
              <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
            </a>
          </li>
          <!--end::Fullscreen Toggle-->
          <!--begin::User Menu Dropdown-->
          <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
              <img
                src="https://ui-avatars.com/api/?name={{ urlencode($user->admin->nama ?? $user->siswa->nama ?? 'User') }}&background=0D8ABC&color=fff"
                class="user-image rounded-circle shadow"
                alt="User Image" />
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

          <!--end::User Menu Dropdown-->
        </ul>
        <!--end::End Navbar Links-->
      </div>
      <!--end::Container-->
    </nav>
    <!--end::Header-->
    <!--begin::Sidebar-->
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
      <!--begin::Sidebar Brand-->
      <div class="sidebar-brand my-3">
        <!--begin::Brand Link-->
        <a href="./index.html" class="brand-link">
          <!--begin::Brand Image-->
          <div class="row mx-2 justify-content-center text-center">
            <h4><i class="fas fa-graduation-cap me-2"></i>SPP System</h4>
            <small>Sistem Pembayaran SPP</small>
          </div>

          <!--end::Brand Image-->
          <!--begin::Brand Text-->

          <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
      </div>
      <!--end::Sidebar Brand-->
      <!--begin::Sidebar Wrapper-->
      <div class="sidebar-wrapper">
        <nav class="mt-2">
          <!--begin::Sidebar Menu-->
          <ul
            class="nav sidebar-menu flex-column"
            data-lte-toggle="treeview"
            role="navigation"
            aria-label="Main navigation"
            data-accordion="false"
            id="navigation">

            <li class="nav-item">
              <a href="/admin/dashboard" class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-fill"></i>
                <p>Dashboard</p>
              </a>
            </li>

            <li class="nav-item has-treeview {{ Request::is('siswa*') || Request::is('tahun-ajaran') || Request::is('kelas') || Request::is('jurusan') ? 'menu-open' : '' }}">
              <a href="javascript:void(0)" class="nav-link {{ Request::is('data-siswa*') || Request::is('siswa*') ? 'active' : '' }}">
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
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('tahun-ajaran') }}" class="nav-link {{ Request::is('tahun-ajaran') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Tahun Ajaran</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('kelas') }}" class="nav-link {{ Request::is('kelas') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Data Kelas</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('jurusan') }}" class="nav-link {{ Request::is('jurusan') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Jurusan</p>
                  </a>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview {{ Request::is('pembayaran*') || Request::is('jenis-pembayaran') || Request::is('tagihan*') || Request::is('terima-pembayaran*') ||Request::is('transaksi') ? 'menu-open' : '' }}">
              <a href="javascript:void(0)" class="nav-link {{ Request::is('pembayaran*') || Request::is('tagihan*') ? 'active' : '' }}">
                <i class="bi bi-wallet-fill"></i>
                <p> Pembayaran <i class="nav-arrow bi bi-chevron-right"></i> </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('jenis-pembayaran') }}" class="nav-link {{ Request::is('jenis-pembayaran') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Jenis Pembayaran</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('tagihan') }}" class="nav-link {{ Request::is('tagihan') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p> Buat Tagihan</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('terima-pembayaran') }}" class="nav-link {{ Request::is('terima-pembayaran*') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Terima Pembayaran</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ route('transaksi.index') }}" class="nav-link {{ Request::is('transaksi*') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Riwayat Transaksi</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="bi bi-graph-up"></i>
                <p>
                  Laporan
                  <i class="nav-arrow bi bi-chevron-right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ route('laporan.index') }}" class="nav-link {{ Request::is('laporan') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Ringkasan</p>
                  </a>
                </li>
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
                </li>
                <li class="nav-item">
                  <a href="{{ route('laporan.per-kelas') }}" class="nav-link {{ Request::is('laporan/per-kelas') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i>
                    <p>Laporan Per Kelas</p>
                  </a>
                </li>
              </ul>
            </li>

            <li class="nav-item">
              <a href="/profil" class="nav-link {{ Request::is('admin/pengaturan-akun') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i>
                <p>Pengaturan Akun</p>
              </a>
            </li>
          </ul>

          <!-- Logout dipindah ke luar ul -->
          <form method="POST" action="{{ route('logout') }}" class="mt-4 px-3">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
              Logout
            </button>
          </form>
          <!--end::Sidebar Menu-->
        </nav>
      </div>
      <!--end::Sidebar Wrapper-->
    </aside>
    <!--end::Sidebar-->
    <!--begin::App Main-->
    <div class="main-content">
      @yield('content')
    </div>
    <!--end::App Main-->

    {{-- jQuery dan plugin JS --}}
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    <!-- jQuery (required for toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Script tambahan dari halaman --}}
    @stack('scripts')

    <!--begin::Footer-->
    <footer class="app-footer">
      <!--begin::To the end-->
      <div class="float-end d-none d-sm-inline">Anything you want</div>
      <!--end::To the end-->
      <!--begin::Copyright-->
      <strong>
        Copyright &copy; 2014-2025&nbsp;
        <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
      </strong>
      All rights reserved.
      <!--end::Copyright-->
    </footer>
    <!--end::Footer-->
  </div>
  <!--end::App Wrapper-->
  <!--begin::Script-->
  <!--begin::Third Party Plugin(OverlayScrollbars)-->
  <script
    src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
    crossorigin="anonymous"></script>
  <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
  <script
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    crossorigin="anonymous"></script>
  <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
    crossorigin="anonymous"></script>
  <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
  <script src="{{ asset('js/adminlte.js') }}"></script>
  <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
  <script>
    const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
    const Default = {
      scrollbarTheme: 'os-theme-light',
      scrollbarAutoHide: 'leave',
      scrollbarClickScroll: true,
    };
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
      if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
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
  <!--end::OverlayScrollbars Configure-->
  <!-- OPTIONAL SCRIPTS -->
  <!-- sortablejs -->
  <script
    src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"
    crossorigin="anonymous"></script>
  <!-- sortablejs -->
  <script>
    new Sortable(document.querySelector('.connectedSortable'), {
      group: 'shared',
      handle: '.card-header',
    });

    const cardHeaders = document.querySelectorAll('.connectedSortable .card-header');
    cardHeaders.forEach((cardHeader) => {
      cardHeader.style.cursor = 'move';
    });
  </script>
  <!-- apexcharts -->
  <script
    src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
    integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8="
    crossorigin="anonymous"></script>

  <!-- Toastr CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

  <!-- jQuery (required for toastr) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Toastr JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


  <!--end::Script-->
</body>
<!--end::Body-->

</html>