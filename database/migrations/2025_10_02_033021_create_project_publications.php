<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('project_publications')) {
            Schema::create('project_publications', function (Blueprint $t) {
                $t->id();
                $t->foreignId('project_id')->constrained('research_projects')->cascadeOnDelete();
                $t->foreignId('publication_id')->constrained('publications')->cascadeOnDelete();
                $t->timestamps();
                $t->unique(['project_id','publication_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_publications');
    }
};
