<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\JenisPembayaranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\TerimaPembayaranController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SiswaSideController;
use App\Http\Controllers\PaymentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ======================== AUTH ========================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ======================== DASHBOARD ========================
Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/siswa/dashboard', [AuthController::class, 'siswaDashboard'])->name('siswa.dashboard');
});

// ======================== ROOT REDIRECT ========================
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('siswa.dashboard');
    }
    return redirect()->route('login');
});

// ======================== PROFIL ========================
Route::middleware(['auth'])->group(function () {
    Route::get('/profil', [ProfileController::class, 'index'])->name('profil');
    Route::post('/profil/password', [ProfileController::class, 'updatePassword'])->name('profil.password');
});

// ======================== CRUD MASTER DATA ========================
Route::middleware(['auth'])->group(function () {
    // Siswa
    Route::resource('siswa', SiswaController::class);
    
    // Tahun Ajaran
    Route::resource('tahun-ajaran', TahunAjaranController::class);

    // Kelas
    Route::resource('kelas', KelasController::class);

    // Jurusan
    Route::resource('jurusan', JurusanController::class);



    // Jenis Pembayaran
    Route::resource('jenis-pembayaran', JenisPembayaranController::class);

    //Tagihan
    Route::resource('tagihan', TagihanController::class);

    // Tambahan untuk bulk operations
    Route::post('tagihan/bulk-edit', [TagihanController::class, 'bulkEdit'])->name('tagihan.bulk-edit');
    Route::post('tagihan/edit-by-class', [TagihanController::class, 'editByClass'])->name('tagihan.edit-by-class');



    //Terima Pembayaran
    Route::get('/terima-pembayaran', [TerimaPembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/terima-pembayaran/cari', [TerimaPembayaranController::class, 'cari'])->name('pembayaran.cari');
    Route::post('/terima-pembayaran/proses/{id}', [TerimaPembayaranController::class, 'proses'])->name('pembayaran.proses');
    Route::get('/terima-pembayaran/kwitansi/{transaksi}', [TerimaPembayaranController::class, 'showKwitansi'])->name('pembayaran.kwitansi');
    Route::get('/terima-pembayaran/kwitansi/{transaksi}/print', [TerimaPembayaranController::class, 'printKwitansi'])->name('pembayaran.kwitansi.print');

    // Riwayat Transaksi
    Route::prefix('transaksi')->middleware('auth')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
        Route::get('/{transaksi}/kwitansi', [TransaksiController::class, 'printKwitansi'])->name('transaksi.kwitansi');
        Route::get('/export', [TransaksiController::class, 'export'])->name('transaksi.export');
    });

    // Laporan
    Route::prefix('laporan')->middleware('auth')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/pembayaran', [LaporanController::class, 'pembayaran'])->name('laporan.pembayaran');
        Route::get('/tunggakan', [LaporanController::class, 'tunggakan'])->name('laporan.tunggakan');
        Route::get('/per-kelas', [LaporanController::class, 'perKelas'])->name('laporan.per-kelas');

        // Export routes
        Route::get('/export/pembayaran', [LaporanController::class, 'exportPembayaran'])->name('laporan.export.pembayaran');
        Route::get('/export/tunggakan', [LaporanController::class, 'exportTunggakan'])->name('laporan.export.tunggakan');
        Route::get('/export/per-kelas', [LaporanController::class, 'exportPerKelas'])->name('laporan.export.per-kelas');
    });
    // Routes untuk Siswa Side
    Route::prefix('siswa-side')->middleware(['auth'])->group(function () {
        Route::get('/tagihan', [SiswaSideController::class, 'tagihan'])->name('siswa.tagihan');
        Route::get('/tagihan/semua-tagihan', [SiswaSideController::class, 'semuaTagihan'])->name('siswa.tagihan.semua-tagihan');
        Route::get('/history', [SiswaSideController::class, 'historyPembayaran'])->name('siswa.history');
        Route::get('/kwitansi/{transaksi}', [SiswaSideController::class, 'kwitansi'])->name('siswa.kwitansi');
        Route::get('/profil', [SiswaSideController::class, 'profil'])->name('siswa.profil');
    });
});



// Payment Routes (Siswa & Admin)
Route::prefix('payment')->middleware('auth')->group(function() {
    Route::get('/tagihan/{tagihan}', [PaymentController::class, 'index'])->name('payment.index');
    Route::post('/create/{tagihan}', [PaymentController::class, 'create'])->name('payment.create');
    Route::get('/check-status/{orderId}', [PaymentController::class, 'checkStatus'])->name('payment.check-status');
    Route::get('/detail/{orderId}', [PaymentController::class, 'detail'])->name('payment.detail');
    
    // Callback URLs
    Route::get('/finish', [PaymentController::class, 'finish'])->name('payment.finish');
    Route::get('/unfinish', [PaymentController::class, 'unfinish'])->name('payment.unfinish');
    Route::get('/error', [PaymentController::class, 'error'])->name('payment.error');
});

// Webhook (public, no auth)
Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');

// Payment History (Siswa only)
Route::get('/siswa/payment-history', [PaymentController::class, 'history'])
     ->middleware(['auth', 'role:siswa'])
     ->name('siswa.payment.history');