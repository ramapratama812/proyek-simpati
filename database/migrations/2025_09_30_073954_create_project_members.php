<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('project_members')) {
            Schema::create('project_members', function (Blueprint $t) {
                $t->id();
                $t->foreignId('project_id')->constrained('research_projects')->cascadeOnDelete();
                $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $t->enum('peran', ['ketua','anggota'])->default('anggota');
                $t->timestamps();

                $t->unique(['project_id','user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};
