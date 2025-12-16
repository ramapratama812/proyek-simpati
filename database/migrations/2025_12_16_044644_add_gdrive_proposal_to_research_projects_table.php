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
        Schema::table('research_projects', function (Blueprint $table) {
            $table->string('gdrive_proposal_id')->nullable();
            $table->string('gdrive_proposal_name')->nullable();
            $table->string('gdrive_proposal_mime')->nullable();
            $table->unsignedBigInteger('gdrive_proposal_size')->nullable();
            $table->text('gdrive_proposal_view_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research_projects', function (Blueprint $table) {
            $table->dropColumn([
                'gdrive_proposal_id',
                'gdrive_proposal_name',
                'gdrive_proposal_mime',
                'gdrive_proposal_size',
                'gdrive_proposal_view_link'
            ]);
        });
    }
};
