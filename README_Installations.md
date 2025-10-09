# SIMPATI Starter (Laravel 12)

Starter berisi controller, model, service, migration, route, dan view dasar untuk Sistem Informasi Manajemen Pengelolaan Kepegawaian TI (SIMPATI).

## Cara Pakai (Instalasi Baru)
1) Buat proyek Laravel 12 baru:
```bash
composer create-project laravel/laravel simpati "12.*"
cd simpati
```
2) Tambahkan dependensi:
```bash
composer require guzzlehttp/guzzle renanbr/bibtex-parser
```
3) Ekstrak isi ZIP ini ke dalam **root** proyek Laravel kamu (akan menambah/menimpa file-file pada `app/`, `database/`, `resources/`, dan `routes/`).  
   - File route khusus disimpan di `routes/simpati_web.php`.  
   - Buka `routes/web.php` kamu dan tambahkan baris berikut di bagian paling bawah:
```php
require __DIR__.'/simpati_web.php';
```
4) Konfigurasi `.env` (contoh penting):
```
FILESYSTEM_DISK=public
```
Opsional (untuk Feeder):
```
PDDIKTI_BASE=http://localhost:3003
PDDIKTI_USER=operator_username
PDDIKTI_PASS=operator_password
```
5) Buat symlink storage dan migrasi:
```bash
php artisan storage:link
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
```
6) Jalankan:
```bash
php artisan serve
```
7) Login Admin default:
- **Email**: admin@simpati.local
- **Password**: password

> Catatan: Sistem ini **tidak menggunakan** paket role eksternal; kolom `role` pada tabel `users` menangani peran (`admin`, `dosen`, `mahasiswa`).

## Fitur yang Termasuk
- Login (email/username) & Register sederhana.
- Dashboard ringkas (jumlah Penelitian/Pengabdian/Publikasi).
- CRUD Penelitian/Pengabdian (dengan unggah gambar maks 5 file).
- CRUD Publikasi + Impor cepat:
  - DOI → Crossref API
  - Upload BibTeX (ekspor dari Google Scholar)
  - OAI-PMH harvester (jurnal OJS)
- Sinkronisasi PDDIKTI Feeder (opsional, via kredensial operator).

## Struktur
- app/Http/Controllers/Auth/LoginController.php, RegisterController.php
- app/Http/Controllers/DashboardController.php, ResearchProjectController.php, PublicationController.php, ImportController.php, PddiktiController.php
- app/Models/{LecturerProfile,StudentProfile,ResearchProject,ProjectMember,ProjectImage,Publication}.php
- app/Services/{CrossrefService,BibtexImporter,OaiPmhHarvester,PddiktiFeederClient}.php
- database/migrations/*.php
- database/seeders/AdminUserSeeder.php
- resources/views/... (login, register, dashboard, projects, publications)
- routes/simpati_web.php

## Catatan Integrasi
- Crossref API dipakai untuk metadata DOI.
- Impor BibTeX mendukung file ekspor dari Google Scholar (GS tidak memiliki API resmi).
- OAI-PMH untuk jurnal/prosiding (banyak OJS menyediakan endpoint OAI). 
- PDDIKTI Feeder hanya untuk operator PT yang memiliki kredensial.

Semua kode contoh bersifat minimal—silakan kembangkan UI/validasi sesuai kebutuhan.
