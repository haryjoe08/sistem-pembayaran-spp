@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">

  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">Data Siswa</h4>
          <p class="text-muted mb-0">Kelola data siswa</p>
        </div>
        <div class="d-flex gap-2">
          <!-- Export -->
          <div class="btn-group">
            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
              <i class="bi bi-download me-1"></i> Export
            </button>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" href="{{ route('siswa.export') }}">
                  <i class="bi bi-file-earmark-excel me-2"></i> Export Semua
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('siswa.export', ['status' => 'aktif']) }}">
                  <i class="bi bi-check-circle me-2"></i> Export Siswa Aktif
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('siswa.export', ['status' => 'lulus']) }}">
                  <i class="bi bi-mortarboard me-2"></i> Export Siswa Lulus
                </a>
              </li>
              @if(request('kelas_id'))
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('siswa.export', array_merge(request()->all(), ['status' => request('status', 'aktif')])) }}">
                  <i class="bi bi-funnel me-2"></i> Export Filter Aktif
                </a>
              </li>
              @endif
            </ul>
          </div>

          <!-- Import -->
          <a href="{{ route('siswa.import-form') }}" class="btn btn-info">
            <i class="bi bi-upload me-1"></i> Import
          </a>

          <!-- Tambah -->
          <a href="{{ route('siswa.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-md-3 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
              <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                <i class="bi bi-people text-primary fs-5"></i>
              </div>
            </div>
            <div class="flex-grow-1">
              <p class="text-muted mb-1 small">Total Siswa</p>
              <h4 class="mb-0 fw-bold">{{ $siswas->total() }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter & Search -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
      <form action="{{ route('siswa.index') }}" method="GET" id="filterForm">
        <div class="row g-3 align-items-end">

          <!-- Search -->
          <div class="col-md-3">
            <label class="form-label small text-muted fw-bold">Cari Siswa</label>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0">
                <i class="bi bi-search text-muted"></i>
              </span>
              <input type="text"
                name="search"
                class="form-control border-start-0"
                placeholder="NIS atau Nama..."
                value="{{ request('search') }}">
            </div>
          </div>

          <!-- Filter Kelas -->
          <div class="col-md-2">
            <label class="form-label small text-muted fw-bold">Filter Kelas</label>
            <select class="form-select" name="kelas">
              <option value="">Semua Kelas</option>
              @foreach(\App\Models\Kelas::where('status', 'aktif')->orderBy('kelas')->get() as $k)
              <option value="{{ $k->id }}" {{ request('kelas') == $k->id ? 'selected' : '' }}>
                {{ $k->kelas }}
              </option>
              @endforeach
            </select>
          </div>

          <!-- Filter Jurusan -->
          <div class="col-md-2">
            <label class="form-label small text-muted fw-bold">Filter Jurusan</label>
            <select class="form-select" name="jurusan">
              <option value="">Semua Jurusan</option>
              @foreach(\App\Models\Jurusan::where('status', 'aktif')->orderBy('nama')->get() as $j)
              <option value="{{ $j->id }}" {{ request('jurusan') == $j->id ? 'selected' : '' }}>
                {{ $j->nama }}
              </option>
              @endforeach
            </select>
          </div>

          <!-- Filter Status -->
          <div class="col-md-2">
            <label class="form-label small text-muted fw-bold">Filter Status</label>
            <select class="form-select" name="status">
              <option value="aktif" {{ request('status', 'aktif') == 'aktif' ? 'selected' : '' }}>
                Aktif
              </option>
              <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>
                Tidak Aktif
              </option>
              <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>
                Semua Status
              </option>
            </select>
          </div>

          <!-- Buttons -->
          <div class="col-md-3">
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-search me-1"></i> Cari
              </button>
              <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-clockwise me-1"></i> Reset
              </a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>


  <!-- Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
      <h6 class="mb-0 fw-semibold">
        <i class="bi bi-table me-2"></i>
        Daftar Siswa ({{ $siswas->total() }} siswa)
      </h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="bg-light">
            <tr>
              <th class="px-4 py-3">No</th>
              <th class="py-3">NIS</th>
              <th class="py-3">Nama</th>
              <th class="py-3">Kelas</th>
              <th class="py-3">Jurusan</th>
              <th class="py-3">Jenis Kelamin</th>
              <th class="py-3">Kontak</th>
              <th class="py-3">Status</th>
              <th class="py-3 ">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($siswas as $s)
            <tr>
              <td class="px-4 py-3">
                {{ ($siswas->currentPage() - 1) * $siswas->perPage() + $loop->iteration }}
              </td>
              <td class="py-3">
                <span class="badge bg-secondary">{{ $s->nis }}</span>
              </td>
              <td class="py-3">
                <div class="d-flex align-items-center">
                  <div>
                    <div class="fw-semibold">{{ $s->nama }}</div>

                  </div>
                </div>
              </td>
              <td class="py-3">
                <span class="badge bg-info">{{ $s->kelas->kelas ?? '-' }}</span>
              </td>
              <td class="py-3">{{ $s->jurusan->nama ?? '-' }}</td>
              <td class="py-3">
                @if($s->jenis_kelamin == 'L')
                <span class="badge bg-primary"><i class="bi bi-gender-male"></i> Laki-laki</span>
                @else
                <span class="badge bg-danger"><i class="bi bi-gender-female"></i> Perempuan</span>
                @endif
              </td>
              <td class="py-3">
                <small class="text-muted">
                  <i class="bi bi-telephone"></i> {{ $s->kontak }}
                </small>
              </td>
              <td>
                <span class="badge {{ $s->statusBadgeClass() }}">
                  {{ $s->statusLabel() }}
                </span>
              </td>
              <td>
                <a href="{{ route('siswa.show', $s->nis) }}"
                  class="btn btn-outline-primary"
                  data-bs-toggle="tooltip"
                  title="Detail">
                  <i class="bi bi-eye"></i>
                </a>
                <div class="btn-group btn-group-sm gap-2">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                      type="button"
                      data-bs-toggle="dropdown">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li>
                        <h6 class="dropdown-header">Ubah Status</h6>
                      </li>
                      <li>
                        <hr class="dropdown-divider">
                      </li>
                      @foreach(['aktif', 'tidak_aktif'] as $status)
                      <li>
                        <form action="{{ route('siswa.update-status', $s->nis) }}"
                          method="POST"
                          class="d-inline">
                          @csrf
                          <input type="hidden" name="status" value="{{ $status }}">
                          <button type="submit"
                            class="dropdown-item"
                            onclick="return confirm('Yakin mengubah status menjadi {{ ucfirst(str_replace('_', ' ', $status)) }}?')">
                            <i class="bi bi-circle-fill me-2" style="font-size: 8px; color: {{ match($status) {
                                'aktif' => '#198754',
                                'tidak_aktif' => '#6c757d',
                            } }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                          </button>
                        </form>
                      </li>
                      @endforeach
                    </ul>
                  </div>
                  <a href="{{ route('siswa.edit', $s->nis) }}"
                    class="btn btn-outline-warning"
                    data-bs-toggle="tooltip"
                    title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center py-5">
                <i class="bi bi-inbox display-6 text-muted"></i>
                <p class="text-muted mb-0 mt-2">Tidak ada data siswa</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($siswas->hasPages())
    <div class="card-footer bg-white border-top">
      <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted small">
          Menampilkan {{ $siswas->firstItem() }} - {{ $siswas->lastItem() }} dari {{ $siswas->total() }} siswa
        </div>
        <div>
          {{ $siswas->links() }}
        </div>
      </div>
    </div>
    @endif
  </div>

</div>

<style>
  #toast-container>div {
    opacity: 1 !important;
    background-image: none !important;
  }

  .toast-success {
    background-color: #198754 !important;
  }

  .toast-error {
    background-color: #dc3545 !important;
  }

  .toast-info {
    background-color: #0dcaf0 !important;
  }

  .toast-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
  }


  .table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
  }

  .btn-group-sm .btn {
    padding: 4px 8px;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
</script>
@if(session('success'))
@push('scripts')
<script>
  toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "3000"
  };
  toastr.success("{{ session('success') }}");
</script>
@endpush
@endif
@endsection