@extends('layouts.adminMaster')

@section('content')
<div class="container-fluid p-4">
  
  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-person-circle me-2 text-primary"></i>
            Detail Siswa
          </h4>
          <p class="text-muted mb-0">Informasi lengkap data siswa</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('siswa.edit', $siswa->nis) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i> Edit
          </a>
          <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Left Column: Profile -->
    <div class="col-lg-4 mb-4">
      
      <!-- Profile Card -->
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body text-center p-4">
          <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
               style="width: 100px; height: 100px;">
            <i class="bi bi-person-fill text-primary" style="font-size: 3rem;"></i>
          </div>
          <h5 class="fw-bold mb-1">{{ $siswa->nama }}</h5>
          <p class="text-muted mb-2">
            <i class="bi bi-hash"></i> NIS: {{ $siswa->nis }}
          </p>
          <span class="badge {{ $siswa->statusBadgeClass() }} px-3 py-2">
            {{ $siswa->statusLabel() }}
          </span>
        </div>
      </div>

      <!-- Data Pribadi -->
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white py-3">
          <h6 class="mb-0 fw-bold">
            <i class="bi bi-person-badge me-2 text-primary"></i>
            Data Pribadi
          </h6>
        </div>
        <div class="card-body p-3">
          <div class="mb-3">
            <small class="text-muted d-block mb-1">Tanggal Lahir</small>
            <p class="mb-0 fw-semibold">
            {{ $siswa->tgl_lahir ? \Carbon\Carbon::parse($siswa->tgl_lahir)->format('d F Y') : '-' }}
              @if($siswa->tgl_lahir)
           
              @endif
            </p>
          </div>

          <div class="mb-3">
            <small class="text-muted d-block mb-1">Jenis Kelamin</small>
            <p class="mb-0">
              @if($siswa->jenis_kelamin == 'L')
                <span class="badge bg-primary">
                  <i class="bi bi-gender-male"></i> Laki-laki
                </span>
              @else
                <span class="badge bg-danger">
                  <i class="bi bi-gender-female"></i> Perempuan
                </span>
              @endif
            </p>
          </div>

          <div class="mb-3">
            <small class="text-muted d-block mb-1">Alamat</small>
            <p class="mb-0">{{ $siswa->alamat ?? '-' }}</p>
          </div>

          <div class="mb-3">
            <small class="text-muted d-block mb-1">Wali</small>
            <p class="mb-0 fw-semibold">{{ $siswa->wali ?? '-' }}</p>
          </div>

          <div class="mb-0">
            <small class="text-muted d-block mb-1">Kontak</small>
            <p class="mb-0">
              <a href="https://wa.me/{{ $siswa->kontak }}" target="_blank" class="text-decoration-none">
                <i class="bi bi-whatsapp text-success"></i> {{ $siswa->kontak ?? '-' }}
              </a>
            </p>
          </div>
        </div>
      </div>

      <!-- Data Akademik -->
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
          <h6 class="mb-0 fw-bold">
            <i class="bi bi-book me-2 text-primary"></i>
            Data Akademik
          </h6>
        </div>
        <div class="card-body p-3">
          <div class="mb-3">
            <small class="text-muted d-block mb-1">Kelas</small>
            <p class="mb-0">
              <span class="badge bg-info">{{ $siswa->kelas->kelas ?? '-' }}</span>
            </p>
          </div>

          <div class="mb-3">
            <small class="text-muted d-block mb-1">Jurusan</small>
            <p class="mb-0 fw-semibold">{{ $siswa->jurusan->nama ?? '-' }}</p>
          </div>

          <div class="mb-0">
            <small class="text-muted d-block mb-1">Tahun Masuk</small>
            <p class="mb-0">{{ $siswa->tahun_masuk ?? '-' }}</p>
          </div>
        </div>
      </div>

    </div>

    <!-- Right Column: Tagihan & Transaksi -->
    <div class="col-lg-8">
      
      <!-- Summary Keuangan -->
      <div class="row mb-4">
        <div class="col-md-4 mb-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                  <i class="bi bi-receipt fs-4 text-primary"></i>
                </div>
                <div>
                  <small class="text-muted d-block">Total Tagihan</small>
                  <h5 class="fw-bold mb-0">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h5>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 mb-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                  <i class="bi bi-check-circle fs-4 text-success"></i>
                </div>
                <div>
                  <small class="text-muted d-block">Sudah Dibayar</small>
                  <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</h5>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 mb-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                  <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
                </div>
                <div>
                  <small class="text-muted d-block">Total Tunggakan</small>
                  <h5 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="row mb-4">
        <div class="col-md-6 mb-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <small class="text-muted d-block">Tagihan Lunas</small>
                  <h4 class="fw-bold mb-0 text-success">{{ $tagihanLunas }}</h4>
                </div>
                <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6 mb-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <small class="text-muted d-block">Tagihan Belum Lunas</small>
                  <h4 class="fw-bold mb-0 text-warning">{{ $tagihanBelumLunas }}</h4>
                </div>
                <i class="bi bi-hourglass-split text-warning" style="font-size: 2rem;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tagihan Terbaru -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
          <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
              <i class="bi bi-receipt-cutoff me-2 text-primary"></i>
              Tagihan Terbaru
            </h6>
            <a href="{{ route('tagihan.index') }}?siswa={{ $siswa->nis }}" class="btn btn-sm btn-outline-primary">
              Lihat Semua
            </a>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th class="py-3">Jenis Tagihan</th>
                  <th class="py-3 text-end">Total</th>
                  <th class="py-3 text-end">Dibayar</th>
                  <th class="py-3 text-center">Status</th>
                  <th class="py-3">Jatuh Tempo</th>
                </tr>
              </thead>
              <tbody>
                @forelse($tagihanTerbaru as $t)
                <tr>
                  <td class="py-3">
                    <strong>{{ $t->jenisTagihan->nama }}</strong>
                    <br>
                    <small class="text-muted">{{ $t->created_at->format('d/m/Y') }}</small>
                  </td>
                  <td class="py-3 text-end">
                    Rp {{ number_format($t->total_tagihan, 0, ',', '.') }}
                  </td>
                  <td class="py-3 text-end">
                    <span class="text-success fw-semibold">
                      Rp {{ number_format($t->sudah_dibayar, 0, ',', '.') }}
                    </span>
                  </td>
                  <td class="py-3 text-center">
                    @if($t->status == 'lunas')
                      <span class="badge bg-success">Lunas</span>
                    @else
                      <span class="badge bg-warning text-dark">Belum Lunas</span>
                    @endif
                  </td>
                  <td class="py-3">
                    @if($t->jatuh_tempo)
                      <small class="{{ $t->isJatuhTempo() ? 'text-danger' : '' }}">
                        {{ $t->jatuh_tempo->format('d/m/Y') }}
                      </small>
                    @else
                      <small class="text-muted">-</small>
                    @endif
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox"></i> Belum ada tagihan
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Riwayat Pembayaran -->
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
          <h6 class="mb-0 fw-bold">
            <i class="bi bi-clock-history me-2 text-success"></i>
            Riwayat Pembayaran
          </h6>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th class="py-3">Tanggal</th>
                  <th class="py-3">Jenis Tagihan</th>
                  <th class="py-3 text-end">Jumlah</th>
                  <th class="py-3 text-center">Metode</th>
                </tr>
              </thead>
              <tbody>
                @forelse($transaksiTerbaru as $tx)
                <tr>
                  <td class="py-3">
                    <small>{{ $tx->tanggal->format('d/m/Y H:i') }}</small>
                  </td>
                  <td class="py-3">
                    <strong>{{ $tx->tagihan->jenisTagihan->nama }}</strong>
                  </td>
                  <td class="py-3 text-end">
                    <span class="text-success fw-bold">
                      Rp {{ number_format($tx->jumlah_bayar, 0, ',', '.') }}
                    </span>
                  </td>
                  <td class="py-3 text-center">
                    <span class="badge bg-light text-dark border">
                      {{ strtoupper($tx->metode) }}
                    </span>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="4" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox"></i> Belum ada pembayaran
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Info Footer -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm bg-light">
        <div class="card-body p-3">
          <div class="row text-center">
            <div class="col-md-4">
              <small class="text-muted d-block">Terdaftar Sejak</small>
              <p class="mb-0 fw-semibold">{{ $siswa->created_at->format('d F Y') }}</p>
            </div>
            <div class="col-md-4">
              <small class="text-muted d-block">Terakhir Diupdate</small>
              <p class="mb-0 fw-semibold">{{ $siswa->updated_at->format('d F Y H:i') }}</p>
            </div>
            <div class="col-md-4">
              <small class="text-muted d-block">Username Login</small>
              <p class="mb-0 fw-semibold font-monospace">{{ $siswa->nis }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<style>
.card {
  transition: transform 0.2s;
}

.card:hover {
  transform: translateY(-2px);
}
</style>
@endsection