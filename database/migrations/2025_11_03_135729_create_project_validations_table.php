<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration untuk menambahkan sistem validasi pada SIMPATI
 * File: database/migrations/2025_11_03_create_validation_system.php
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat tabel untuk tracking validasi
        Schema::create('project_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                  ->constrained('research_projects')
                  ->cascadeOnDelete();
            $table->foreignId('validated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->enum('status', ['pending', 'revision', 'approved', 'rejected']);
            $table->text('notes')->nullable();
            $table->string('approval_letter')->nullable(); // path file surat persetujuan
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            // Index untuk performa query
            $table->index('project_id');
            $table->index('validated_by');
            $table->index('status');
            $table->index('validated_at');
        });

        // 2. Tambahkan kolom validation_status ke research_projects
        Schema::table('research_projects', function (Blueprint $table) {
            // Validation workflow status
            if (!Schema::hasColumn('research_projects', 'validation_status')) {
                $table->enum('validation_status', [
                    'draft',           // Belum diajukan
                    'submitted',       // Sudah diajukan, menunggu review
                    'under_review',    // Sedang direview admin
                    'revision_needed', // Perlu revisi
                    'approved',        // Disetujui
                    'rejected'         // Ditolak
                ])->default('draft')->after('status');
            }

            // Timestamp untuk tracking
            if (!Schema::hasColumn('research_projects', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('validation_status');
            }

            if (!Schema::hasColumn('research_projects', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('submitted_at');
            }

            if (!Schema::hasColumn('research_projects', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_at');
            }

            // File surat persetujuan dari admin
            if (!Schema::hasColumn('research_projects', 'approval_letter')) {
                $table->string('approval_letter')->nullable()->after('surat_proposal');
            }

            // Nomor dan tanggal surat persetujuan
            if (!Schema::hasColumn('research_projects', 'nomor_surat_persetujuan')) {
                $table->string('nomor_surat_persetujuan')->nullable()->after('approval_letter');
            }

            if (!Schema::hasColumn('research_projects', 'tanggal_surat_persetujuan')) {
                $table->date('tanggal_surat_persetujuan')->nullable()->after('nomor_surat_persetujuan');
            }

            // Admin yang memvalidasi
            if (!Schema::hasColumn('research_projects', 'validated_by')) {
                $table->foreignId('validated_by')
                      ->nullable()
                      ->after('created_by')
                      ->constrained('users')
                      ->nullOnDelete();
            }

            // Index untuk performa
            $table->index('validation_status');
            $table->index('submitted_at');
            $table->index('approved_at');
        });

        // 3. Buat tabel untuk revision history
        Schema::create('project_revision_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                  ->constrained('research_projects')
                  ->cascadeOnDelete();
            $table->foreignId('requested_by')  // Admin yang minta revisi
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('revised_by')    // Dosen yang merevisi
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->json('revision_points');   // Poin-poin yang harus direvisi
            $table->text('revision_notes')->nullable(); // Catatan revisi
            $table->text('response_notes')->nullable(); // Tanggapan dari dosen
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamp('requested_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('project_id');
            $table->index('status');
        });

        // 4. Tambahkan tipe notifikasi untuk user_notifications
        Schema::table('user_notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('user_notifications', 'type')) {
                $table->enum('type', [
                    'info',
                    'success',
                    'warning',
                    'error',
                    'approval',
                    'revision',
                    'submission'
                ])->default('info')->after('content');
            }

            if (!Schema::hasColumn('user_notifications', 'related_project_id')) {
                $table->foreignId('related_project_id')
                      ->nullable()
                      ->after('user_id')
                      ->constrained('research_projects')
                      ->cascadeOnDelete();
            }

            $table->index('type');
            $table->index('related_project_id');
        });

        // 5. Buat tabel untuk template surat persetujuan
        Schema::create('approval_letter_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['penelitian', 'pengabdian']);
            $table->text('template_content'); // HTML template dengan placeholder
            $table->json('variables'); // List variabel yang bisa digunakan
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
        });

        // 6. Buat tabel untuk validation rules/kriteria
        Schema::create('validation_criteria', function (Blueprint $table) {
            $table->id();
            $table->enum('project_type', ['penelitian', 'pengabdian']);
            $table->string('criteria_name');
            $table->text('description');
            $table->integer('weight')->default(1); // Bobot kriteria
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('project_type');
            $table->index('is_active');
        });

        // 7. Buat tabel untuk scoring validasi
        Schema::create('project_validation_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                  ->constrained('research_projects')
                  ->cascadeOnDelete();
            $table->foreignId('criteria_id')
                  ->constrained('validation_criteria')
                  ->cascadeOnDelete();
            $table->foreignId('scored_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->integer('score'); // Nilai 1-10
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'criteria_id', 'scored_by']);
            $table->index('project_id');
            $table->index('criteria_id');
        });

        // 8. Update existing data untuk set default validation_status
        DB::statement("UPDATE research_projects SET validation_status =
            CASE
                WHEN status = 'usulan' THEN 'submitted'
                WHEN status = 'didanai' THEN 'approved'
                WHEN status = 'berjalan' THEN 'approved'
                WHEN status = 'selesai' THEN 'approved'
                ELSE 'draft'
            END
            WHERE validation_status IS NULL OR validation_status = ''");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first
        Schema::table('research_projects', function (Blueprint $table) {
            if (Schema::hasColumn('research_projects', 'validated_by')) {
                $table->dropForeign(['validated_by']);
            }
        });

        Schema::table('user_notifications', function (Blueprint $table) {
            if (Schema::hasColumn('user_notifications', 'related_project_id')) {
                $table->dropForeign(['related_project_id']);
            }
        });

        // Drop tables
        Schema::dropIfExists('project_validation_scores');
        Schema::dropIfExists('validation_criteria');
        Schema::dropIfExists('approval_letter_templates');
        Schema::dropIfExists('project_revision_histories');
        Schema::dropIfExists('project_validations');

        // Remove columns from existing tables
        Schema::table('research_projects', function (Blueprint $table) {
            $columns = [
                'validation_status',
                'submitted_at',
                'approved_at',
                'rejected_at',
                'approval_letter',
                'nomor_surat_persetujuan',
                'tanggal_surat_persetujuan',
                'validated_by'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('research_projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('user_notifications', function (Blueprint $table) {
            if (Schema::hasColumn('user_notifications', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('user_notifications', 'related_project_id')) {
                $table->dropColumn('related_project_id');
            }
        });
    }
};
