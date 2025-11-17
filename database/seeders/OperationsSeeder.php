<?php
// database/seeders/OperationsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperationsSeeder extends Seeder
{
    public function run(): void
    {
        $operations = [
            ['code' => '1', 'name' => 'Operasional', 'description' => 'Kegiatan rutin operasional'],
            ['code' => '2', 'name' => 'Program', 'description' => 'Program khusus/project'],
        ];

        foreach ($operations as $operation) {
            DB::table('operations')->insertOrIgnore([
                'code' => $operation['code'],
                'name' => $operation['name'],
                'description' => $operation['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Operations seeded!');
    }
}