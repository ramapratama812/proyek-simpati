<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResearchProjectController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PddiktiController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.post');

Route::middleware('auth')->group(function () {
    Route::get('/', fn()=>redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dosen / Mahasiswa listing sederhana (berbasis role)
    Route::get('/dosen', [DashboardController::class, 'lecturers'])->name('dosen.index');
    Route::get('/mahasiswa', [DashboardController::class, 'students'])->name('mahasiswa.index');

    // Projects (Penelitian/Pengabdian)
    Route::resource('projects', ResearchProjectController::class);

    // Publications
    Route::resource('publications', PublicationController::class);

    // Imports
    Route::post('/import/crossref', [ImportController::class, 'crossrefByDoi'])->name('import.crossref');
    Route::post('/import/bibtex', [ImportController::class, 'bibtexUpload'])->name('import.bibtex');
    Route::post('/import/oai', [ImportController::class, 'oaiHarvest'])->name('import.oai');

    // PDDIKTI sync (cek role di controller)
    Route::post('/sync/pddikti/dosen', [PddiktiController::class, 'syncDosen'])->name('sync.pddikti.dosen');
    Route::post('/sync/pddikti/mahasiswa', [PddiktiController::class, 'syncMhs'])->name('sync.pddikti.mhs');
});
