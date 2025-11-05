@extends('layouts.adminMaster')

@section('content')
<div class="container">
    <h4 class="my-3">Daftar Tagihan Aktif</h4>
    <a href="{{ route('tagihan.create') }}" class="btn btn-primary mb-3">+ Tambah Tagihan</a>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Jenis Pembayaran</th>
                    <th>Total Tagihan</th>
                    <th>Sudah Dibayar</th>
                    <th>Sisa Tagihan</th>
                    <th>Jatuh Tempo</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $d)
                <tr>
                    <td>{{ ($data->currentPage()-1) * $data->perPage() + $loop->iteration }}</td>
                    <td>{{ $d->siswa_nis }}</td>
                    <td>{{ $d->siswa->nama ?? '-' }} </td>
                    <td>{{ $d->siswa->kelas?->kelas ?? '-' }}</td>
                    <td>{{ $d->jenisPembayaran->nama ?? '-' }}</td>
                    <td>Rp {{ number_format($d->total_tagihan,0,',','.') }}</td>
                    <td>Rp {{ number_format($d->sudah_dibayar,0,',','.') }}</td>
                    <td>
                        <span class="text-danger fw-bold">
                            Rp {{ number_format($d->total_tagihan - $d->sudah_dibayar,0,',','.') }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($d->jatuh_tempo)->translatedFormat('d F Y') }}</td>

                    <td>
                        <a href="{{ route('tagihan.edit', $d->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('tagihan.destroy', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada tagihan yang belum lunas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $data->links() }}
</div>
@endsection