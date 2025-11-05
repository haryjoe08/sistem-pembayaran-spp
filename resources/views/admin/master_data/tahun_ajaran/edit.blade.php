@extends('layouts.adminMaster')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Edit Tahun Ajaran</h3>

    {{-- Notifikasi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tahun-ajaran.update', $tahunAjaran->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="tahun" class="form-label">Tahun Ajaran</label>
                    <input type="text" name="tahun" id="tahun" 
                           class="form-control" 
                           value="{{ old('tahun', $tahunAjaran->tahun) }}" 
                           required>
                </div>

                <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                <a href="{{ route('tahun-ajaran.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
