<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('user_notifications')) {
            Schema::create('user_notifications', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $t->foreignId('project_id')->nullable()->constrained('research_projects')->cascadeOnDelete();
                $t->string('type',50)->default('info');
                $t->string('message',500);
                $t->boolean('is_shown')->default(false);
                $t->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
