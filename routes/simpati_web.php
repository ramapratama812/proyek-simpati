<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResearchProjectController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PddiktiController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ProfileController;

// =======================================================
// ===================== AUTH ROUTES =====================
// =======================================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.post');

// =======================================================
// ================ ROUTES DENGAN AUTH ===================
// =======================================================
Route::middleware('auth')->group(function () {

    // ===================== DEFAULT REDIRECT =====================
    Route::get('/', function () {
        $user = auth()->user();

        if ($user->role === 'mahasiswa') {
            return redirect()->route('dashboard.mahasiswa');
        } elseif ($user->role === 'dosen') {
            return redirect()->route('dashboard.dosen');
        }

        return redirect()->route('dashboard');
    });

    // ===================== DASHBOARD =====================
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ===================== DASHBOARD PER ROLE =====================
    Route::get('/dashboard/mahasiswa', [MahasiswaController::class, 'index'])
        ->name('dashboard.mahasiswa');
    Route::get('/dashboard/dosen', [DosenController::class, 'index'])
        ->name('dashboard.dosen');

    // ===================== MAHASISWA =====================
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::get('/mahasiswa/search', [MahasiswaController::class, 'search'])->name('mahasiswa.search');
    Route::get('/mahasiswa/detail/{id}', [MahasiswaController::class, 'show'])->name('mahasiswa.detail');
    Route::get('/mahasiswa/profile', [MahasiswaController::class, 'profile'])->name('mahasiswa.profile'); // ✅ Tambahan agar tidak error
    Route::resource('mahasiswa', MahasiswaController::class)->except(['index']);

    // ===================== DOSEN =====================
    Route::get('/dosen', [DosenController::class, 'index'])->name('dosen.index');
    Route::resource('dosen', DosenController::class)->except(['index']);

    // ===================== PROFIL USER =====================
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ===================== PROJECTS & PUBLICATIONS =====================
    Route::resource('projects', ResearchProjectController::class);
    Route::resource('publications', PublicationController::class);

    // ===================== IMPORT & SYNC PDDIKTI =====================
    Route::post('/import/crossref', [ImportController::class, 'crossrefByDoi'])->name('import.crossref');
    Route::post('/import/bibtex', [ImportController::class, 'bibtexUpload'])->name('import.bibtex');
    Route::post('/import/oai', [ImportController::class, 'oaiHarvest'])->name('import.oai');

    Route::post('/sync/pddikti/dosen', [PddiktiController::class, 'syncDosen'])->name('sync.pddikti.dosen');
    Route::post('/sync/pddikti/mahasiswa', [PddiktiController::class, 'syncMhs'])->name('sync.pddikti.mhs');
});
