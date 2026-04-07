<?php

use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminKunjunganController;
use Illuminate\Support\Facades\Route;

// ============================================================
// Halaman Publik
// ============================================================

Route::get('/', [KunjunganController::class, 'index'])->name('home');

Route::get('/kalender', [KunjunganController::class, 'kalender'])->name('kalender');

Route::get('/reservasi', [KunjunganController::class, 'create'])->name('reservasi.create');
Route::post('/reservasi', [KunjunganController::class, 'store'])->name('reservasi.store');
Route::get('/reservasi/sukses', [KunjunganController::class, 'sukses'])->name('reservasi.sukses');

Route::get('/cek-status', [KunjunganController::class, 'cekStatus'])->name('cek-status');
Route::post('/cek-status', [KunjunganController::class, 'cariStatus'])->name('cek-status.cari');

// ============================================================
// Admin - Authentication
// ============================================================

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected Admin Routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AdminKunjunganController::class, 'dashboard'])->name('dashboard');
        Route::get('/kunjungan/{kunjungan}', [AdminKunjunganController::class, 'show'])->name('kunjungan.show');
        Route::post('/kunjungan/{kunjungan}/approve', [AdminKunjunganController::class, 'approve'])->name('kunjungan.approve');
        Route::post('/kunjungan/{kunjungan}/reject', [AdminKunjunganController::class, 'reject'])->name('kunjungan.reject');
    });
});
