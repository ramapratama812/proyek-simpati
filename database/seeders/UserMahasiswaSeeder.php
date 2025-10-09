<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserMahasiswaSeeder extends Seeder
{
    public function up()
{
    Schema::create('mahasiswas', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->string('nim')->unique();
        $table->enum('jenis_kelamin', ['Laki-laki','Perempuan']);
        $table->string('program_studi');
        $table->string('perguruan_tinggi');
        $table->string('status_terakhir')->nullable();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->timestamps();
    });
}
}