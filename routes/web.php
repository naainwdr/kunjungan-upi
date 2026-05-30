<?php

use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminKunjunganController;
use App\Http\Controllers\Admin\AdminTempatController;
use App\Http\Controllers\Admin\AdminSesiController;
use App\Http\Controllers\Admin\AdminKalenderController;
use App\Http\Controllers\Admin\PresensiController;
use App\Http\Controllers\Admin\AdminSurveiController;
use App\Http\Controllers\Petugas\PetugasAuthController;
use App\Http\Controllers\Petugas\PetugasPresensiController;
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
Route::post('/reservasi/{id}/batal', [KunjunganController::class, 'batal'])->name('reservasi.batal');
Route::get('/api/booked-sesi', [KunjunganController::class, 'bookedSesi'])->name('api.booked-sesi');

// Evaluasi
Route::get('/evaluasi/{id}', [KunjunganController::class, 'evaluasiForm'])->name('evaluasi.form');
Route::post('/evaluasi/{id}', [KunjunganController::class, 'simpanEvaluasi'])->name('evaluasi.simpan');
Route::get('/evaluasi/{id}/terima-kasih', [KunjunganController::class, 'terimaKasih'])->name('evaluasi.terima-kasih');

// Tiket Digital
Route::get('/tiket/{nomor}', function($nomor) {
    $kunjungan = \App\Models\Kunjungan::with(['sekolah','kontak','sesi','tempat','presensi'])
        ->where('nomor_registrasi', $nomor)->firstOrFail();
    return view('public.tiket', compact('kunjungan'));
})->name('tiket.show');

// Survei Kepuasan
Route::get('/survei/{nomor}', [\App\Http\Controllers\SurveiController::class, 'form'])->name('survei.form');
Route::post('/survei/{nomor}', [\App\Http\Controllers\SurveiController::class, 'store'])->name('survei.store');
Route::get('/survei/{nomor}/terima-kasih', [\App\Http\Controllers\SurveiController::class, 'terimaKasih'])->name('survei.terima-kasih');

// ============================================================
// Admin - Authentication & Protected Routes
// ============================================================

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected Admin Routes — hanya role 'admin'
    Route::middleware(['auth', 'role.admin'])->group(function () {
        Route::get('/dashboard', [AdminKunjunganController::class, 'dashboard'])->name('dashboard');
        Route::get('/kunjungan/{kunjungan}', [AdminKunjunganController::class, 'show'])->name('kunjungan.show');
        Route::post('/kunjungan/{kunjungan}/approve', [AdminKunjunganController::class, 'approve'])->name('kunjungan.approve');
        Route::post('/kunjungan/{kunjungan}/reject', [AdminKunjunganController::class, 'reject'])->name('kunjungan.reject');
        Route::post('/kunjungan/{kunjungan}/complete', [AdminKunjunganController::class, 'complete'])->name('kunjungan.complete');

        // Scanner & Presensi (masih tersedia di admin, tapi idealnya pakai /petugas)
        Route::get('/scanner', [PresensiController::class, 'scanner'])->name('scanner');
        Route::get('/scanner/lookup', [PresensiController::class, 'lookup'])->name('scanner.lookup');
        Route::post('/presensi/{kunjungan}/checkin', [PresensiController::class, 'checkIn'])->name('presensi.checkin');
        Route::post('/presensi/{kunjungan}/checkout', [PresensiController::class, 'checkOut'])->name('presensi.checkout');
        Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');

        // Survei Admin
        Route::get('/survei', [AdminSurveiController::class, 'index'])->name('survei.index');
        Route::post('/survei/{survei}/toggle', [AdminSurveiController::class, 'togglePublik'])->name('survei.toggle');

        // Referensi (Tempat & Sesi)
        Route::resource('/tempat', AdminTempatController::class)->except(['create', 'edit', 'show']);
        Route::resource('/sesi', AdminSesiController::class)->except(['create', 'edit', 'show']);

        // Pengaturan Kalender (Tanggal)
        Route::get('/kalender', [AdminKalenderController::class, 'index'])->name('kalender.index');
        Route::post('/kalender', [AdminKalenderController::class, 'store'])->name('kalender.store');
        Route::delete('/kalender/{id}', [AdminKalenderController::class, 'destroy'])->name('kalender.destroy');
    });
});

// ============================================================
// Petugas Presensi - Authentication & Protected Routes
// ============================================================

Route::prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/login', [PetugasAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [PetugasAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [PetugasAuthController::class, 'logout'])->name('logout');

    // Protected Petugas Routes — hanya role 'petugas'
    Route::middleware(['auth', 'role.petugas'])->group(function () {
        Route::get('/scanner', [PetugasPresensiController::class, 'scanner'])->name('scanner');
        Route::get('/scanner/lookup', [PetugasPresensiController::class, 'lookup'])->name('scanner.lookup');
        Route::post('/presensi/{kunjungan}/checkin', [PetugasPresensiController::class, 'checkIn'])->name('presensi.checkin');
        Route::post('/presensi/{kunjungan}/checkout', [PetugasPresensiController::class, 'checkOut'])->name('presensi.checkout');
        Route::get('/presensi', [PetugasPresensiController::class, 'index'])->name('presensi.index');
    });
});
