<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('dosens')->insert([
            [
                'nama' => 'Nindy Permatasari',
                'nidn' => '0123456789',
                'status' => 'Aktif',
            ],
            [
                'nama' => 'WINDA APRIANTI',
                'nidn' => '9876543210',
                'status' => 'Aktif',
            ],
            [
             
                'nama' => 'JAKA PERMADI',
                'nidn' => '9876543210',
                'status' => 'Aktif',
            ],
            [
                
                'nama' => 'NINA MIA ARISTI',
                'nidn' => '9876543210',
                'status' => 'Aktif',
            ],
        ]);
    }
}
