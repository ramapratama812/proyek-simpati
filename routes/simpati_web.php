<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RegistrationRequestController;
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
use App\Http\Controllers\AHP\CriteriaComparisonController;
use App\Http\Controllers\DosenBerprestasiController;
use App\Http\Controllers\KegiatanController;

/*
|--------------------------------------------------------------------------
| AUTHENTICATION (GUEST)
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.post');

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (LOGGED IN USERS)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* ================= PROFILE (Update Profil Sendiri) ================= */
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'show')->name('show');
        Route::get('/edit', 'edit')->name('edit');
        Route::put('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    /* ================= DOSEN ================= */
    Route::resource('dosen', DosenController::class);
    Route::resource('dosen-prestasi', DosenPrestasiController::class);

    /* ================= MAHASISWA ================= */
    // 1. Route Custom ditaruh DI ATAS Route::resource agar tidak dianggap sebagai ID
    Route::get('/mahasiswa/search', [MahasiswaController::class, 'search'])->name('mahasiswa.search');
    Route::get('/mahasiswa/{id}/biodata', [MahasiswaController::class, 'show'])->name('mahasiswa.biodata');
    Route::post('/mahasiswa/kegiatan/{project}/ikut', [MahasiswaController::class, 'ikutKegiatan'])->name('mahasiswa.ikutKegiatan');

    // 2. Route Resource (Otomatis membuat index, create, store, show, edit, update, destroy)
    // Ini yang menangani route('mahasiswa.update', $id)
    Route::resource('mahasiswa', MahasiswaController::class);

    /* ================= KEGIATAN UMUM ================= */
    Route::resource('kegiatan', KegiatanController::class);

    /* ================= RESEARCH PROJECT ================= */
    Route::resource('research-projects', ResearchProjectController::class)
         ->parameters(['research-projects' => 'project']);
         
    Route::resource('projects', ResearchProjectController::class)
         ->parameters(['projects' => 'project']);

    Route::get('/kegiatan/kelola', [ResearchProjectController::class, 'myProjects'])->name('projects.my');
    Route::post('/projects/{project}/ajukan-validasi', [ResearchProjectController::class, 'submitValidation'])->name('projects.submitValidation');

    /* ================= PUBLIKASI ================= */
    Route::resource('publications', PublicationController::class)
         ->parameters(['publications' => 'publication']);
         
    Route::get('/publikasi/kelola', [PublicationController::class, 'myPublications'])->name('publications.my');
    Route::post('/publications/{publication}/submit', [PublicationController::class, 'submitValidation'])->name('publications.submit');

    /* ================= TOOLS / IMPORT ================= */
    Route::post('/import/crossref', [ImportController::class, 'crossrefByDoi'])->name('import.crossref');
    Route::post('/import/bibtex', [ImportController::class, 'bibtexUpload'])->name('import.bibtex');
    Route::post('/sync/pddikti/dosen', [PddiktiController::class, 'syncDosen'])->name('sync.pddikti.dosen');
    Route::post('/sync/pddikti/mahasiswa', [PddiktiController::class, 'syncMhs'])->name('sync.pddikti.mhs');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        /* 1. KELOLA PERMOHONAN AKUN */
        Route::resource('registration-requests', RegistrationRequestController::class)->only(['index', 'show']);
        Route::post('registration-requests/{registrationRequest}/approve', [RegistrationRequestController::class, 'approve'])->name('registration-requests.approve');
        Route::post('registration-requests/{registrationRequest}/reject', [RegistrationRequestController::class, 'reject'])->name('registration-requests.reject');

        /* 2. VALIDASI KEGIATAN */
        Route::prefix('kegiatan/validasi')->name('projects.validation.')->controller(ResearchProjectController::class)->group(function () {
            Route::get('/', 'validationIndex')->name('index');
            Route::get('/{project}', 'validationShow')->name('show');
            Route::post('/{project}/approve', 'approveValidation')->name('approve');
            Route::post('/{project}/revision', 'requestRevision')->name('revision');
            Route::post('/{project}/reject', 'rejectValidation')->name('reject');
        });

        /* 3. VALIDASI PUBLIKASI */
        Route::prefix('publications/validation')->name('publications.validation.')->controller(PublicationValidationController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{publication}', 'show')->name('show');
            Route::post('/{publication}', 'update')->name('update');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | AHP & TPK ROUTES (Admin Only)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        
        // AHP
        Route::prefix('ahp')->name('ahp.')->group(function () {
            Route::get('/criteria-comparisons/edit', [CriteriaComparisonController::class, 'edit'])->name('criteria_comparisons.edit');
            Route::put('/criteria-comparisons/update', [CriteriaComparisonController::class, 'update'])->name('criteria_comparisons.update');
            Route::post('/kriteria/hitung', [AhpController::class, 'hitungKriteria'])->name('kriteria.hitung');
        });

        // TPK
        Route::prefix('tpk')->name('tpk.')->controller(DosenBerprestasiController::class)->group(function () {
            Route::get('/dosen-berprestasi', 'index')->name('dosen_berprestasi.index');
            Route::post('/dosen-berprestasi/hitung', 'hitung')->name('dosen_berprestasi.hitung');
        });
    });

});