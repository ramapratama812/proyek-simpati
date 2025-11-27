<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('registration_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email'); // email kontak user (bisa gmail, dll)
            $table->string('role');  // dosen / mahasiswa
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('note')->nullable(); // catatan admin ketika approve/reject
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_requests');
    }
};
