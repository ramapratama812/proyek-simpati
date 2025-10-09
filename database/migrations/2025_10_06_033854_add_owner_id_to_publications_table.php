<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('publications') && !Schema::hasColumn('publications', 'owner_id')) {
            Schema::table('publications', function (Blueprint $table) {
                $table->foreignId('owner_id')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('cascade')
                    ->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('publications') && Schema::hasColumn('publications', 'owner_id')) {
            Schema::table('publications', function (Blueprint $table) {
                $table->dropConstrainedForeignId('owner_id');
            });
        }
    }
};
