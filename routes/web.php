<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResearchProjectController;
use App\Http\Controllers\ProjectPublicationController;
use App\Http\Controllers\PublicationController;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/simpati_web.php';


Route::resource('projects', ResearchProjectController::class);

// Dokumentasi (gambar) — hanya peserta (ketua/anggota) yang boleh upload
Route::post('projects/{project}/images', [ResearchProjectController::class, 'storeImages'])
    ->name('projects.images.store');

// (opsional) hapus gambar — batasi untuk pemilik/ketua
Route::delete('projects/{project}/images/{image}', [ResearchProjectController::class, 'destroyImage'])
    ->name('projects.images.destroy');

Route::get('projects/{project}/publications', [ProjectPublicationController::class, 'index'])
    ->name('projects.publications.index');

Route::post('projects/{project}/publications/attach', [ProjectPublicationController::class, 'attach'])
    ->name('projects.publications.attach');

Route::middleware('auth')->group(function () {
    Route::get('/publications/create', [PublicationController::class, 'create'])->name('publications.create');
    Route::post('/publications', [PublicationController::class, 'store'])->name('publications.store');

    // BARU untuk edit/update/destroy:
    Route::get('/publications/{publication}/edit', [PublicationController::class, 'edit'])->name('publications.edit');
    Route::put('/publications/{publication}', [PublicationController::class, 'update'])->name('publications.update');
    Route::delete('/publications/{publication}', [PublicationController::class, 'destroy'])->name('publications.destroy');
});

// show bisa publik (atau auth), sesuaikan
Route::get('/publications/{publication}', [PublicationController::class, 'show'])->name('publications.show');
