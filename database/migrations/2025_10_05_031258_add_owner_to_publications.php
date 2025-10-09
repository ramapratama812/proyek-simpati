<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('publications', function (Blueprint $t) {
      if (!Schema::hasColumn('publications', 'owner_id')) {
        $t->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete()->index();
      }
    });
  }
  public function down(): void {
    Schema::table('publications', function (Blueprint $t) {
      if (Schema::hasColumn('publications', 'owner_id')) {
        $t->dropConstrainedForeignId('owner_id');
      }
    });
  }
};
