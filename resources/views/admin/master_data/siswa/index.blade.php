@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">

  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-people-fill me-2 text-primary"></i>
            Data Siswa
          </h4>
          <p class="text-muted mb-0">Manajemen data siswa MA Negeri</p>
        </div>
        <a href="{{ route('siswa.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-circle me-1"></i>
          Tambah Siswa
        </a>
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
      <form action="{{ route('siswa.index') }}" method="GET">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label small text-muted">Cari Siswa</label>
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
          <div class="col-md-3">
            <label class="form-label small text-muted">Filter Kelas</label>
            <select class="form-select" name="kelas">
              <option value="">Semua Kelas</option>
              @foreach(\App\Models\Kelas::orderBy('kelas')->get() as $k)
              <option value="{{ $k->kelas }}" {{ request('kelas') == $k->kelas ? 'selected' : '' }}>
                {{ $k->kelas }}
              </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-5">
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
              <th class="py-3 text-center">Aksi</th>
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
                  <img src="https://ui-avatars.com/api/?name={{ urlencode($s->nama) }}&size=32&background=667eea&color=fff"
                    class="rounded-circle me-2"
                    width="32" height="32">
                  <div>
                    <div class="fw-semibold">{{ $s->nama }}</div>
                    <small class="text-muted">{{ $s->wali }}</small>
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
              <td class="py-3">
                <div class="btn-group btn-group-sm">
                  <a href="{{ route('siswa.edit', $s->nis) }}"
                    class="btn btn-outline-warning"
                    data-bs-toggle="tooltip"
                    title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <button type="button"
                    class="btn btn-outline-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteModal{{ $s->nis }}"
                    title="Hapus">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal{{ $s->nis }}" tabindex="-1">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header bg-danger text-white">
                        <h6 class="modal-title">
                          <i class="bi bi-exclamation-triangle me-2"></i>
                          Konfirmasi Hapus
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <p class="mb-2">Apakah Anda yakin ingin menghapus siswa:</p>
                        <div class="alert alert-light">
                          <strong>{{ $s->nama }}</strong><br>
                          <small class="text-muted">NIS: {{ $s->nis }}</small>
                        </div>
                        <p class="text-danger small mb-0">
                          <i class="bi bi-exclamation-circle me-1"></i>
                          Data yang dihapus tidak dapat dikembalikan!
                        </p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form action="{{ route('siswa.destroy', $s->nis) }}" method="POST" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Hapus
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
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
  #toast-container>.toast {
    
    opacity: 0.95 !important;
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