<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\BwmController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\MarcosController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;

// Public Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Administrator Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/panduan', [DashboardController::class, 'panduan'])->name('panduan');

    // Profil Admin
    Route::get('/profil', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profil', [AuthController::class, 'updateProfile'])->name('profile.update');

    // 1. Kriteria (Master Data)
    Route::get('/kriteria', [KriteriaController::class, 'index'])->name('kriteria.index');
    Route::post('/kriteria', [KriteriaController::class, 'store'])->name('kriteria.store');
    Route::put('/kriteria/{id}', [KriteriaController::class, 'update'])->name('kriteria.update');
    Route::delete('/kriteria/{id}', [KriteriaController::class, 'destroy'])->name('kriteria.destroy');

    // 2. Alternatif / Warga
    Route::get('/warga', [WargaController::class, 'index'])->name('warga.index');
    Route::post('/warga', [WargaController::class, 'store'])->name('warga.store');
    Route::get('/warga/{id}/detail', [WargaController::class, 'getDetail'])->name('warga.detail');
    Route::put('/warga/{id}', [WargaController::class, 'update'])->name('warga.update');
    Route::delete('/warga/{id}', [WargaController::class, 'destroy'])->name('warga.destroy');

    // 3. Pembobotan BWM (Stepped Wizard)
    Route::get('/bwm', [BwmController::class, 'index'])->name('bwm.index');
    Route::post('/bwm/calculate', [BwmController::class, 'calculate'])->name('bwm.calculate');
    Route::post('/bwm/save', [BwmController::class, 'save'])->name('bwm.save');

    // 4. Matriks Penilaian Riil Warga
    Route::get('/penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
    Route::post('/penilaian/batch', [PenilaianController::class, 'batchUpdate'])->name('penilaian.batch');
    Route::post('/penilaian/single', [PenilaianController::class, 'updateSingle'])->name('penilaian.single');

    // 5. Analisis MARCOS (Tabbed View)
    Route::get('/marcos', [MarcosController::class, 'index'])->name('marcos.index');
    Route::post('/marcos/calculate', [MarcosController::class, 'calculateAndSave'])->name('marcos.calculate');

    // 6. Laporan & Rekomendasi
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export');

    // 7. Pengaturan Profil Desa & Kop Surat
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
});
