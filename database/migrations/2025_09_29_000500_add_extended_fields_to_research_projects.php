<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('research_projects')) {
            Schema::table('research_projects', function (Blueprint $t) {
                if (!Schema::hasColumn('research_projects','ketua_id')) {
                    $t->foreignId('ketua_id')->nullable()->constrained('users')->nullOnDelete()->after('biaya');
                }
                if (!Schema::hasColumn('research_projects','tahun_usulan')) {
                    $t->year('tahun_usulan')->nullable()->after('ketua_id');
                }
                if (!Schema::hasColumn('research_projects','tahun_pelaksanaan')) {
                    $t->year('tahun_pelaksanaan')->nullable()->after('tahun_usulan');
                }
                if (!Schema::hasColumn('research_projects','status')) {
                    $t->enum('status',['usulan','didanai','berjalan','selesai'])->default('usulan')->after('tahun_pelaksanaan');
                }
                if (!Schema::hasColumn('research_projects','tkt')) {
                    $t->unsignedTinyInteger('tkt')->nullable()->after('status');
                }
                if (!Schema::hasColumn('research_projects','mitra_nama')) {
                    $t->string('mitra_nama')->nullable()->after('tkt');
                }
                if (!Schema::hasColumn('research_projects','lokasi')) {
                    $t->string('lokasi')->nullable()->after('mitra_nama');
                }
                if (!Schema::hasColumn('research_projects','nomor_kontrak')) {
                    $t->string('nomor_kontrak')->nullable()->after('lokasi');
                }
                if (!Schema::hasColumn('research_projects','tanggal_kontrak')) {
                    $t->date('tanggal_kontrak')->nullable()->after('nomor_kontrak');
                }
                if (!Schema::hasColumn('research_projects','target_luaran')) {
                    $t->json('target_luaran')->nullable()->after('lama_kegiatan_bulan');
                }
                if (!Schema::hasColumn('research_projects','keywords')) {
                    $t->string('keywords')->nullable()->after('target_luaran');
                }
                if (!Schema::hasColumn('research_projects','tautan')) {
                    $t->string('tautan')->nullable()->after('keywords');
                }
            });
        }

        if (!Schema::hasTable('project_members')) {
            Schema::create('project_members', function (Blueprint $t) {
                $t->id();
                $t->foreignId('project_id')->constrained('research_projects')->cascadeOnDelete();
                $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $t->enum('peran',['ketua','anggota'])->default('anggota');
                $t->timestamps();
                $t->unique(['project_id','user_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('research_projects')) {
            Schema::table('research_projects', function (Blueprint $t) {
                foreach (['tautan','keywords','target_luaran','lama_kegiatan_bulan','tanggal_kontrak','nomor_kontrak','lokasi','mitra_nama','tkt','status','tahun_pelaksanaan','tahun_usulan','ketua_id'] as $col) {
                    if (Schema::hasColumn('research_projects',$col)) {
                        $t->dropColumn($col);
                    }
                }
            });
        }
        if (Schema::hasTable('project_members')) {
            Schema::drop('project_members');
        }
    }
};
