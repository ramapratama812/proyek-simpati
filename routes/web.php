<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\ResearchProjectController;
use App\Http\Controllers\ProjectPublicationController;
use App\Http\Controllers\Auth\GoogleRegisterController;
use App\Http\Controllers\Auth\RegistrationRequestController;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/simpati_web.php';

// Resource routes untuk ResearchProject
Route::resource('projects', ResearchProjectController::class);

// Dokumentasi (gambar) — hanya peserta (ketua/anggota) yang boleh upload
Route::post('projects/{project}/images', [ResearchProjectController::class, 'storeImages'])
    ->name('projects.images.store');

// Hapus gambar — akses dibatasi hanya untuk pemilik/ketua
Route::delete('projects/{project}/images/{image}', [ResearchProjectController::class, 'destroyImage'])
    ->name('projects.images.destroy');

Route::get('projects/{project}/publications', [ProjectPublicationController::class, 'index'])
    ->name('projects.publications.index');

Route::post('projects/{project}/publications/attach', [ProjectPublicationController::class, 'attach'])
    ->name('projects.publications.attach');

Route::delete('/projects/{project}/publications/{publication}',
    [ProjectPublicationController::class, 'destroy']
)->name('projects.publications.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/publications/create', [PublicationController::class, 'create'])->name('publications.create');
    Route::post('/publications', [PublicationController::class, 'store'])->name('publications.store');

    // BARU untuk edit/update/destroy:
    Route::get('/publications/{publication}/edit', [PublicationController::class, 'edit'])->name('publications.edit');
    Route::put('/publications/{publication}', [PublicationController::class, 'update'])->name('publications.update');
    Route::delete('/publications/{publication}', [PublicationController::class, 'destroy'])->name('publications.destroy');
});

// show publikasi
Route::get('/publications/{publication}', [PublicationController::class, 'show'])->name('publications.show');

// Tombol login / daftar pakai Google, role dikirim lewat query ?role=dosen / ?role=mahasiswa
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])
    ->name('google.redirect');

// Callback dari Google
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('google.callback');

// Lengkapi data pendaftaran Google (jika perlu)
Route::get('/register/google/complete', [GoogleRegisterController::class, 'showCompleteForm'])
    ->name('register.google.complete');
Route::post('/register/google/complete', [GoogleRegisterController::class, 'storeComplete'])
    ->name('register.google.store');

// Tombol login / daftar pakai Google, role dikirim lewat query ?role=dosen / ?role=mahasiswa
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');

// Route untuk update status validasi pendaftaran (approve/reject)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/registration-requests', [RegistrationRequestController::class, 'index'])
        ->name('admin.registration-requests.index');

    Route::get('/admin/registration-requests/{registrationRequest}', [RegistrationRequestController::class, 'show'])
        ->name('admin.registration-requests.show');

    Route::post('/admin/registration-requests/{registrationRequest}/approve',
        [RegistrationRequestController::class, 'approve']
    )->name('admin.registration-requests.approve');

    Route::post('/admin/registration-requests/{registrationRequest}/reject',
        [RegistrationRequestController::class, 'reject']
    )->name('admin.registration-requests.reject');
});

// Route untuk update status validasi publikasi (belum dipakai)
Route::middleware(['auth'])->group(function () {
    Route::post('/admin/publications/{publication}/status',
        [PublicationController::class, 'updateStatus']
    )->name('admin.publications.update-status');
});
