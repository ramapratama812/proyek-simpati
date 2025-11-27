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
        Schema::table('publications', function (Blueprint $table) {
            $table->string('volume')->nullable()->after('tahun');
            $table->string('nomor')->nullable()->after('volume');
            $table->text('abstrak')->nullable()->after('nomor');
            $table->integer('jumlah_halaman')->nullable()->after('abstrak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn(['volume', 'nomor', 'abstrak', 'jumlah_halaman']);
        });
    }
};
