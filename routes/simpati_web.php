<?php

// Semua routes CRUD yang sudah digabung

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResearchProjectController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PddiktiController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\ProfileController;



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
    Route::get('/dosen/{dosen}/edit', [DosenController::class, 'edit'])->name('dosen.edit'); // form edit dosen
    Route::put('/dosen/{dosen}', [DosenController::class, 'update'])->name('dosen.update'); // update dosen
    Route::delete('/dosen/{dosen}', [DosenController::class, 'destroy'])->name('dosen.destroy'); // hapus dosen
    // ==================================================
    // 🔹 Mahasiswa (opsional)
    // ==================================================
    Route::get('/mahasiswa', [DashboardController::class, 'students'])->name('mahasiswa.index');

        // CRUD Mahasiswa baru
        Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
        Route::get('/mahasiswa/search', [MahasiswaController::class, 'search'])->name('mahasiswa.search');
        Route::get('/mahasiswa/detail/{id}', [MahasiswaController::class, 'show'])->name('mahasiswa.detail');
        Route::resource('mahasiswa', MahasiswaController::class)->except(['index']);

    // ==================================================
    // 🔹 Projects (Penelitian & Pengabdian)
    // ==================================================
    Route::resource('projects', ResearchProjectController::class);

    // Ajukan validasi kegiatan oleh ketua/pembuat
    Route::post('/projects/{project}/ajukan-validasi', [ResearchProjectController::class, 'submitValidation'])
        ->name('projects.submitValidation');

    // ==================================================
    // 🔹 Validasi Kegiatan oleh Admin
    // ==================================================
    Route::prefix('admin')->name('projects.validation.')->group(function () {
        Route::get('/kegiatan/validasi', [ResearchProjectController::class, 'validationIndex'])
            ->name('index');  // admin.projects.validation.index secara efektif: route('projects.validation.index')

        Route::get('/kegiatan/validasi/{project}', [ResearchProjectController::class, 'validationShow'])
            ->name('show');

        Route::post('/kegiatan/validasi/{project}/approve', [ResearchProjectController::class, 'approveValidation'])
            ->name('approve');

        Route::post('/kegiatan/validasi/{project}/revision', [ResearchProjectController::class, 'requestRevision'])
            ->name('revision');

        Route::post('/kegiatan/validasi/{project}/reject', [ResearchProjectController::class, 'rejectValidation'])
            ->name('reject');
    });

    // ==================================================
    // 🔹 Publikasi
    // ==================================================
    Route::resource('publications', PublicationController::class);

    // ==================================================
    // 🔹 Import Data Publikasi (CrossRef, BibTeX, OAI)
    // ==================================================
    Route::post('/import/crossref', [ImportController::class, 'crossrefByDoi'])->name('import.crossref');
    Route::post('/import/bibtex', [ImportController::class, 'bibtexUpload'])->name('import.bibtex');

    // ==================================================
    // 🔹 Sinkronisasi PDDIKTI (Belum aktif)
    // ==================================================
    Route::post('/sync/pddikti/dosen', [PddiktiController::class, 'syncDosen'])->name('sync.pddikti.dosen');
    Route::post('/sync/pddikti/mahasiswa', [PddiktiController::class, 'syncMhs'])->name('sync.pddikti.mhs');

    // ==================================================
    // 🔹 Profil Pengguna
    // ==================================================
    Route::controller(ProfileController::class)
        ->prefix('profile')
        ->name('profile.')
        ->group(function () {
            Route::get('/', 'show')->name('show');       // Lihat profil
            Route::get('/edit', 'edit')->name('edit');   // Edit profil
            Route::put('/', 'update')->name('update');   // Simpan perubahan
            Route::delete('/', 'destroy')->name('destroy'); // Hapus akun
    });
});
