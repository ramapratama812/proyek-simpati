<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('research_projects') && !Schema::hasColumn('research_projects','created_by')) {
            Schema::table('research_projects', function (Blueprint $t) {
                $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('ketua_id');
            });
            DB::statement('UPDATE research_projects SET created_by = ketua_id WHERE created_by IS NULL');
        }
    }
    public function down(): void
    {
        if (Schema::hasTable('research_projects') && Schema::hasColumn('research_projects','created_by')) {
            Schema::table('research_projects', function (Blueprint $t) {
                $t->dropConstrainedForeignId('created_by');
            });
        }
    }
};
