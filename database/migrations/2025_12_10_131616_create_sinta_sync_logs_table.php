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
        Schema::create('sinta_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->unsignedBigInteger('triggered_by')->nullable();
            $table->string('source', 20); // 'web' / 'console'
            $table->unsignedInteger('total_metrics')->default(0);
            $table->string('status', 20); // 'success' / 'failed'
            $table->text('message')->nullable();
            $table->timestamps();

            $table->foreign('triggered_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinta_sync_logs');
    }
};
