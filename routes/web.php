<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\BukuTabunganController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Route utama
Route::get('/', function () {
    return redirect()->route('login');
});

// Route untuk dashboard menggunakan DashboardController
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Route dengan middleware auth
Route::middleware('auth')->group(function () {
    // Route untuk profil pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route untuk Tahun Ajaran
    Route::resource('tahun-ajaran', TahunAjaranController::class)->except(['show']);

    // Route untuk Kelas
    Route::resource('kelas', KelasController::class);

    // Route untuk Siswa
    Route::resource('siswa', SiswaController::class);

    // Route untuk Buku Tabungan
    Route::resource('buku-tabungan', BukuTabunganController::class);

    // Route untuk Transaksi
    Route::resource('transaksi', TransaksiController::class);

    // Route AJAX untuk mendapatkan Buku Tabungan berdasarkan Kelas
    Route::get('/get-buku-tabungan-by-kelas/{kelasId}', [TransaksiController::class, 'getBukuTabunganByKelas'])
    ->name('get-buku-tabungan-by-kelas');

});

require __DIR__.'/auth.php';
