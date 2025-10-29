<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosens', function (Blueprint $table) {
            // Pastikan kolomnya ditambahkan setelah kolom yang memang ada
            if (!Schema::hasColumn('dosens', 'status')) {
                $table->string('status')->nullable()->after('nidn');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dosens', function (Blueprint $table) {
            if (Schema::hasColumn('dosens', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
