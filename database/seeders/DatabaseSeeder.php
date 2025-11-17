<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class, // Pastikan user dulu
            AccountTypesSeeder::class,
            OperationsSeeder::class,
            FacultiesSeeder::class,
            UnitsSeeder::class,
            ActivityTypesSeeder::class,
            AccountsSeeder::class,
            ReportMappingsSeeder::class,
        ]);
    }
}