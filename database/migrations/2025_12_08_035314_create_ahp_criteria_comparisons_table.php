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
        Schema::create('ahp_criteria_comparisons', function (Blueprint $table) {
            $table->id();

            // baris (i) dan kolom (j) pada matriks perbandingan
            $table->foreignId('row_criteria_id')
                ->constrained('ahp_criteria')
                ->cascadeOnDelete();

            $table->foreignId('col_criteria_id')
                ->constrained('ahp_criteria')
                ->cascadeOnDelete();

            // nilai perbandingan (skala 1–9 ala AHP, bisa pecahan)
            $table->decimal('value', 10, 4);

            $table->timestamps();

            // kombinasi unik: satu pasangan kriteria cuma boleh satu record
            $table->unique(['row_criteria_id', 'col_criteria_id'], 'ahp_criteria_pair_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ahp_criteria_comparisons');
    }
};
