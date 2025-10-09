<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            // Tambahkan kolom hanya jika belum ada
            if (!Schema::hasColumn('users', 'username')) {
                $t->string('username')->unique()->after('name');
            }

            if (!Schema::hasColumn('users', 'role')) {
                $t->enum('role', ['admin', 'dosen', 'mahasiswa'])
                  ->default('mahasiswa')
                  ->after('password');
            }

            foreach (['pddikti_id', 'sinta_id', 'garuda_id', 'scholar_id', 'orcid_id'] as $col) {
                if (!Schema::hasColumn('users', $col)) {
                    $t->string($col)->nullable();
                }
            }
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $columns = ['username', 'role', 'pddikti_id', 'sinta_id', 'garuda_id', 'scholar_id', 'orcid_id'];

            foreach ($columns as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
