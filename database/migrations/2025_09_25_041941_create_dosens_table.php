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
        $table->string('foto')->nullable();
        $table->string('nama');
        $table->string('nidn')->nullable();
        $table->string('perguruan_tinggi')->nullable();
        $table->string('status_ikatan_kerja')->nullable();
        $table->string('jenis_kelamin')->nullable();
        $table->string('program_studi')->nullable();
        $table->string('pendidikan_terakhir')->nullable();
        $table->string('status_aktivitas')->nullable();
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
