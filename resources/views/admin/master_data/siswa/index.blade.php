@extends('layouts.adminMaster')

@section('content')
<style>
  #toast-container>.toast {
    opacity: 0.95 !important;
  }
</style>
<div class="card mb-4 mx-5 mt-4">
  <div class="card-header">
    <h2>Data Siswa</h2>
    <a href="{{ route('siswa.create') }}" class="btn btn-primary mt-3">Tambah Siswa</a>
  </div>

  <div class="card-body">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th style="width: 10px">No</th>
          <th>NIS</th>
          <th>Nama</th>
          <th>Tanggal Lahir</th>
          <th>Jenis Kelamin</th>
          <th>Kelas</th>
          <th>Jurusan</th>
          <th>Tahun Ajaran</th>
          <th>Alamat</th>
          <th>Wali</th>
          <th>Kontak</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($siswas as $siswa)
        <tr class="align-middle">
          <td>{{ ($siswas->currentPage() - 1) * $siswas->perPage() + $loop->iteration }}.</td>
          <td>{{ $siswa->nis }}</td>
          <td>{{ $siswa->nama }}</td>
          <td>{{ $siswa->tgl_lahir }}</td>
          <td>{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
          <td>{{ $siswa->kelas?->kelas ?? '-' }}</td>
          <td>{{ $siswa->jurusan?->nama ?? '-' }}</td>
          <td>{{ $siswa->tahunAjaran?->tahun ?? '-' }}</td>
          <td>{{ $siswa->alamat }}</td>
          <td>{{ $siswa->wali }}</td>
          <td>{{ $siswa->kontak }}</td>
          <td>
            <a href="{{ route('siswa.edit', $siswa) }}" class="btn btn-sm btn-warning">Edit</a>
            <form action="{{ route('siswa.destroy', $siswa->nis) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Yakin ingin menghapus data ini?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
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

    {{ $siswas->links() }}
  </div>

  <div class="card-footer clearfix">
    <div class="pagination-sm m-0 float-end">
      {{ $siswas->links('pagination::bootstrap-5') }}
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
