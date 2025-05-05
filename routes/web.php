<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengukuranController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GaleriKegiatanController;
use App\Http\Controllers\KaderController;
use App\Http\Controllers\KMSController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\ResepMakananController;
use App\Http\Controllers\UserController;
use FontLib\Table\Type\name;

Route::get('/', [LandingPageController::class, 'landingPage']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route untuk Admin
Route::middleware(['auth', 'role:Admin'])->group(function() {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::resource('users', UserController::class);
    Route::resource('profiles', ProfileController::class);
    Route::resource('pengukuran', PengukuranController::class)->except(['show']);

    Route::resource('galeri', GaleriKegiatanController::class);
    Route::prefix('informasi')->group(function () {
        Route::get('/video', [InformasiController::class, 'videoIndex'])->name('informasi.video.index');
        Route::get('/video/crate', [InformasiController::class, 'videoCreate'])->name('informasi.video.create');
        Route::post('/video', [InformasiController::class, 'videoStore'])->name('informasi.video.store');
        Route::get('/video/{video}/edit', [InformasiController::class, 'videoEdit'])->name('informasi.video.edit');
        Route::put('/video/{video}', [InformasiController::class, 'videoUpdate'])->name('informasi.video.update');
        Route::delete('/video/{video}', [InformasiController::class, 'videoDestroy'])->name('informasi.video.destroy');
        Route::get('/resep', [ResepMakananController::class, 'recipeIndex'])->name('informasi.recipe.index');
        Route::get('galeri', [GaleriKegiatanController::class, 'index'])->name('informasi.galeri.index');
        Route::get('/create', [GaleriKegiatanController::class, 'create'])->name('create');
        Route::get('/{galeri}/edit', [GaleriKegiatanController::class, 'edit'])->name('edit');
        Route::post('/', [GaleriKegiatanController::class, 'store'])->name('store');
        Route::delete('/{galeri}', [GaleriKegiatanController::class, 'destroy'])->name('destroy');
        Route::get('/galeri/{id}', [GaleriKegiatanController::class, 'show'])->name('galeri.show');
    });
});

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::resource('users', UserController::class);
});

// Route untuk Kader
Route::middleware(['auth', 'role:Kader'])->group(function (){
    Route::get('/kader', [KaderController::class, 'index'])->name('kader.index');
    Route::resource('profiles', ProfileController::class)->only(['index', 'show', 'create', 'edit','store', 'update']);
    Route::resource('pengukuran', PengukuranController::class)->only(['ibdex', 'show', 'create', 'edit', 'store', 'update']);

Route::prefix('informasi')->group(function () {
    Route::get('/video', [InformasiController::class, 'videoIndex'])->name('informasi.video.index');
    Route::get('/galeri', [GaleriKegiatanController::class, 'index'])->name('informasi.galeri.index');
    Route::get('/galeri/{id}', [GaleriKegiatanController::class, 'show'])->name('galeri.show');
    Route::get('/resep', [ResepMakananController::class, 'recipeIndex'])->name('informasi.recipe.index');
    });
});

Route::middleware(['auth', 'role:Manager'])->group(function() {
    Route::get('/manager', [ManagerController::class, 'index'])->name('manager.dashboard');
    Route::get('/manager', [ManagerController::class, 'index'])->name('manager.index');
    Route::resource('profiles', ProfileController::class)->only(['index', 'show']);
    Route::resource('pengukuran', PengukuranController::class)->only('index', 'show');

    Route::prefix('informasi')->group(function() {
        Route::get('/video', [InformasiController::class, 'videoIndex'])->name('informasi.video.index');
        Route::get('/resep', [ResepMakananController::class, 'recipeIndex'])->name('informasi.recipe.index');
        Route::get('/galeri', [GaleriKegiatanController::class, 'index'])->name('informasi.galeri.index');
        Route::get('/galeri/{id}', [GaleriKegiatanController::class, 'show'])->name('galeri.show');
    });
});


Route::middleware(['auth'])->group(function () {
    Route::get('/petugsd', [PetugasController::class, 'index'])->name('petugas.index');
    Route::get('/petugas/create', [PetugasController::class, 'create'])->name('petugas.create');
    Route::post('/petugas', [PetugasController::class, 'store'])->name('petugas.store');
    Route::get('/petugas/{petugas}', [PetugasController::class, 'show'])->name('petugas.show');
    Route::get('/petugas/{petugas}/edit', [PetugasController::class, 'edit'])->name('petugas.edit');
    Route::put('/petugas/{petugas}', [PetugasController::class, 'update'])->name('petugas.update');
    Route::delete('/petugas/{petugas}', [PetugasController::class, 'destroy'])->name('petugas.destroy');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/manager/dashboard', [ManagerController::class, 'index'])->name('manager.dashboard');
    Route::resource('profiles', ProfileController::class);
    Route::resource('pengukuran', PengukuranController::class)->except(['show']);
    Route::resource('galeri', GaleriKegiatanController::class);
    Route::get('pengukuran/download', [PengukuranController::class, 'download'])->name('pengukuran.download');
    Route::get('profile/download-pdf', [ProfileController::class, 'downloadProfilePdf'])->name('profile.download-pdf');
    Route::get('grafik', [PengukuranController::class, 'grafik'])->name('pengukuran.grafik');
    Route::get('/grafik/kms', [KMSController::class, 'index'])->name('grafik.index'); 
    Route::get('/grafik/kms/{id}/{month?}', [KMSController::class, 'showKMS'])->name('pengukuran.kms'); 
    Route::get('/data-per-bulan-tahun/{bulan}/{tahun}', [PengukuranController::class, 'dataPerBulanTahun'])->name('data.per.bulan.tahun');
    Route::get('/data-per-bulan/{bulan}', [PengukuranController::class, 'dataPerBulan']);
    Route::get('/search-pengukuran', [PengukuranController::class, 'searchPengukuran'])->name('search.pengukuran');
    Route::get('/api/data/{year}', [DashboardController::class, 'getMonthlyData']);
    Route::get('/galeri', [GaleriKegiatanController::class, 'index'])->name('galeri.index');
    Route::get('/galeri/create', [GaleriKegiatanController::class, 'create'])->name('galeri.create');
    Route::get('/galeri/{id}/edit', [GaleriKegiatanController::class, 'edit'])->name('galeri.edit');
    Route::get('/galeri/{id}', [GaleriKegiatanController::class, 'show'])->name('galeri.show');
    Route::post('/galeri', [GaleriKegiatanController::class, 'store'])->name('galeri.store');
    Route::delete('/galeri/{galeri}', [GaleriKegiatanController::class, 'destroy'])->name('galeri.destroy');
});


Route::prefix('informasi/video')->group(function () {
    Route::get('/', [InformasiController::class, 'videoIndex'])->name('informasi.video.index');
    Route::get('/create', [InformasiController::class, 'videoCreate'])->name('informasi.video.create');
    Route::post('/', [InformasiController::class, 'videoStore'])->name('informasi.video.store');
    Route::get('/{video}/edit', [InformasiController::class, 'videoEdit'])->name('informasi.video.edit');
    Route::put('/{video}', [InformasiController::class, 'videoUpdate'])->name('informasi.video.update');
    Route::delete('/{video}', [InformasiController::class, 'videoDestroy'])->name('informasi.video.destroy');
});


Route::prefix('informasi')->group(function () {
    Route::get('/resep', [ResepMakananController::class, 'recipeIndex'])->name('informasi.recipe.index');
});

Route::prefix('informasi/galeri')->name('informasi.galeri.')->group(function () {
    Route::get('/', [GaleriKegiatanController::class, 'index'])->name('index');
    Route::get('/create', [GaleriKegiatanController::class, 'create'])->name('create');
    Route::post('/', [GaleriKegiatanController::class, 'store'])->name('store');
    Route::delete('/{galeri}', [GaleriKegiatanController::class, 'destroy'])->name('destroy');
    Route::get('/{galeri}/edit', [GaleriKegiatanController::class, 'edit'])->name('edit');
    Route::get('/galeri/{id}', [GaleriKegiatanController::class, 'show'])->name('galeri.show');
});

