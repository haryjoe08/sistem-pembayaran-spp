<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SantriController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/data-santri', function () {
    return view('data-santri');
});

Route::get('/data-santri', [SantriController::class, 'index'])->name('santri.index');
Route::get('/santri/create', [SantriController::class, 'create'])->name('santri.create');
Route::post('/santri', [SantriController::class, 'store'])->name('santri.store');
Route::resource('santri', \App\Http\Controllers\SantriController::class);
