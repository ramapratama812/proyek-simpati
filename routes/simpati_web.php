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
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.post');


/*
|--------------------------------------------------------------------------
| ROUTES SETELAH LOGIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Dosen Prestasi
    |--------------------------------------------------------------------------
    */
    Route::resource('dosen-prestasi', DosenPrestasiController::class);

    /*
    |--------------------------------------------------------------------------
    | Dosen (CRUD)
    |--------------------------------------------------------------------------
    */
    Route::get('/dosen', [DosenController::class, 'index'])->name('dosen.index');
    Route::get('/dosen/{dosen}', [DosenController::class, 'show'])->name('dosen.show');
    Route::get('/dosen/{dosen}/edit', [DosenController::class, 'edit'])->name('dosen.edit');
    Route::put('/dosen/{dosen}', [DosenController::class, 'update'])->name('dosen.update');
    Route::delete('/dosen/{dosen}', [DosenController::class, 'destroy'])->name('dosen.destroy');

    /*
    |--------------------------------------------------------------------------
    | Mahasiswa (CRUD)
    |--------------------------------------------------------------------------
    */
    Route::get('/mahasiswa/search', [MahasiswaController::class, 'search'])->name('mahasiswa.search');
    Route::get('/mahasiswa/detail/{id}', [MahasiswaController::class, 'show'])->name('mahasiswa.detail');

    // resource tanpa index agar tidak duplikasi
    Route::resource('mahasiswa', MahasiswaController::class)->except(['index']);

    // Index mahasiswa (pisah untuk menghindari konflik)
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');

    /*
    |--------------------------------------------------------------------------
    | Research Projects (Penelitian & Pengabdian)
    |--------------------------------------------------------------------------
    */
    Route::resource('projects', ResearchProjectController::class);

    // Ajukan validasi kegiatan oleh pembuat
    Route::post('/projects/{project}/ajukan-validasi',
        [ResearchProjectController::class, 'submitValidation']
    )->name('projects.submitValidation');

    /*
    |--------------------------------------------------------------------------
    | Validasi Kegiatan (Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->name('projects.validation.')
        ->group(function () {

        Route::get('/kegiatan/validasi',
            [ResearchProjectController::class, 'validationIndex']
        )->name('index');

        Route::get('/kegiatan/validasi/{project}',
            [ResearchProjectController::class, 'validationShow']
        )->name('show');

        Route::post('/kegiatan/validasi/{project}/approve',
            [ResearchProjectController::class, 'approveValidation']
        )->name('approve');

        Route::post('/kegiatan/validasi/{project}/revision',
            [ResearchProjectController::class, 'requestRevision']
        )->name('revision');

        Route::post('/kegiatan/validasi/{project}/reject',
            [ResearchProjectController::class, 'rejectValidation']
        )->name('reject');
    });

    /*
    |--------------------------------------------------------------------------
    | Publikasi
    |--------------------------------------------------------------------------
    */
    Route::resource('publications', PublicationController::class);

    /*
    |--------------------------------------------------------------------------
    | Import Publikasi (CrossRef, BibTeX)
    |--------------------------------------------------------------------------
    */
    Route::post('/import/crossref', [ImportController::class, 'crossrefByDoi'])->name('import.crossref');
    Route::post('/import/bibtex', [ImportController::class, 'bibtexUpload'])->name('import.bibtex');

    /*
    |--------------------------------------------------------------------------
    | Sinkronisasi PDDIKTI
    |--------------------------------------------------------------------------
    */
    Route::post('/sync/pddikti/dosen', [PddiktiController::class, 'syncDosen'])->name('sync.pddikti.dosen');
    Route::post('/sync/pddikti/mahasiswa', [PddiktiController::class, 'syncMhs'])->name('sync.pddikti.mhs');

    /*
    |--------------------------------------------------------------------------
    | Profil Pengguna
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'show')->name('show');
        Route::get('/edit', 'edit')->name('edit');
        Route::put('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });
});
