@extends('layouts.adminMaster')

@section('content')
<style>
  #toast-container>.toast {
    opacity: 0.95 !important;
  }
</style>
<div class="card mb-4 mx-5 mt-4">
  <div class="card-header">
    <h2>Jenis Pembayaran</h2>
    <a href="{{ route('jenis-pembayaran.create') }}" class="btn btn-primary mt-3">Tambah Jenis Pembayran</a>
  </div>

  <div class="card-body">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th style="width: 10px">No</th>
          <th>Jenis Pembayaran</th>
          <th>Nominal</th>
          <th>Deskripsi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $d)
        <tr class="align-middle">
          <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}.</td>
          <td>{{ $d->nama }}</td>
          <td>Rp {{ number_format($d->nominal, 0, ',', '.') }}</td>
          <td>{{ $d->deskripsi }}</td>
          <td>
            <!-- Tombol Edit -->
            <a href="{{ route('jenis-pembayaran.edit', $d->id) }}"
              class="btn btn-sm btn-warning">
              Edit
            </a>

            <!-- Tombol Hapus -->
            <form action="{{ route('jenis-pembayaran.destroy', $d->id) }}"
              method="POST"
              class="d-inline"
              onsubmit="return confirm('Yakin ingin menghapus data ini?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger">
                Hapus
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="12" class="text-center">Data tidak tersedia.</td>
        </tr>
        @endforelse
      </tbody>

    </table>

    {{ $data->links() }}
  </div>

  <div class="card-footer clearfix">
    <div class="pagination-sm m-0 float-end">
      {{ $data->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>

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