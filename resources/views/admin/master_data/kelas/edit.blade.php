@extends('layouts.adminMaster')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Edit Kelas</h3>

    {{-- Notifikasi --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form Edit Kelas --}}
    <div class="card">
        <div class="card-body">
            <form action="{{ route('kelas.update', $kela->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="kelas" class="form-label">Nama Kelas</label>
                    <input type="text" name="kelas" id="kelas" 
                           class="form-control" 
                           value="{{ old('kela', $kela->kelas) }}" 
                           required>
                </div>

                <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
