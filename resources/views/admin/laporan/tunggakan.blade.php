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
                    <a href="{{ route('laporan.export.tunggakan', request()->all()) }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                    </a>
                    <button class="btn btn-danger" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>
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
                    <h3 class="fw-bold mb-0">{{ number_format($totalTagihanBelumLunas) }}</h3>
                    <p class="mb-0 small opacity-75">Tagihan Belum Lunas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm bg-dark text-white">
                <div class="card-body text-center">
                    <i class="bi bi-currency-dollar display-6 mb-2"></i>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
                    <p class="mb-0 small opacity-75">Total Tunggakan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0 fw-semibold">
                <i class="bi bi-funnel me-2"></i>
                Filter Laporan
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.tunggakan') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Kelas</label>
                        <select class="form-select" name="kelas_id">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis Pembayaran</label>
                        <select class="form-select" name="jenis_pembayaran_id">
                            <option value="">Semua Jenis</option>
                            @foreach($jenisPembayaranList as $jp)
                            <option value="{{ $jp->id }}" {{ request('jenis_pembayaran_id') == $jp->id ? 'selected' : '' }}>{{ $jp->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
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
                Daftar Tunggakan Per Siswa
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="py-3">NIS</th>
                            <th class="py-3">Nama Siswa</th>
                            <th class="py-3">Kelas</th>
                            <th class="py-3 text-center">Jml Tagihan</th>
                            <th class="py-3 text-end">Total Tunggakan</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tunggakanPerSiswa as $index => $data)
                        <tr>
                            <td class="px-4 py-3">{{ $loop->iteration }}</td>
                            <td class="py-3">
                                <span class="badge bg-secondary">{{ $data['siswa']->nis }}</span>
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($data['siswa']->nama) }}&size=32"
                                        class="rounded-circle me-2"
                                        width="32" height="32">
                                    <span class="fw-semibold">{{ $data['siswa']->nama }}</span>
                                </div>
                            </td>
                            <td class="py-3">{{ $data['siswa']->kelas->kelas ?? '-' }}</td>
                            <td class="py-3 text-center">
                                <span class="badge bg-warning">{{ $data['jumlah_tagihan'] }}</span>
                            </td>
                            <td class="py-3 text-end fw-bold text-danger">
                                Rp {{ number_format($data['total_tunggakan'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-center">
                                <a href="{{ route('pembayaran.cari', ['keyword' => $data['siswa']->nis]) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    target="_blank">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-check-circle display-6 text-success"></i>
                                <p class="text-success mb-0 mt-2">Tidak ada tunggakan! Semua siswa sudah lunas.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($tunggakanPerSiswa->count() > 0)
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="5" class="px-4 py-3 text-end">TOTAL TUNGGAKAN:</th>
                            <th class="py-3 text-end text-danger">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Alert jika ada tunggakan besar -->
    @if($totalTunggakan > 10000000)
    <div class="alert alert-danger mt-4">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Perhatian!</strong> Total tunggakan melebihi Rp 10.000.000. Segera lakukan tindakan penagihan.
    </div>
    @endif

</div>

<style>
    @media print {

        .btn,
        .card-header,
        nav,
        .alert {
            display: none !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>
@endsection