@extends('layouts.adminMaster')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Manajemen Tahun Ajaran</h3>

    {{-- Notifikasi --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form Tambah Tahun Ajaran --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('tahun-ajaran.store') }}" method="POST">
                @csrf
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" name="tahun" class="form-control" placeholder="Contoh: 2024/2025" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Tahun Ajaran --}}
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tahun Ajaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tahunAjarans as $index => $ta)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $ta->tahun }}</td>
                        <td>
                            @if($ta->status === 'aktif')
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>

                        <td>
                            @if($ta->status === 'nonaktif')
                            <form action="{{ route('tahun-ajaran.activate', $ta->id) }}"
                                method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-success">
                                    Aktifkan
                                </button>
                            </form>
                            @endif

                            <a href="{{ route('tahun-ajaran.edit', $ta->id) }}"
                                class="btn btn-sm btn-warning">
                                Edit
                            </a>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data tahun ajaran</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection