<?php
// database/seeders/FacultiesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacultiesSeeder extends Seeder
{
    public function run(): void
    {
        $faculties = [
            ['code' => '1', 'name' => 'Fakultas Syariah'],
            ['code' => '2', 'name' => 'Fakultas Teknik'],
            ['code' => '3', 'name' => 'Fakultas Ekonomi'],
            ['code' => '4', 'name' => 'Fakultas Tarbiyah'],
            ['code' => '5', 'name' => 'Fakultas Hukum'],
        ];

        foreach ($faculties as $faculty) {
            DB::table('faculties')->insertOrIgnore([
                'code' => $faculty['code'],
                'name' => $faculty['name'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Faculties seeded!');
    }
}