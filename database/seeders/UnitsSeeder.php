<?php
// database/seeders/UnitsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil faculty IDs
        $syariah = DB::table('faculties')->where('code', '1')->value('id');
        $teknik = DB::table('faculties')->where('code', '2')->value('id');
        $ekonomi = DB::table('faculties')->where('code', '3')->value('id');

        $units = [
            // UNIT PUSAT
            ['faculty_id' => null, 'code' => '1', 'name' => 'Rektorat', 'type' => 'unit_pusat'],
            ['faculty_id' => null, 'code' => '2', 'name' => 'Keuangan', 'type' => 'unit_pusat'],
            ['faculty_id' => null, 'code' => '3', 'name' => 'PTI', 'type' => 'unit_pusat'],
            ['faculty_id' => null, 'code' => '4', 'name' => 'SDM', 'type' => 'unit_pusat'],
            ['faculty_id' => null, 'code' => '5', 'name' => 'Akademik', 'type' => 'unit_pusat'],
            
            // PRODI FAKULTAS SYARIAH
            ['faculty_id' => $syariah, 'code' => '1', 'name' => 'Ekonomi Syariah', 'type' => 'prodi'],
            ['faculty_id' => $syariah, 'code' => '2', 'name' => 'Hukum Keluarga', 'type' => 'prodi'],
            ['faculty_id' => $syariah, 'code' => '3', 'name' => 'Perbankan Syariah', 'type' => 'prodi'],
            
            // PRODI FAKULTAS TEKNIK
            ['faculty_id' => $teknik, 'code' => '1', 'name' => 'Teknik Informatika', 'type' => 'prodi'],
            ['faculty_id' => $teknik, 'code' => '2', 'name' => 'Sistem Informasi', 'type' => 'prodi'],
            ['faculty_id' => $teknik, 'code' => '3', 'name' => 'Teknik Sipil', 'type' => 'prodi'],
            ['faculty_id' => $teknik, 'code' => '4', 'name' => 'Teknik Elektro', 'type' => 'prodi'],
            
            // PRODI FAKULTAS EKONOMI
            ['faculty_id' => $ekonomi, 'code' => '1', 'name' => 'Manajemen', 'type' => 'prodi'],
            ['faculty_id' => $ekonomi, 'code' => '2', 'name' => 'Akuntansi', 'type' => 'prodi'],
            ['faculty_id' => $ekonomi, 'code' => '3', 'name' => 'Ekonomi Pembangunan', 'type' => 'prodi'],
        ];

        foreach ($units as $unit) {
            DB::table('units')->insertOrIgnore([
                'faculty_id' => $unit['faculty_id'],
                'code' => $unit['code'],
                'name' => $unit['name'],
                'type' => $unit['type'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Units seeded!');
    }
}