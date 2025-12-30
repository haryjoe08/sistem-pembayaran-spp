@extends('layouts.adminMaster')

@section('content')

<div class="container-fluid p-4">
   <!-- SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- ERROR MESSAGE -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Atau tampilkan semua errors dari validation -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-file-earmark-text me-2 text-primary"></i>
            Daftar Tagihan
          </h4>
          <p class="text-muted mb-0">Kelola tagihan pembayaran siswa</p>
        </div>
        <a href="{{ route('tagihan.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-circle me-1"></i> Tambah Tagihan
        </a>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card border-0 shadow-sm bg-primary text-white">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="mb-1 small">Total Tagihan</p>
              <h4 class="mb-0">{{ \App\Models\Tagihan::count() }}</h4>
            </div>
            <i class="bi bi-files fs-1 opacity-25"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm bg-danger text-white">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="mb-1 small">Belum Lunas</p>
              <h4 class="mb-0">{{ \App\Models\Tagihan::where('status', 'belum lunas')->count() }}</h4>
            </div>
            <i class="bi bi-exclamation-circle fs-1 opacity-25"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm bg-success text-white">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="mb-1 small">Lunas</p>
              <h4 class="mb-0">{{ \App\Models\Tagihan::where('status', 'lunas')->count() }}</h4>
            </div>
            <i class="bi bi-check-circle fs-1 opacity-25"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm bg-warning text-white">
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="mb-1 small">Total Tunggakan</p>
              <h4 class="mb-0">Rp {{ number_format(\App\Models\Tagihan::where('status', 'belum lunas')->sum('total_tagihan') - \App\Models\Tagihan::where('status', 'belum lunas')->sum('sudah_dibayar'), 0, ',', '.') }}</h4>
            </div>
            <i class="bi bi-cash-stack fs-1 opacity-25"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
      <form action="{{ route('tagihan.index') }}" method="GET">
        <div class="row g-3 align-items-end">
          
          <!-- Filter Status -->
          <div class="col-md-2">
            <label class="form-label small text-muted fw-bold">Status</label>
            <select class="form-select" name="status">
              <option value="belum lunas" {{ request('status', 'belum lunas') === 'belum lunas' ? 'selected' : '' }}>
                Belum Lunas
              </option>
              <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>
                Lunas
              </option>
              <option value="semua" {{ request('status') === 'semua' ? 'selected' : '' }}>
                Semua Status
              </option>
            </select>
          </div>

          <!-- Filter Tahun Ajaran -->
          <div class="col-md-3">
            <label class="form-label small text-muted fw-bold">Tahun Ajaran</label>
            <select class="form-select" name="tahun_ajaran_id">
              <option value="">Semua Tahun Ajaran</option>
              @foreach($tahunAjaran as $ta)
                <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                  {{ $ta->tahun}}
                  @if($ta->status === 'aktif') (Aktif) @endif
                </option>
              @endforeach
            </select>
          </div>

          <!-- Filter Jenis Tagihan -->
          <div class="col-md-3">
            <label class="form-label small text-muted fw-bold">Jenis Tagihan</label>
            <select class="form-select" name="jenis_tagihan_id">
              <option value="">Semua Jenis</option>
              @foreach($jenisTagihan as $jt)
                <option value="{{ $jt->id }}" {{ request('jenis_tagihan_id') == $jt->id ? 'selected' : '' }}>
                  {{ $jt->nama}}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Filter Kelas -->
          <div class="col-md-2">
            <label class="form-label small text-muted fw-bold">Kelas</label>
            <select class="form-select" name="kelas_id">
              <option value="">Semua Kelas</option>
              @foreach($kelas as $k)
                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                  {{ $k->kelas }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Search -->
          <div class="col-md-3">
            <label class="form-label small text-muted fw-bold">Cari Siswa</label>
            <input type="text" 
                   class="form-control" 
                   name="search" 
                   placeholder="NIS atau Nama..."
                   value="{{ request('search') }}">
          </div>

          <!-- Buttons -->
          <div class="col-md-2">
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary flex-fill">
                <i class="bi bi-search"></i>
              </button>
              <a href="{{ route('tagihan.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-clockwise"></i>
              </a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Active Filter Badge -->
  @if(request()->hasAny(['status', 'tahun_ajaran_id', 'kelas_id', 'search']))
  <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
    <div>
      <i class="bi bi-funnel me-2"></i>
      <strong>Filter Aktif:</strong>
      @if(request('status') && request('status') !== 'belum lunas')
        <span class="badge bg-primary ms-1">Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}</span>
      @endif
      @if(request('tahun_ajaran_id'))
        <span class="badge bg-primary ms-1">
          Tahun: {{ \App\Models\TahunAjaran::find(request('tahun_ajaran_id'))->tahun ?? '-' }}
        </span>
      @endif
      @if(request('jenis_pembayaran_id'))
        <span class="badge bg-primary ms-1">
          Jenis Tagihan: {{ \App\Models\JenisPembayaran::find(request('jenis_pembayaran_id'))->nama ?? '-' }}
        </span>
      @endif
      @if(request('kelas_id'))
        <span class="badge bg-primary ms-1">
          Kelas: {{ \App\Models\Kelas::find(request('kelas_id'))->kelas ?? '-' }}
        </span>
      @endif
      @if(request('search'))
        <span class="badge bg-primary ms-1">Search: {{ request('search') }}</span>
      @endif
    </div>
    <a href="{{ route('tagihan.index') }}" class="btn btn-sm btn-outline-danger">
      <i class="bi bi-x-circle"></i> Clear
    </a>
  </div>
  @endif

  <!-- Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      @if($data->count() > 0)
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th width="50">#</th>
              <th>Siswa</th>
              <th width="120">Kelas</th>
              <th width="150">Tahun Ajaran</th>
              <th>Jenis Tagihan</th>
              <th width="130">Total</th>
              <th width="130">Dibayar</th>
              <th width="130">Sisa</th>
              <th width="120">Jatuh Tempo</th>
              <th width="100">Status</th>
              <th width="150" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($data as $d)
            <tr>
              <td>{{ $data->firstItem() + $loop->index }}</td>
              <td>
                <strong>{{ $d->siswa->nama ?? '-' }}</strong>
                <br>
                <small class="text-muted">NIS: {{ $d->siswa_nis }}</small>
              </td>
              <td>{{ $d->siswa->kelas?->kelas ?? '-' }}</td>
              <td>
                <span class="badge bg-info">
                  {{ $d->tahunAjaran->tahun ?? '-' }}
                </span>
              </td>
              <td>
                {{ $d->jenisTagihan->nama ?? '-' }}
                @if($d->jenisTagihan)
                  <br>
                  <small class="text-muted">({{ ucfirst($d->jenisTagihan->tipe ?? 'bulanan') }})</small>
                @endif
              </td>
              <td>
                <strong>Rp {{ number_format($d->total_tagihan, 0, ',', '.') }}</strong>
              </td>
              <td>
                <span class="text-success">Rp {{ number_format($d->sudah_dibayar, 0, ',', '.') }}</span>
              </td>
              <td>
                @php
                  $sisa = $d->total_tagihan - $d->sudah_dibayar;
                @endphp
                <strong class="{{ $sisa > 0 ? 'text-danger' : 'text-success' }}">
                  Rp {{ number_format($sisa, 0, ',', '.') }}
                </strong>
              </td>
              <td>
                @php
                  $jatuhTempo = \Carbon\Carbon::parse($d->jatuh_tempo);
                  $isLewat = $jatuhTempo->isPast() && $d->status !== 'lunas';
                @endphp
                <small class="{{ $isLewat ? 'text-danger fw-bold' : '' }}">
                  {{ $jatuhTempo->translatedFormat('d M Y') }}
                  @if($isLewat)
                    <br><span class="badge bg-danger">Lewat JT</span>
                  @endif
                </small>
              </td>
              <td>
                @if($d->status === 'lunas')
                  <span class="badge bg-success">Lunas</span>
                @else
                  <span class="badge bg-danger">Belum Lunas</span>
                @endif
              </td>
              <td class="text-center">
                <div class="btn-group btn-group-sm">
                  <a href="{{ route('tagihan.edit', $d->id) }}" 
                     class="btn btn-warning" 
                     title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form action="{{ route('tagihan.destroy', $d->id) }}" 
                        method="POST" 
                        class="d-inline"
                        onsubmit="return confirm('Yakin hapus tagihan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" title="Hapus">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-3 mb-3">
        {{ $data->appends(request()->except('page'))->links() }}
      </div>
      @else
      <div class="text-center py-5">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <p class="text-muted mt-3">Tidak ada tagihan ditemukan</p>
        @if(request()->hasAny(['status', 'tahun_ajaran_id', 'kelas_id', 'search']))
          <a href="{{ route('tagihan.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-clockwise me-1"></i> Reset Filter
          </a>
        @else
          <a href="{{ route('tagihan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Buat Tagihan Pertama
          </a>
        @endif
      </div>
      @endif
    </div>
  </div>

</div>
@endsection