<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dosens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->string('foto')->nullable();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('nomor_hp')->nullable();
            $table->string('nidn')->nullable();
            $table->string('perguruan_tinggi')->nullable();
            $table->string('status_ikatan_kerja')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('program_studi')->nullable();
            $table->string('photo')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('status_aktivitas')->default('Tidak Aktif'); // Ganti ke tidak aktif defaultnya
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosens');
    }
};
