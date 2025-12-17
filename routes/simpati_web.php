<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AhpController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PddiktiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DosenPrestasiController;
use App\Http\Controllers\ResearchProjectController;
use App\Http\Controllers\DosenBerprestasiController;
use App\Http\Controllers\GoogleDriveTokenController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\AhpCriteriaComparisonController;
use App\Http\Controllers\Admin\PublicationValidationController;

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

    // --- Mahasiswa ---
    // Note: Route custom harus diletakkan SEBELUM Route::resource agar tidak tertimpa oleh {id}
    Route::get('/mahasiswa/search', [MahasiswaController::class, 'search'])->name('mahasiswa.search');
    // Jika 'detail' hanya alias untuk 'show', sebenarnya resource sudah menghandle ini via mahasiswa/{id}
    Route::get('/mahasiswa/detail/{id}', [MahasiswaController::class, 'show'])->name('mahasiswa.detail');
    Route::resource('mahasiswa', MahasiswaController::class);
    Route::put('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'updateProfile'])
    ->name('mahasiswa.update');

    // ==================================================
    // 🔹 Kegiatan & Publikasi (Dosen)
    // ==================================================

    // Halaman "Milik Saya"
    Route::get('/kegiatan/kelola', [ResearchProjectController::class, 'myProjects'])->name('projects.my');
    Route::get('/publikasi/kelola', [PublicationController::class, 'myPublications'])->name('publications.my');

    // Get detail publikasi untuk detail profile dosen
    Route::get('/publikasi/{id}', [PublicationController::class, 'show'])->name('publikasi.show');

    // CRUD Utama
    Route::resource('projects', ResearchProjectController::class);
    Route::resource('publications', PublicationController::class);

    // Validasi Kegiatan dan Publikasi (Diajukan oleh Dosen)
    Route::post('/projects/{project}/ajukan-validasi', [ResearchProjectController::class, 'submitValidation'])
        ->name('projects.submitValidation');
    Route::post('/publications/{publication}/submit', [PublicationController::class, 'submitValidation'])
        ->name('publications.submit');

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

    Route::middleware(['role:admin'])
        ->prefix('admin/publications')
        ->name('admin.publications.')
        ->group(function () {
            Route::get('validation', [PublicationValidationController::class, 'index'])->name('validation.index');
            Route::get('validation/{publication}', [PublicationValidationController::class, 'show'])->name('validation.show');
            Route::post('validation/{publication}', [PublicationValidationController::class, 'update'])->name('validation.update');
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
            Route::post('/sync-sinta', 'syncSinta')->name('sync_sinta'); // Sync SINTA
        });

    // ==================================================
    // 🔹 TPK Dosen Berprestasi
    // ==================================================

    Route::middleware(['auth'])->group(function () {
        Route::post('/ahp/kriteria/hitung', [AhpController::class, 'hitungKriteria'])
            ->name('ahp.kriteria.hitung');

        Route::post('/tpk/dosen-berprestasi/hitung', [DosenBerprestasiController::class, 'hitung'])
            ->name('tpk.dosen_berprestasi.hitung');

        Route::get('/ahp/kriteria/comparisons', [AhpCriteriaComparisonController::class, 'edit'])
            ->name('ahp.criteria_comparisons.edit');

        Route::post('/ahp/kriteria/comparisons', [AhpCriteriaComparisonController::class, 'update'])
            ->name('ahp.criteria_comparisons.update');

        Route::post('/ahp/kriteria/comparisons/calculate', [AhpCriteriaComparisonController::class, 'calculateWeights'])
            ->name('ahp.criteria_comparisons.calculate');

        Route::get('/tpk/dosen-berprestasi', [DosenBerprestasiController::class, 'index'])
            ->name('tpk.dosen_berprestasi.index');

        Route::post('/tpk/dosen-berprestasi/sync-internal', [DosenBerprestasiController::class, 'syncInternal'])
            ->name('tpk.dosen_berprestasi.sync_internal');

        Route::post('/tpk/dosen-berprestasi/sync-sinta', [DosenBerprestasiController::class, 'syncSinta'])
            ->name('tpk.dosen_berprestasi.sync_sinta');
        Route::get('/tpk/dosen-berprestasi/export', [DosenBerprestasiController::class, 'exportExcel'])
            ->name('tpk.dosen_berprestasi.export');
        });

    // ==================================================
    // 🔹 Notifikasi Sistem
    // ==================================================

    Route::middleware(['auth'])->group(function () {
        Route::post('/notifications/{notification}/read',   [UserNotificationController::class, 'markRead'])
            ->name('notifications.mark_read');

        Route::post('/notifications/{notification}/unread', [UserNotificationController::class, 'markUnread'])
            ->name('notifications.mark_unread');
        });

    Route::middleware('auth')->group(function () {
        Route::get('/integrations/google-drive/connect', [GoogleDriveController::class, 'redirect'])
            ->name('gdrive.connect');

        Route::get('/integrations/google-drive/callback', [GoogleDriveController::class, 'callback'])
            ->name('gdrive.callback');

        // JS Picker butuh access token → kita kasih token short-lived via endpoint ini
        Route::get('/integrations/google-drive/token', [GoogleDriveTokenController::class, 'show'])
            ->name('gdrive.token');
    });
});
