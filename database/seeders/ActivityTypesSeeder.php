<?php
// database/seeders/ActivityTypesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTypesSeeder extends Seeder
{
    public function run(): void
    {
        $activityTypes = [
            ['code' => '0', 'name' => 'Umum', 'description' => 'Kegiatan umum/tidak terkategori'],
            ['code' => '1', 'name' => 'Gaji & Tunjangan', 'description' => 'Gaji, honorarium, tunjangan'],
            ['code' => '2', 'name' => 'Operasional Kantor', 'description' => 'ATK, supplies, admin'],
            ['code' => '3', 'name' => 'Pemeliharaan', 'description' => 'Perawatan aset, gedung, kendaraan'],
            ['code' => '4', 'name' => 'Transport', 'description' => 'BBM, tol, parkir, perjalanan dinas'],
            ['code' => '5', 'name' => 'Konsumsi', 'description' => 'Makan, snack, catering'],
            ['code' => '6', 'name' => 'Utilitas', 'description' => 'Listrik, air, internet, telepon'],
            ['code' => '7', 'name' => 'Pelatihan & Pengembangan', 'description' => 'Diklat, seminar, workshop'],
            ['code' => '8', 'name' => 'Administrasi', 'description' => 'Surat menyurat, legalisir, perizinan'],
            ['code' => '9', 'name' => 'Lain-lain', 'description' => 'Pengeluaran lainnya'],
        ];

        foreach ($activityTypes as $activity) {
            DB::table('activity_types')->insertOrIgnore([
                'code' => $activity['code'],
                'name' => $activity['name'],
                'description' => $activity['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Activity types seeded!');
    }
}