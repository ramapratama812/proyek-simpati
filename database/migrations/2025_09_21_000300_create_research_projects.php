<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('research_projects', function (Blueprint $t) {
            $t->id();
            $t->enum('jenis', ['penelitian','pengabdian']);
            $t->string('judul');
            $t->string('kategori_kegiatan')->nullable();
            $t->string('bidang_ilmu')->nullable();
            $t->string('skema')->nullable();
            $t->text('abstrak')->nullable();
            $t->date('mulai')->nullable();
            $t->date('selesai')->nullable();
            $t->string('sumber_dana')->nullable();
            $t->decimal('biaya', 16,2)->nullable();
            $t->foreignId('ketua_id')->constrained('users');
            $t->boolean('is_public')->default(true);
            $t->json('external_refs')->nullable();
            $t->timestamps();
        });

        Schema::create('project_members', function (Blueprint $t) {
            $t->id();
            $t->foreignId('project_id')->constrained('research_projects')->cascadeOnDelete();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->enum('peran',['ketua','anggota'])->default('anggota');
            $t->timestamps();
        });

        Schema::create('project_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('project_id')->constrained('research_projects')->cascadeOnDelete();
            $t->string('path');
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('project_images');
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('research_projects');
    }
};
