<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Pastikan tabel 'dosens' sudah ada dan belum punya kolom 'email'
        if (Schema::hasTable('dosens') && !Schema::hasColumn('dosens', 'email')) {
            Schema::table('dosens', function (Blueprint $table) {
                // Tambahkan kolom email unik (bisa nullable dulu agar tidak bentrok saat migrasi awal)
                $table->string('email')->unique()->nullable()->after('nama');
            });
        }
    }

    public function down(): void
    {
        // ✅ Hapus kolom email hanya jika benar-benar ada
        if (Schema::hasTable('dosens') && Schema::hasColumn('dosens', 'email')) {
            Schema::table('dosens', function (Blueprint $table) {
                $table->dropUnique(['email']); // hapus constraint unique dulu
                $table->dropColumn('email');
            });
        }
    }
};
