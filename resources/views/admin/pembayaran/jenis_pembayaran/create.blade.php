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
    <h4>Form Tambah Jenis Pembayaran</h4>
  </div>
  <!--end::Header-->

  <!--begin::Form-->
  <form action="{{ route('jenis-pembayaran.store') }}" method="POST">
    @csrf
    <!--begin::Body-->
    <div class="card-body">
      <div class="row">
        <!-- NAMA -->
        <div class="col-md-6">
          <div class="mb-3">
            <label for="nama" class="form-label">Nama Jenis Pembayaran <span class="text-danger">*</span></label>
            <input type="text" 
                   class="form-control @error('nama') is-invalid @enderror"
                   id="nama" 
                   name="nama" 
                   value="{{ old('nama') }}" 
                   maxlength="50" 
                   required>
            @error('nama')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <!-- nominal -->
        <div class="col-md-6">
          <div class="mb-3">
            <label for="nominal" class="form-label">nominal (Rp)</label>
            <input type="number" 
                   class="form-control @error('nominal') is-invalid @enderror"
                   id="nominal" 
                   name="nominal" 
                   value="{{ old('nominal') }}" 
                   min="0"
                   placeholder="Contoh: 250000">
            @error('nominal')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <!-- DESKRIPSI -->
      <div class="row">
        <div class="col-md-12">
          <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                      id="deskripsi" 
                      name="deskripsi" 
                      rows="3" 
                      maxlength="255"
                      placeholder="Keterangan tambahan...">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
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
      <a href="{{ route('jenis-pembayaran.index') }}" class="btn btn-light float-end">Cancel</a>
    </div>
    <!--end::Footer-->
  </form>
  <!--end::Form-->
</div>
@endsection
