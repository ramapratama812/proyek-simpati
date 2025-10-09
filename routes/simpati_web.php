<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResearchProjectController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PddiktiController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\ProfileController;


// ======================================================
// 🔹 AUTH ROUTES
// ======================================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.post');

// ======================================================
// 🔹 ROUTES YANG HANYA BISA DIAKSES SETELAH LOGIN
// ======================================================
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==================================================
    // 🔹 Dosen
    // ==================================================
    Route::get('/dosen', [DosenController::class, 'index'])->name('dosen.index');       // daftar dosen
    Route::get('/dosen/{dosen}', [DosenController::class, 'show'])->name('dosen.show'); // detail dosen

    // ==================================================
    // 🔹 Mahasiswa (opsional)
    // ==================================================
    Route::get('/mahasiswa', [DashboardController::class, 'students'])->name('mahasiswa.index');

    // ==================================================
    // 🔹 Penelitian & Pengabdian
    // ==================================================
    Route::resource('projects', ResearchProjectController::class);

    // ==================================================
    // 🔹 Publikasi
    // ==================================================
    Route::resource('publications', PublicationController::class);

    // ==================================================
    // 🔹 Import Data (CrossRef, BibTeX, OAI)
    // ==================================================
    Route::post('/import/crossref', [ImportController::class, 'crossrefByDoi'])->name('import.crossref');
    Route::post('/import/bibtex', [ImportController::class, 'bibtexUpload'])->name('import.bibtex');
    Route::post('/import/oai', [ImportController::class, 'oaiHarvest'])->name('import.oai');

    // ==================================================
    // 🔹 Sinkronisasi PDDIKTI
    // ==================================================
    Route::post('/sync/pddikti/dosen', [PddiktiController::class, 'syncDosen'])->name('sync.pddikti.dosen');
    Route::post('/sync/pddikti/mahasiswa', [PddiktiController::class, 'syncMhs'])->name('sync.pddikti.mhs');

    // ==================================================
    // 🔹 Profil Pengguna (Dosen yang login)
    // ==================================================
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'show')->name('show');       // Lihat profil
        Route::get('/edit', 'edit')->name('edit');   // Edit profil
        Route::put('/', 'update')->name('update');   // Simpan perubahan
        Route::delete('/', 'destroy')->name('destroy'); // Hapus akun

        

Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

    });
});
