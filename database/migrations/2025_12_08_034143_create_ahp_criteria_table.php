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
        Schema::create('ahp_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();  // contoh: SINTA_SCORE, JUMLAH_HIBAH
            $table->string('nama');            // nama lengkap kriteria
            $table->decimal('bobot', 10, 6)->nullable(); // hasil perhitungan AHP
            $table->boolean('is_benefit')->default(true); // semua benefit criteria
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ahp_criteria');
    }
};
