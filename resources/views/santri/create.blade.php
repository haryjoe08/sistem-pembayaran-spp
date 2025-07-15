@extends('layouts.master')

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
    <h4>Form Tambah Data Santri</h4>
  </div>
  <!--end::Header-->

  <!--begin::Form-->
  <form action="{{ route('santri.store') }}" method="POST">
    @csrf
    <!--begin::Body-->
    <div class="card-body">
      <div class="mb-3">
        <label for="nama" class="form-label">Nama Santri</label>
        <input type="text" class="form-control" id="nama" name="nama" required>
      </div>
      <div class="mb-3">
        <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
        <div class="input-group">
          <input type="date" name="tgl_lahir" class="form-control" id="tgl_lahir" required>
          <span class="input-group-text" style="cursor: pointer;" onclick="document.getElementById('tgl_lahir').showPicker()">
            <i class="bi bi-calendar-date"></i>
          </span>
        </div>
      </div>

      <div class="mb-3">
        <label for="kelas" class="form-label">Kelas</label>
        <select class="form-select" id="kelas" name="kelas" required>
          <option value="">-- Pilih Iqro --</option>
          <option value="Iqro 1">Iqro 1</option>
          <option value="Iqro 2">Iqro 2</option>
          <option value="Iqro 3">Iqro 3</option>
          <option value="Iqro 4">Iqro 4</option>
          <option value="Iqro 5">Iqro 5</option>
          <option value="Iqro 6">Iqro 6</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="alamat" class="form-label">Alamat</label>
        <textarea class="form-control" id="alamat" name="alamat" rows="2" required></textarea>
      </div>

      <div class="mb-3">
        <label for="nama_orangtua" class="form-label">Nama Orangtua</label>
        <input type="text" class="form-control" id="nama_orangtua" name="nama_orangtua" required>
      </div>

    </div>
    <!--end::Body-->
    <!--begin::Footer-->
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Tambah Data</button>
      <a href="/data-santri" class="btn btn-light float-end">Cancel</a>
    </div>
    <!--end::Footer-->
  </form>
  <!--end::Form-->
</div>
<!--end::Horizontal Form-->
@if(session('success'))
@push('scripts')
<script>
  toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "3000"
  };
  toastr.success("{{ session('success') }}");
</script>
@endpush
@endif

@endsection