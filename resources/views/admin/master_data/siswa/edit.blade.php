@extends('layouts.adminMaster')

@section('content')
<div class="card card-warning card-outline mt-5 mx-5">
    <div class="card-header">
        <h4>Edit Data Siswa</h4>
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

    <div class="container my-3">

        <form action="{{ route('siswa.update', $siswa->nis) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>NIS</label>
                <input type="text" class="form-control" value="{{ $siswa->nis }}" disabled>
            </div>

            <div class="mb-3">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" 
                    value="{{ old('nama', $siswa->nama) }}">
            </div>

            <div class="mb-3">
                <label>Tanggal Lahir</label>
                <input type="date" name="tgl_lahir" class="form-control" 
                    value="{{ old('tgl_lahir', $siswa->tgl_lahir) }}">
            </div>

            <div class="mb-3">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-control">
                    <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="kelas_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                <select class="form-select @error('kelas_id') is-invalid @enderror"
                    id="kelas_id" name="kelas_id" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ $siswa->kelas_id == $k->id ? 'selected' : '' }}>
                            {{ $k->kelas }}
                        </option>
                    @endforeach
                </select>
                @error('kelas_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="jurusan_id" class="form-label">Jurusan <span class="text-danger">*</span></label>
                <select class="form-select @error('jurusan_id') is-invalid @enderror"
                    id="jurusan_id" name="jurusan_id" required>
                    <option value="">-- Pilih Jurusan --</option>
                    @foreach($jurusan as $j)
                        <option value="{{ $j->id }}" {{ $siswa->jurusan_id == $j->id ? 'selected' : '' }}>
                            {{ $j->nama }}
                        </option>
                    @endforeach
                </select>
                @error('jurusan_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                <select class="form-select @error('tahun_ajaran_id') is-invalid @enderror"
                    id="tahun_ajaran_id" name="tahun_ajaran_id" required>
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    @foreach($tahunAjarans as $t)
                        <option value="{{ $t->id }}" {{ $siswa->tahun_ajaran_id == $t->id ? 'selected' : '' }}>
                            {{ $t->tahun }}
                        </option>
                    @endforeach
                </select>
                @error('tahun_ajaran_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control">{{ old('alamat', $siswa->alamat) }}</textarea>
            </div>

            <div class="mb-3">
                <label>Wali</label>
                <input type="text" name="wali" class="form-control" 
                    value="{{ old('wali', $siswa->wali) }}">
            </div>

            <div class="mb-3">
                <label>Kontak</label>
                <input type="text" name="kontak" class="form-control" 
                    value="{{ old('kontak', $siswa->kontak) }}">
            </div>

            <button type="submit" class="btn btn-warning">Update</button>
            <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
