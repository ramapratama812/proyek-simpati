<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('research_projects', function (Blueprint $table) {
            // status proses validasi oleh admin
            $table->enum('validation_status', ['draft','pending','approved','revision_requested','rejected'])
                  ->default('draft')
                  ->after('status');

            // catatan dari admin saat validasi
            $table->text('validation_note')->nullable()->after('validation_status');

            // siapa admin yang memvalidasi
            $table->foreignId('validated_by')
                  ->nullable()
                  ->after('validation_note')
                  ->constrained('users')
                  ->nullOnDelete();

            // kapan divalidasi
            $table->timestamp('validated_at')->nullable()->after('validated_by');

            // surat persetujuan P3M (PDF yang diupload admin)
            $table->string('surat_persetujuan')->nullable()->after('surat_proposal');
        });
    }

    public function down(): void
    {
        Schema::table('research_projects', function (Blueprint $table) {
            if (Schema::hasColumn('research_projects','validated_by')) {
                $table->dropForeign(['validated_by']);
            }

            $dropCols = [
                'validation_status',
                'validation_note',
                'validated_by',
                'validated_at',
                'surat_persetujuan',
            ];

            foreach ($dropCols as $col) {
                if (Schema::hasColumn('research_projects', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
