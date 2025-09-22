<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lecturer_profiles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('nidn')->nullable();
            $t->string('nip')->nullable();
            $t->string('bidang_keahlian')->nullable();
            $t->text('bio')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('lecturer_profiles');
    }
};
