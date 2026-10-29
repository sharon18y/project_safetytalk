<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Koordinator\SafetyTalkController;
use App\Http\Controllers\Manajemen\ReportController;
use App\Http\Controllers\Manajemen\PantauUnitController;
use Illuminate\Support\Facades\Route;

// Saat pertama kali buka link, langsung masuk ke Dashboard (Mode Publik / Manajemen)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard bisa diakses publik tanpa login maupun oleh petugas yang login
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Group Route PEMANTAUAN (Bebas diakses publik tanpa login, tanpa middleware role/auth)
Route::prefix('manajemen')->name('manajemen.')->group(function () {
    // Redirect laporan ke pantau
    Route::redirect('/laporan', '/manajemen/pantau')->name('laporan.index');
    
    // Halaman Detail Laporan
    Route::get('/laporan/{id}/detail', [ReportController::class, 'show'])->name('laporan.show');

    // Pemantauan Unit
    Route::get('/pantau', [PantauUnitController::class, 'index'])->name('pantau.index');
    Route::get('/pantau/pdf', [PantauUnitController::class, 'pdf'])->name('pantau.pdf');
    Route::get('/pantau/{unit}', [PantauUnitController::class, 'show'])->name('pantau.show');
});

// Route Profil khusus pengguna yang sudah login
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Group Route khusus ADMIN K3 (Wajib login & ber-role admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/master-data', [MasterDataController::class, 'index'])->name('master.index');
});

// Group Route khusus KOORDINATOR & ADMIN (Wajib login untuk mengisi laporan)
Route::middleware(['auth', 'role:koordinator,admin'])->prefix('koordinator')->name('koordinator.')->group(function () {
    Route::get('/safety-talk', [SafetyTalkController::class, 'index'])->name('safety-talk.index');
    Route::get('/safety-talk/create', [SafetyTalkController::class, 'create'])->name('safety-talk.create');
    Route::post('/safety-talk', [SafetyTalkController::class, 'store'])->name('safety-talk.store');
    Route::get('/safety-talk/{id}/absensi', [SafetyTalkController::class, 'createAbsensi'])->name('safety-talk.absensi');
    Route::post('/safety-talk/{id}/absensi', [SafetyTalkController::class, 'storeAbsensi'])->name('safety-talk.store-absensi');
    Route::get('/safety-talk/{id}/detail', [SafetyTalkController::class, 'show'])->name('safety-talk.show');
});

require __DIR__.'/auth.php';