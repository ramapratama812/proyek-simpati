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
         Schema::table('dosens', function (Blueprint $table) {
        $table->string('status_aktivitas')->default('Tidak Aktif')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('dosens', function (Blueprint $table) {
        $table->string('status_aktivitas')->nullable(false)->change();
        });
    }
};
