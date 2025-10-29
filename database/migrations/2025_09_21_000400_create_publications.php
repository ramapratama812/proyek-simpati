<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('publications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained('users');
            $t->foreignId('project_id')->nullable()->constrained('research_projects')->nullOnDelete();
            $t->string('judul');
            $t->string('jenis')->nullable();
            $t->string('jurnal')->nullable();
            $t->integer('tahun')->nullable();
            $t->string('doi')->nullable();
            $t->string('issn')->nullable();
            $t->json('penulis')->nullable();
            $t->json('sumber')->nullable();
            $t->json('tautan')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('publications');
    }
};
