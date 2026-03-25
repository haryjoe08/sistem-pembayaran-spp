@extends('layouts.adminMaster')

@section('content')
<style>
  #toast-container>.toast {
    opacity: 0.95 !important;
  }
</style>
<div class="card mb-4 mx-5 mt-4">
  <div class="card-header">
    <h2>Jenis Tagihan</h2>
    <a href="{{ route('jenis-pembayaran.create') }}" class="btn btn-primary mt-3">Tambah Jenis Tagihan</a>
  </div>

  <div class="card-body">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th style="width: 10px">No</th>
          <th>Jenis Tagihan</th>
          <th>Tipe</th>
          <th>Deskripsi</th>
          <th>Status</th>
          <th style="width: 220px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $d)
        <tr class="align-middle">
          <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>

          <td>{{ $d->nama }}</td>
          <td>{{ $d->tipe }}</td>
          <td>{{ $d->deskripsi }}</td>
          {{-- STATUS --}}
          <td>
            @if($d->status === 'aktif')
            <span class="badge bg-success">Aktif</span>
            @else
            <span class="badge bg-secondary">Nonaktif</span>
            @endif
          </td>

          {{-- AKSI --}}
          <td>
            {{-- Aktif / Nonaktif --}}
            @if($d->status === 'aktif')
            <form action="{{ route('jenis-pembayaran.nonaktifkan', $d->id) }}"
              method="POST"
              class="d-inline"
              onsubmit="return confirm('Nonaktifkan Jenis Tagihan ini?')">
              @csrf
              @method('PATCH')
              <button class="btn btn-sm btn-secondary">
                Nonaktifkan
              </button>
            </form>
            @else
            <form action="{{ route('jenis-pembayaran.aktifkan', $d->id) }}"
              method="POST"
              class="d-inline">
              @csrf
              @method('PATCH')
              <button class="btn btn-sm btn-success">
                Aktifkan
              </button>
            </form>
            @endif

            {{-- Edit --}}
            <a href="{{ route('jenis-pembayaran.edit', $d->id) }}"
              class="btn btn-sm btn-warning">
              Edit
            </a>

          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="text-center">Data tidak tersedia.</td>
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
<style>
  #toast-container>div {
    opacity: 1 !important;
    background-image: none !important;
  }

  .toast-success {
    background-color: #198754 !important;
  }

  .toast-error {
    background-color: #dc3545 !important;
  }

  .toast-info {
    background-color: #0dcaf0 !important;
  }

  .toast-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
  }


  .table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
  }

  .btn-group-sm .btn {
    padding: 4px 8px;
  }
</style>

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