<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('publications', function (Blueprint $table) {
            if (!Schema::hasColumn('publications', 'validation_status')) {
                $table->string('validation_status')->default('pending')->after('owner_id');
            }
            if (!Schema::hasColumn('publications', 'validation_note')) {
                $table->text('validation_note')->nullable()->after('validation_status');
            }
            if (!Schema::hasColumn('publications', 'validated_by')) {
                $table->unsignedBigInteger('validated_by')->nullable()->after('validation_note');
            }
        });
    }

    public function down(): void {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn(['validation_status','validation_note','validated_by']);
        });
    }
};
