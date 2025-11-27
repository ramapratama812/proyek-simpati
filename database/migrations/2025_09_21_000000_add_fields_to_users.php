<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->string('username')->unique()->after('name');
            $t->enum('role', ['admin','dosen','mahasiswa'])->default('mahasiswa')->after('password');
            $t->string('pddikti_id')->nullable();
            $t->string('sinta_id')->nullable();
            $t->string('garuda_id')->nullable();
            $t->string('scholar_id')->nullable();
            $t->string('orcid_id')->nullable();
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['username','role','pddikti_id','sinta_id','garuda_id','scholar_id','orcid_id']);
        });
    }
};
