@extends('layouts.adminMaster')

@section('content')
<h1 class="mx-5">
  Sealamat Datang 
</h1>

<div class="mx-5">
  <!-- Info boxes -->
  <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-primary shadow-sm">
          <i class="bi bi-people-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Jumlah Siswa</span>
          <span class="info-box-number">{{ $jumlahSiswa }}</span>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-success shadow-sm">
          <i class="bi bi-check-circle-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Jumlah Tagihan Lunas</span>
          <span class="info-box-number">{{ $jumlahLunas }}</span>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-danger shadow-sm">
          <i class="bi bi-x-circle-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Jumlah Tagihan Belum Lunas</span>
          <span class="info-box-number">{{ $jumlahBelumLunas }}</span>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-warning shadow-sm">
          <i class="bi bi-cash-coin"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Tunggakan</span>
          <span class="info-box-number">
            Rp{{ number_format($totalTunggakan, 0, ',', '.') }}
          </span>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection