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
        Schema::create('riwayat_pendidikans', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('dosen_id'); // relasi ke dosen
        $table->string('perguruan_tinggi');
        $table->string('gelar');
        $table->string('tahun');
        $table->string('jenjang');
        $table->timestamps();

        $table->foreign('dosen_id')->references('id')->on('dosens')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pendidikans');
    }
};
