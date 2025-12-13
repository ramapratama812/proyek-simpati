<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\DosenPrestasiController;
use App\Http\Controllers\ResearchProjectController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\Admin\PublicationValidationController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PddiktiController;
use App\Http\Controllers\AhpController;
use App\Http\Controllers\DosenBerprestasiController;
use App\Http\Controllers\KegiatanController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.post');

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* ================= DOSEN ================= */
    Route::resource('dosen', DosenController::class);
    Route::resource('dosen-prestasi', DosenPrestasiController::class);

    /* ================= MAHASISWA ================= */
    Route::get('/mahasiswa/search', [MahasiswaController::class, 'search'])->name('mahasiswa.search');
    Route::get('/mahasiswa/{id}/biodata', [MahasiswaController::class, 'show'])->name('mahasiswa.biodata');
    Route::resource('mahasiswa', MahasiswaController::class);
    Route::put('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'updateProfile'])->name('mahasiswa.update');
    Route::post('/mahasiswa/kegiatan/{project}/ikut', [MahasiswaController::class, 'ikutKegiatan'])->name('mahasiswa.ikutKegiatan');

    /* ================= KEGIATAN UMUM ================= */
    Route::resource('kegiatan', KegiatanController::class);

    /* ================= RESEARCH PROJECT ================= */
    Route::resource('research-projects', ResearchProjectController::class); // Utama
    Route::resource('projects', ResearchProjectController::class); // Alias lama

    Route::get('/kegiatan/kelola', [ResearchProjectController::class, 'myProjects'])->name('projects.my');
    Route::post('/projects/{project}/ajukan-validasi', [ResearchProjectController::class, 'submitValidation'])->name('projects.submitValidation');

    /* ================= PUBLIKASI ================= */
    Route::resource('publications', PublicationController::class);
    Route::get('/publikasi/kelola', [PublicationController::class, 'myPublications'])->name('publications.my');
    Route::post('/publications/{publication}/submit', [PublicationController::class, 'submitValidation'])->name('publications.submit');

    /* ================= ADMIN VALIDASI KEGIATAN ================= */
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

    /* ================= ADMIN VALIDASI PUBLIKASI ================= */
    Route::middleware(['role:admin'])
        ->prefix('admin/publications')
        ->name('admin.publications.')
        ->group(function () {
            Route::get('validation', [PublicationValidationController::class, 'index'])->name('validation.index');
            Route::get('validation/{publication}', [PublicationValidationController::class, 'show'])->name('validation.show');
            Route::post('validation/{publication}', [PublicationValidationController::class, 'update'])->name('validation.update');
        });

    /* ================= TOOLS / IMPORT ================= */
    Route::post('/import/crossref', [ImportController::class, 'crossrefByDoi'])->name('import.crossref');
    Route::post('/import/bibtex', [ImportController::class, 'bibtexUpload'])->name('import.bibtex');
    Route::post('/sync/pddikti/dosen', [PddiktiController::class, 'syncDosen'])->name('sync.pddikti.dosen');
    Route::post('/sync/pddikti/mahasiswa', [PddiktiController::class, 'syncMhs'])->name('sync.pddikti.mhs');

    /* ================= PROFILE ================= */
    Route::prefix('profile')
        ->name('profile.')
        ->controller(ProfileController::class)
        ->group(function () {
            Route::get('/', 'show')->name('show');
            Route::get('/edit', 'edit')->name('edit');
            Route::put('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
        });

    /* ================= AHP ================= */
    Route::post('/ahp/kriteria/hitung', [AhpController::class, 'hitungKriteria'])->name('ahp.kriteria.hitung');

    /* ================= TPK Dosen Berprestasi ================= */
    Route::prefix('tpk')->name('tpk.')->group(function () {
        Route::get('/dosen-berprestasi', [DosenBerprestasiController::class, 'index'])->name('dosen_berprestasi.index');
        Route::post('/dosen-berprestasi/hitung', [DosenBerprestasiController::class, 'hitung'])->name('dosen_berprestasi.hitung');
    });
});
