<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_requests', function (Blueprint $table) {
            // username SIMPATI yang diusulkan
            $table->string('username')->nullable()->after('role');

            // NIM atau NIDN/NIP (sesuai role)
            $table->string('identity')->nullable()->after('username');

            // password yang diusulkan (disimpan sudah dalam bentuk hash)
            $table->string('password')->nullable()->after('identity');

            // kalau permohonan datang dari Google
            $table->string('google_id')->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('registration_requests', function (Blueprint $table) {
            $table->dropColumn(['username', 'identity', 'password', 'google_id']);
        });
    }
};
