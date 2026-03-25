@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">

  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-cash-coin me-2 text-primary"></i>
            Tarif Pembayaran
          </h4>
          <p class="text-muted mb-0">Kelola tarif pembayaran per tahun ajaran</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('tarif-tagihan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Tarif
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Alert -->
  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <!-- Filter Tahun Ajaran -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
      <form action="{{ route('tarif-tagihan.index') }}" method="GET">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label small text-muted fw-bold">Filter Tahun Ajaran</label>
            <select class="form-select" name="tahun_ajaran_id" onchange="this.form.submit()">
              <option value="">Semua Tahun Ajaran</option>
              @foreach($tahunAjaran as $ta)
              <option value="{{ $ta->id }}" {{ $tahunAjaranId == $ta->id ? 'selected' : '' }}>
                {{ $ta->tahun }}
                @if($ta->status === 'aktif')
                <span>(Aktif)</span>
                @endif
              </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <a href="{{ route('tarif-tagihan.index') }}" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-clockwise me-1"></i> Reset
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row mb-3">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm bg-primary text-white">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="mb-1 small">Total Tarif</p>
              <h4 class="mb-0">{{ $tarif->total() }}</h4>
            </div>
            <i class="bi bi-cash-stack fs-1 opacity-25"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm bg-success text-white">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="mb-1 small">Jenis Tagihan</p>
              <h4 class="mb-0">{{ \App\Models\JenisPembayaran::where('status', 'aktif')->count() }}</h4>
            </div>
            <i class="bi bi-tag fs-1 opacity-25"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm bg-info text-white">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="mb-1 small">Tahun Ajaran Aktif</p>
              <h4 class="mb-0">{{ \App\Models\TahunAjaran::where('status', 'aktif')->first()->tahun ?? '-' }}</h4>
            </div>
            <i class="bi bi-calendar-check fs-1 opacity-25"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      @if($tarif->count() > 0)
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th width="50">#</th>
              <th>Jenis Tagihan</th>
              <th>Tahun Ajaran</th>
              <th width="180">Nominal</th>
              <th>Status</th>
              <th>Keterangan</th>
              <th width="150" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($tarif as $index => $t)
            <tr>
              <td>{{ $tarif->firstItem() + $index }}</td>
              <td>
                <strong>{{ $t->jenisTagihan->nama }}</strong>
                <br>
                <small class="text-muted">{{ ucfirst($t->jenisTagihan->tipe) }}</small>
              </td>
              <td>
                {{ $t->tahunAjaran->tahun_ajaran }}
                @if($t->tahunAjaran->status === 'aktif')
                <span class="badge bg-success ms-1">Aktif</span>
                @endif
              </td>
              <td>
                <strong class="text-primary">{{ $t->nominal_format }}</strong>
              </td>
              <td>
                @if($t->status === 'aktif')
                <span class="badge bg-success">Aktif</span>
                @else
                <span class="badge bg-secondary">Nonaktif</span>
                @endif
              </td>

              <td>
                <small class="text-muted">{{ $t->keterangan ?? '-' }}</small>
              </td>
              <td class="text-center">
                <div class="btn-group btn-group-sm gap-2">

                  {{-- Toggle Status --}}
                  @if($t->status === 'aktif')
                  <form action="{{ route('tarif-tagihan.nonaktif', $t->id) }}"
                    method="POST"
                    class="d-inline"
                    title="Nonaktifkan"
                    onsubmit="return confirm('Nonaktifkan tarif ini?')">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-secondary" title="Nonaktifkan">
                      <i class="bi bi-pause-circle"></i>
                    </button>
                  </form>
                  @else
                  <form action="{{ route('tarif-tagihan.aktifkan', $t->id) }}"
                    method="POST"
                    title="Aktifkan"
                    class="d-inline"
                    onsubmit="return confirm('Aktifkan tarif ini? Tarif lain akan otomatis nonaktif')">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-success" title="Aktifkan">
                      <i class="bi bi-play-circle"></i>
                    </button>
                  </form>
                  @endif

                  {{-- Edit --}}
                  <a href="{{ route('tarif-tagihan.edit', $t->id) }}"
                    class="btn btn-warning"
                    title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>

                  <!-- {{-- Hapus --}}
                  <form action="{{ route('tarif-tagihan.destroy', $t->id) }}"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm('Yakin hapus tarif ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" title="Hapus">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form> -->

                </div>
              </td>

            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-3 mb-3">
        {{ $tarif->appends(request()->except('page'))->links() }}
      </div>
      @else
      <div class="text-center py-5">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <p class="text-muted mt-3">Belum ada tarif pembayaran</p>
        <a href="{{ route('tarif-tagihan.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-circle me-1"></i> Tambah Tarif Pertama
        </a>
      </div>
      @endif
    </div>
  </div>

</div>

@endsection