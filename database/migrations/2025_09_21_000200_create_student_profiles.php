<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_profiles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('nim')->unique();
            $t->string('angkatan')->nullable();
            $t->foreignId('dosen_pembimbing_id')->nullable()->constrained('users');
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('student_profiles');
    }
};
