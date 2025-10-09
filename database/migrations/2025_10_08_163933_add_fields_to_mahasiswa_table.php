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
    Schema::table('mahasiswa', function (Blueprint $table) {
        $table->string('jenjang_pendidikan')->nullable();
        $table->integer('semester')->nullable();
        $table->string('status_aktivitas')->nullable();
    });
}

public function down(): void
{
    Schema::table('mahasiswa', function (Blueprint $table) {
        $table->dropColumn(['jenjang_pendidikan', 'semester', 'status_aktivitas']);
    });
}

};
