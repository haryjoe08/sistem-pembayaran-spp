@extends('layouts.adminMaster')

@section('content')
<style>
  #toast-container>.toast {
    opacity: 0.95 !important;
  }
</style>
<!--begin::Horizontal Form-->
<div class="card card-primary card-outline mt-5 mx-5">
  <!--begin::Header-->
  <div class="card-header">
    <h4>Form Tambah Data Siswa</h4>
  </div>
  <!--end::Header-->

  <!--begin::Form-->
  <form action="{{ route('siswa.store') }}" method="POST">
    @csrf
    <!--begin::Body-->
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
            <input type="number" class="form-control @error('nis') is-invalid @enderror"
              id="nis" name="nis" value="{{ old('nis') }}" required>
            @error('nis')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="nama" class="form-label">Nama Siswa <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nama') is-invalid @enderror"
              id="nama" name="nama" value="{{ old('nama') }}" maxlength="30" required>
            @error('nama')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
            <select class="form-select @error('jenis_kelamin') is-invalid @enderror"
              id="jenis_kelamin" name="jenis_kelamin" required>
              <option value="">-- Pilih Jenis Kelamin --</option>
              <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
              <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('jenis_kelamin')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="col-md-6">
          <div class="mb-3">
            <label for="tgl_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="date" name="tgl_lahir" class="form-control @error('tgl_lahir') is-invalid @enderror"
                id="tgl_lahir" value="{{ old('tgl_lahir') }}" required>
              <span class="input-group-text" style="cursor: pointer;"
                onclick="document.getElementById('tgl_lahir').showPicker()">
                <i class="bi bi-calendar-date"></i>
              </span>
            </div>
            @error('tgl_lahir')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <!-- KELAS -->
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="kelas_id" class="form-label">Kelas <span class="text-danger">*</span></label>
            <select class="form-select @error('kelas_id') is-invalid @enderror"
              id="kelas_id" name="kelas_id" required>
              <option value="">-- Pilih Kelas --</option>
              @foreach($kelas as $k)
                <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                  {{ $k->kelas }}
                </option>
              @endforeach
            </select>
            @error('kelas_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <!-- JURUSAN -->
        <div class="col-md-6">
          <div class="mb-3">
            <label for="jurusan_id" class="form-label">Jurusan <span class="text-danger">*</span></label>
            <select class="form-select @error('jurusan_id') is-invalid @enderror"
              id="jurusan_id" name="jurusan_id" required>
              <option value="">-- Pilih Jurusan --</option>
              @foreach($jurusan as $j)
                <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>
                  {{ $j->nama }}
                </option>
              @endforeach
            </select>
            @error('jurusan_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <!-- TAHUN AJARAN -->
      <div class="row">
        <div class="col-md-12">
          <div class="mb-3">
            <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
            <select class="form-select @error('tahun_ajaran_id') is-invalid @enderror"
              id="tahun_ajaran_id" name="tahun_ajaran_id" required>
              <option value="">-- Pilih Tahun Ajaran --</option>
              @foreach($tahunAjarans as $ta)
                <option value="{{ $ta->id }}" {{ old('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                  {{ $ta->tahun }}
                </option>
              @endforeach
            </select>
            @error('tahun_ajaran_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control @error('alamat') is-invalid @enderror"
              id="alamat" name="alamat" rows="2" maxlength="50">{{ old('alamat') }}</textarea>
            @error('alamat')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="wali" class="form-label">Nama Wali</label>
            <input type="text" class="form-control @error('wali') is-invalid @enderror"
              id="wali" name="wali" value="{{ old('wali') }}" maxlength="50">
            @error('wali')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="col-md-6">
          <div class="mb-3">
            <label for="kontak" class="form-label">Kontak</label>
            <input type="text" class="form-control @error('kontak') is-invalid @enderror"
              id="kontak" name="kontak" value="{{ old('kontak') }}" maxlength="50"
              placeholder="No. HP / Telepon">
            @error('kontak')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

    </div>
    <!--end::Body-->
    <!--begin::Footer-->
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Tambah Data</button>
      <a href="{{ route('siswa.index') }}" class="btn btn-light float-end">Cancel</a>
    </div>
    <!--end::Footer-->
  </form>
  <!--end::Form-->
</div>
@endsection
