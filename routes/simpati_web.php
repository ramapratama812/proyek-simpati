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
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DosenPrestasiController;

/*
|--------------------------------------------------------------------------
| AUTH (Login & Register)
|--------------------------------------------------------------------------
*/
// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Register
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.post');

/*
|--------------------------------------------------------------------------
| ROUTES SETELAH LOGIN (PROTECTED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // ==================================================
    // 🔹 Dashboard
    // ==================================================
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==================================================
    // 🔹 Manajemen Pengguna (Dosen & Mahasiswa)
    // ==================================================

    // --- Dosen ---
    Route::resource('dosen', DosenController::class);
    Route::resource('dosen-prestasi', DosenPrestasiController::class);

    // --- Mahasiswa ---
    // Note: Route custom harus diletakkan SEBELUM Route::resource agar tidak tertimpa oleh {id}
    Route::get('/mahasiswa/search', [MahasiswaController::class, 'search'])->name('mahasiswa.search');
    // Jika 'detail' hanya alias untuk 'show', sebenarnya resource sudah menghandle ini via mahasiswa/{id}
    Route::get('/mahasiswa/detail/{id}', [MahasiswaController::class, 'show'])->name('mahasiswa.detail');
    Route::resource('mahasiswa', MahasiswaController::class);

    // ==================================================
    // 🔹 Kegiatan & Publikasi (Dosen)
    // ==================================================

    // Halaman "Milik Saya"
    Route::get('/kegiatan/kelola', [ResearchProjectController::class, 'myProjects'])->name('projects.my');
    Route::get('/publikasi/kelola', [PublicationController::class, 'myPublications'])->name('publications.my');

    // CRUD Utama
    Route::resource('projects', ResearchProjectController::class);
    Route::resource('publications', PublicationController::class);

    // Validasi Kegiatan (Diajukan oleh Dosen)
    Route::post('/projects/{project}/ajukan-validasi', [ResearchProjectController::class, 'submitValidation'])
        ->name('projects.submitValidation');

    // ==================================================
    // 🔹 Validasi Kegiatan (Area Admin/Kaprodi)
    // ==================================================
    Route::prefix('admin')
        ->name('projects.validation.')
        ->controller(ResearchProjectController::class)
        ->group(function () {
            Route::get('/kegiatan/validasi', 'validationIndex')->name('index');
            Route::get('/kegiatan/validasi/{project}', 'validationShow')->name('show');
            Route::post('/kegiatan/validasi/{project}/approve', 'approveValidation')->name('approve');
            Route::post('/kegiatan/validasi/{project}/revision', 'requestRevision')->name('revision');
            Route::post('/kegiatan/validasi/{project}/reject', 'rejectValidation')->name('reject');
        });

    // ==================================================
    // 🔹 Tools & Utilities
    // ==================================================

    // Import Data
    Route::post('/import/crossref', [ImportController::class, 'crossrefByDoi'])->name('import.crossref');
    Route::post('/import/bibtex', [ImportController::class, 'bibtexUpload'])->name('import.bibtex');

    // Sinkronisasi PDDIKTI
    Route::post('/sync/pddikti/dosen', [PddiktiController::class, 'syncDosen'])->name('sync.pddikti.dosen');
    Route::post('/sync/pddikti/mahasiswa', [PddiktiController::class, 'syncMhs'])->name('sync.pddikti.mhs');

    // ==================================================
    // 🔹 Profil Pengguna
    // ==================================================
    Route::prefix('profile')
        ->name('profile.')
        ->controller(ProfileController::class)
        ->group(function () {
            Route::get('/', 'show')->name('show');       // Lihat profil
            Route::get('/edit', 'edit')->name('edit');   // Edit profil
            Route::put('/', 'update')->name('update');   // Simpan perubahan
            Route::delete('/', 'destroy')->name('destroy'); // Hapus akun
        });
});
