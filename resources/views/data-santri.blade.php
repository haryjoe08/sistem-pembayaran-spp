@extends('layouts.master')

@section('content')
<style>
  #toast-container>.toast {
    opacity: 0.95 !important;
  }
</style>
<div class="card mb-4 mx-5 mt-4">
  <div class="card-header">
    <h2>Data Santri</h2>
    <a href="{{ route('santri.create') }}" class="btn btn-primary ">Tambah Santri</a>
  </div>

  <!-- /.card-body -->
  <div class="card-body">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th style="width: 10px">No</th>
          <th>Nama</th>
          <th>Tanggal Lahir</th>
          <th>Kelas</th>
          <th>Alamat</th>
          <th>Nama Orangtua</th>
          <th>Aksi</th> {{-- Tambahkan kolom aksi --}}
        </tr>
      </thead>
      <tbody>
        @forelse($santris as $santri)
        <tr class="align-middle">
          <td>{{ ($santris->currentPage() - 1) * $santris->perPage() + $loop->iteration }}.</td>
          <td>{{ $santri->nama }}</td>
          <td>{{ $santri->tgl_lahir }}</td>
          <td>{{ $santri->kelas }}</td>
          <td>{{ $santri->alamat }}</td>
          <td>{{ $santri->nama_orangtua }}</td>
          <td>
            <a href="{{ route('santri.edit', $santri->id) }}" class="btn btn-sm btn-warning">Edit</a>
            <form action="{{ route('santri.destroy', $santri->id) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Yakin ingin menghapus data ini?')">
              @csrf
              @method('DELETE')
              <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $santri->id }}">
                Hapus
              </button>

            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center">Data tidak tersedia.</td>
        </tr>
        @endforelse
      </tbody>

    </table>
    <form id="delete-form" method="POST" style="display: none;">
      @csrf
      @method('DELETE')
    </form>

  </div>

  <!-- /.card-footer -->
  <div class="card-footer clearfix">
    <div class="pagination-sm m-0 float-end">
      {{ $santris->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
      button.addEventListener('click', function () {
        const santriId = this.getAttribute('data-id');
        Swal.fire({
          title: 'Apakah Anda yakin?',
          text: "Data yang dihapus tidak dapat dikembalikan!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            const form = document.getElementById('delete-form');
            form.action = `/santri/${santriId}`;
            form.submit();
          }
        });
      });
    });
  });
</script>
@endpush

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