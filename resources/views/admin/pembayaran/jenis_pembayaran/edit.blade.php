@extends('layouts.adminMaster')

@section('content')
<div class="card card-warning card-outline mt-5 mx-5">
  <div class="card-header">
    <h4>Form Edit Jenis Tagihan</h4>
  </div>

  <form action="{{ route('jenis-pembayaran.update', $jenis_pembayaran->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card-body">
      <div class="row">

        <!-- NAMA -->
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">
              Nama Jenis Tagihan <span class="text-danger">*</span>
            </label>
            <input type="text"
                   name="nama"
                   class="form-control @error('nama') is-invalid @enderror"
                   value="{{ old('nama', $jenis_pembayaran->nama) }}"
                   required>
            @error('nama')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

      </div>

      <!-- DESKRIPSI -->
      <div class="row">
        <div class="col-md-12">
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi"
                      class="form-control @error('deskripsi') is-invalid @enderror"
                      rows="3">{{ old('deskripsi', $jenis_pembayaran->deskripsi) }}</textarea>
            @error('deskripsi')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <!-- STATUS INFO (READ ONLY) -->
      <div class="row">
        <div class="col-md-6">
          <label class="form-label">Status</label><br>
          @if($jenis_pembayaran->status === 'aktif')
            <span class="badge bg-success">Aktif</span>
          @else
            <span class="badge bg-secondary">Nonaktif</span>
          @endif
          <small class="text-muted d-block mt-1">
            Status diubah dari halaman daftar
          </small>
        </div>
      </div>

    </div>

    <div class="card-footer">
      <button type="submit" class="btn btn-warning">Update</button>
      <a href="{{ route('jenis-pembayaran.index') }}" class="btn btn-light float-end">Batal</a>
    </div>
  </form>
</div>
@endsection
