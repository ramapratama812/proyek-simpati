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
        Schema::table('publications', function (Blueprint $table) {
            $table->string('gdrive_pdf_id')->nullable();
            $table->string('gdrive_pdf_name')->nullable();
            $table->string('gdrive_pdf_mime')->nullable();
            $table->unsignedBigInteger('gdrive_pdf_size')->nullable();
            $table->text('gdrive_pdf_view_link')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn([
                'gdrive_pdf_id',
                'gdrive_pdf_name',
                'gdrive_pdf_mime',
                'gdrive_pdf_size',
                'gdrive_pdf_view_link'
            ]);
        });
    }
};
