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
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nim')->unique();
            $table->string('email')->unique();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('program_studi')->nullable();
            $table->string('perguruan_tinggi')->nullable();
            $table->string('status_terakhir')->nullable();
            $table->string('foto')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('jenjang_pendidikan')->nullable();
            $table->integer('semester')->nullable();
            $table->string('status_aktivitas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
