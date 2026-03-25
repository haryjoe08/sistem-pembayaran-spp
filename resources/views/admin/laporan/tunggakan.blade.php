@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold text-dark mb-1">
                        <i class="bi bi-exclamation-triangle me-2 text-danger"></i>
                        Laporan Tunggakan
                    </h4>
                    <p class="text-muted mb-0">Daftar siswa dengan tagihan yang belum lunas</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>

                    <a href="{{ route('laporan.export.tunggakan', request()->only(['keyword', 'kelas_id', 'jenis_tagihan_id'])) }}"
                        class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                    </a>

                    <a href="{{ route('laporan.tunggakan.print', request()->only(['keyword', 'kelas_id', 'jenis_tagihan_id'])) }}"
                        class="btn btn-primary"
                        target="_blank">
                        <i class="bi bi-printer me-1"></i> Print
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body text-center">
                    <i class="bi bi-people display-6 mb-2"></i>
                    <h3 class="fw-bold mb-0">{{ number_format($totalSiswaMenunggak) }}</h3>
                    <p class="mb-0 small opacity-75">Siswa Menunggak</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark-text display-6 mb-2"></i>
                    <h3 class="fw-bold mb-0">{{ number_format($totalTagihan) }}</h3>
                    <p class="mb-0 small opacity-75">Jumlah Tagihan</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm bg-dark text-white">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-circle display-6 mb-2"></i>
                    <h3 class="fw-bold mb-0">
                        Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
                    </h3>
                    <p class="mb-0 small opacity-75">Total Tunggakan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0 fw-semibold">
                <i class="bi bi-funnel me-2"></i> Filter Laporan
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.tunggakan') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-search me-1"></i>
                            Cari Siswa
                        </label>
                        <input type="text"
                            class="form-control"
                            name="keyword"
                            placeholder="NIS atau Nama Siswa..."
                            value="{{ request('keyword') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-select">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" @selected(request('kelas_id')==$k->id)>
                                {{ $k->kelas }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Jenis Tagihan</label>
                        <select name="jenis_tagihan_id" class="form-select">
                            <option value="">Semua Jenis</option>
                            @foreach($jenisPembayaranList as $jp)
                            <option value="{{ $jp->id }}" @selected(request('jenis_tagihan_id')==$jp->id)>
                                {{ $jp->nama }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('laporan.tunggakan') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0 fw-semibold">
                <i class="bi bi-list-ul me-2"></i>
                Daftar Tunggakan Per Siswa ({{ $totalSiswaMenunggak }} siswa)
            </h6>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th class="text-center">Jml Tagihan</th>
                        <th class="text-end">Total Tunggakan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tunggakanPerSiswa as $data)
                    <tr>
                        <td>{{ $tunggakanPerSiswa->firstItem() + $loop->index }}</td>
                        <td>{{ $data['siswa']->nis }}</td>
                        <td class="fw-semibold">{{ $data['siswa']->nama }}</td>
                        <td>{{ $data['siswa']->kelas->kelas ?? '-' }}</td>
                        <td class="text-center">{{ $data['jumlah_tagihan'] }}</td>
                        <td class="text-end fw-bold text-danger">
                            Rp {{ number_format($data['total_tunggakan'], 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('pembayaran.cari', ['keyword' => $data['siswa']->nis]) }}"
                                class="btn btn-sm btn-outline-primary"
                                target="_blank">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-success">
                            <i class="bi bi-check-circle fs-3"></i>
                            <div>Tidak ada tunggakan</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>

                @if($tunggakanPerSiswa->count())
                <tfoot class="table-light">
                    <tr>
                        <th colspan="5" class="text-end">TOTAL</th>
                        <th class="text-end text-danger">
                            Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
                        </th>
                        <th></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        @if($tunggakanPerSiswa->hasPages())
        <div class="card-footer bg-white">
            {{ $tunggakanPerSiswa->links() }}
        </div>
        @endif
    </div>

</div>
@endsection