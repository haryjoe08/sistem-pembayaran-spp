@extends('layouts.adminMaster')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Manajemen Kelas</h3>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form Tambah Kelas --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('kelas.store') }}" method="POST">
                @csrf
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" name="kelas" class="form-control" placeholder="Nama Kelas" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Kelas --}}
    <div class="card">
        <div class="card-body">
<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>No</th>
            <th>Kelas</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kelas as $index => $k)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $k->kelas }}</td>
                <td>
                    <span class="badge {{ $k->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($k->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('kelas.edit', $k->id) }}" class="btn btn-warning btn-sm">Edit</a>

                    @if($k->status == 'aktif')
                        <form action="{{ route('kelas.nonaktif', $k->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-secondary btn-sm"
                                onclick="return confirm('Nonaktifkan kelas ini?')">
                                Nonaktif
                            </button>
                        </form>
                    @else
                        <form action="{{ route('kelas.aktifkan', $k->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-success btn-sm">
                                Aktifkan
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">Belum ada data kelas</td>
            </tr>
        @endforelse
    </tbody>
</table>

        </div>
    </div>
</div>
@endsection
