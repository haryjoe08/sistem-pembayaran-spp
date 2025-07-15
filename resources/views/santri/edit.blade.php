@extends('layouts.master')

@section('content')
<div class="card card-warning card-outline mt-5 mx-5">
    <div class="card-header">
        <h4>Edit Data Santri</h4>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('santri.update', $santri->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card-body">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Santri</label>
                <input type="text" class="form-control" id="nama" name="nama" value="{{ $santri->nama }}" required>
            </div>

            <div class="mb-3">
                <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                <div class="input-group">
                    <input
                        type="date"
                        name="tgl_lahir"
                        class="form-control"
                        id="tgl_lahir"
                        value="{{ \Carbon\Carbon::parse($santri->tgl_lahir)->format('Y-m-d') }}"
                        required>

                    <span class="input-group-text" style="cursor: pointer;" onclick="document.getElementById('tgl_lahir').showPicker()">
                        <i class="bi bi-calendar-date"></i>
                    </span>
                </div>
            </div>

            <div class="mb-3">
                <label for="kelas" class="form-label">Kelas</label>
                <select class="form-select" name="kelas" required>
                    <option value="" disabled>Pilih Kelas</option>
                    @for ($i = 1; $i <= 6; $i++)
                        <option value="Iqro {{ $i }}" {{ $santri->kelas == 'Iqro '.$i ? 'selected' : '' }}>
                        Iqro {{ $i }}
                        </option>
                        @endfor
                </select>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="2">{{ $santri->alamat }}</textarea>
            </div>

            <div class="mb-3">
                <label for="nama_orangtua" class="form-label">Nama Orangtua</label>
                <input type="text" class="form-control" id="nama_orangtua" name="nama_orangtua" value="{{ $santri->nama_orangtua }}">
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-warning">Update</button>
            <a href="{{ route('santri.index') }}" class="btn btn-light float-end">Kembali</a>
        </div>
    </form>
</div>
@endsection