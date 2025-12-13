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
        Schema::create('dosen_performance_metrics', function (Blueprint $table) {
            $table->id();

            // SESUAIKAN nama tabel referensi ini kalau di sistemmu bukan 'dosen'
            $table->foreignId('user_id')->constrained('dosens');

            $table->year('tahun');

            // ====== Kriteria AHP ======
            $table->decimal('sinta_score', 10, 3)->nullable();
            $table->decimal('sinta_score_3yr', 10, 3)->nullable();

            $table->unsignedInteger('jumlah_hibah')->default(0);
            $table->unsignedInteger('publikasi_scholar_1th')->default(0);
            $table->unsignedInteger('jumlah_penelitian')->default(0);
            $table->unsignedInteger('jumlah_p3m')->default(0);
            $table->unsignedInteger('jumlah_publikasi')->default(0);

            // ====== Hasil perhitungan ======
            $table->decimal('skor_akhir', 10, 6)->nullable();
            $table->unsignedInteger('peringkat')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'tahun'], 'dosen_tahun_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen_performance_metrics');
    }
};
