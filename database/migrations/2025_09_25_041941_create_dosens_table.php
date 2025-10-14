<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('dosens', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel users
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');

            // Data utama dosen
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('nomor_hp')->nullable();
            $table->string('nidn')->nullable();

            // Data tambahan
            $table->string('perguruan_tinggi')->nullable();
            $table->string('program_studi')->nullable();
            $table->string('status_ikatan_kerja')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('pendidikan_terakhir')->nullable();

            // Foto profil
            $table->string('foto')->nullable();

            // 🔹 Status aktivitas (enum)
            $table->enum('status_aktivitas', ['Aktif', 'Tidak Aktif', 'Cuti'])
                  ->default('Aktif'); // ✅ default otomatis aktif

            $table->timestamps();
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosens');
    }
};
