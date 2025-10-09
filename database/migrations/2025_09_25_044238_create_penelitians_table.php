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
        Schema::create('penelitians', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('dosen_id');
        $table->string('judul');
        $table->string('tahun');
        $table->timestamps();

        $table->foreign('dosen_id')->references('id')->on('dosens')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penelitians');
    }
};
