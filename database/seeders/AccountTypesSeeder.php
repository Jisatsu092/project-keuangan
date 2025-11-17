<?php
// database/seeders/AccountTypesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountTypesSeeder extends Seeder
{
    public function run(): void
    {
        $accountTypes = [
            ['code' => '1', 'name' => 'Aset', 'category' => 'aset', 'normal_balance' => 'debit'],
            ['code' => '2', 'name' => 'Hutang', 'category' => 'hutang', 'normal_balance' => 'kredit'],
            ['code' => '3', 'name' => 'Modal', 'category' => 'modal', 'normal_balance' => 'kredit'],
            ['code' => '4', 'name' => 'Pendapatan', 'category' => 'pendapatan', 'normal_balance' => 'kredit'],
            ['code' => '5', 'name' => 'Beban', 'category' => 'beban', 'normal_balance' => 'debit'],
        ];

        foreach ($accountTypes as $type) {
            DB::table('account_types')->insertOrIgnore([
                'code' => $type['code'],
                'name' => $type['name'],
                'category' => $type['category'],
                'normal_balance' => $type['normal_balance'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Account types seeded!');
    }
}