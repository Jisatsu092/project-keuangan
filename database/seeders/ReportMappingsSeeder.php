<?php
// database/seeders/ReportMappingsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportMappingsSeeder extends Seeder
{
    public function run(): void
    {
        $mappings = [
            ['report_type' => 'lpk', 'section' => 'ASET', 'subsection' => 'Aset Lancar', 'account_pattern' => '11%', 'display_order' => 10],
            ['report_type' => 'lpk', 'section' => 'ASET', 'subsection' => 'Aset Tidak Lancar', 'account_pattern' => '12%', 'display_order' => 20],
            ['report_type' => 'lpk', 'section' => 'LIABILITAS', 'subsection' => 'Liabilitas Jangka Pendek', 'account_pattern' => '21%', 'display_order' => 30],
            ['report_type' => 'lpk', 'section' => 'LIABILITAS', 'subsection' => 'Liabilitas Jangka Panjang', 'account_pattern' => '22%', 'display_order' => 40],
            ['report_type' => 'lpk', 'section' => 'EKUITAS', 'subsection' => 'Modal', 'account_pattern' => '3%', 'display_order' => 50],
        ];

        foreach ($mappings as $mapping) {
            DB::table('report_mappings')->insertOrIgnore([
                'report_type' => $mapping['report_type'],
                'section' => $mapping['section'],
                'subsection' => $mapping['subsection'],
                'account_pattern' => $mapping['account_pattern'],
                'display_order' => $mapping['display_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Report mappings seeded!');
    }
}