@extends('layouts.adminMaster')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Manajemen Jurusan</h3>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form Tambah Jurusan --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('jurusan.store') }}" method="POST">
                @csrf
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: IPA / IPS / TKJ" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Jurusan --}}
    <div class="card">
        <div class="card-body">
<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>No</th>
            <th>Nama Jurusan</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jurusans as $index => $jurusan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $jurusan->nama }}</td>
                <td>
                    <span class="badge {{ $jurusan->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($jurusan->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('jurusan.edit', $jurusan->id) }}"
                       class="btn btn-sm btn-warning">Edit</a>

                    @if($jurusan->status == 'aktif')
                        <form action="{{ route('jurusan.nonaktif', $jurusan->id) }}"
                              method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-secondary"
                                onclick="return confirm('Nonaktifkan jurusan ini?')">
                                Nonaktif
                            </button>
                        </form>
                    @else
                        <form action="{{ route('jurusan.aktifkan', $jurusan->id) }}"
                              method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-success">
                                Aktifkan
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">Belum ada data jurusan</td>
            </tr>
        @endforelse
    </tbody>
</table>

        </div>
    </div>
</div>
@endsection
