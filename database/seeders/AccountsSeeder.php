<?php
// database/seeders/AccountsSeeder.php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // ROOT ACCOUNTS
            ['code' => '1000000', 'name' => 'ASET', 'normal_balance' => 'debit', 'is_header' => true, 'level' => 1, 'digit_1' => '1'],
            ['code' => '2000000', 'name' => 'HUTANG', 'normal_balance' => 'kredit', 'is_header' => true, 'level' => 1, 'digit_1' => '2'],
            ['code' => '3000000', 'name' => 'MODAL', 'normal_balance' => 'kredit', 'is_header' => true, 'level' => 1, 'digit_1' => '3'],
            ['code' => '4000000', 'name' => 'PENDAPATAN', 'normal_balance' => 'kredit', 'is_header' => true, 'level' => 1, 'digit_1' => '4'],
            ['code' => '5000000', 'name' => 'BEBAN', 'normal_balance' => 'debit', 'is_header' => true, 'level' => 1, 'digit_1' => '5'],
            
            // ASET - OPERASIONAL
            ['code' => '1100000', 'name' => 'Aset Lancar - Operasional', 'normal_balance' => 'debit', 'is_header' => true, 'level' => 2, 'parent_code' => '1000000', 'digit_1' => '1', 'digit_2' => '1'],
            
            // UNIT PUSAT - KEUANGAN
            ['code' => '1100200', 'name' => 'Aset Lancar Ops - Keuangan', 'normal_balance' => 'debit', 'is_header' => true, 'level' => 3, 'parent_code' => '1100000', 'digit_1' => '1', 'digit_2' => '1', 'digit_3' => '0', 'digit_4' => '2'],
            ['code' => '1100210', 'name' => 'Kas & Bank - Keuangan', 'normal_balance' => 'debit', 'is_header' => true, 'level' => 4, 'parent_code' => '1100200', 'digit_1' => '1', 'digit_2' => '1', 'digit_3' => '0', 'digit_4' => '2', 'digit_5' => '1'],
            ['code' => '1100211', 'name' => 'Kas Kecil Keuangan', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 5, 'parent_code' => '1100210', 'digit_1' => '1', 'digit_2' => '1', 'digit_3' => '0', 'digit_4' => '2', 'digit_5' => '1', 'digit_6' => '0', 'digit_7' => '1'],
            ['code' => '1100212', 'name' => 'Bank BRI Keuangan', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 5, 'parent_code' => '1100210', 'digit_1' => '1', 'digit_2' => '1', 'digit_3' => '0', 'digit_4' => '2', 'digit_5' => '1', 'digit_6' => '0', 'digit_7' => '2'],
            
            // BEBAN - OPERASIONAL
            ['code' => '5100000', 'name' => 'Beban Operasional', 'normal_balance' => 'debit', 'is_header' => true, 'level' => 2, 'parent_code' => '5000000', 'digit_1' => '5', 'digit_2' => '1'],
            
            // UNIT PUSAT - KEUANGAN
            ['code' => '5100200', 'name' => 'Beban Ops - Keuangan', 'normal_balance' => 'debit', 'is_header' => true, 'level' => 3, 'parent_code' => '5100000', 'digit_1' => '5', 'digit_2' => '1', 'digit_3' => '0', 'digit_4' => '2'],
            
            // Gaji & Tunjangan
            ['code' => '5100210', 'name' => 'Beban Ops - Keuangan - Gaji', 'normal_balance' => 'debit', 'is_header' => true, 'level' => 4, 'parent_code' => '5100200', 'digit_1' => '5', 'digit_2' => '1', 'digit_3' => '0', 'digit_4' => '2', 'digit_5' => '1'],
            ['code' => '5100211', 'name' => 'Gaji Pokok Staff Keuangan', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 5, 'parent_code' => '5100210', 'digit_1' => '5', 'digit_2' => '1', 'digit_3' => '0', 'digit_4' => '2', 'digit_5' => '1', 'digit_6' => '0', 'digit_7' => '1'],
            ['code' => '5100212', 'name' => 'Tunjangan Staff Keuangan', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 5, 'parent_code' => '5100210', 'digit_1' => '5', 'digit_2' => '1', 'digit_3' => '0', 'digit_4' => '2', 'digit_5' => '1', 'digit_6' => '0', 'digit_7' => '2'],
            
            // Transport
            ['code' => '5100240', 'name' => 'Beban Ops - Keuangan - Transport', 'normal_balance' => 'debit', 'is_header' => true, 'level' => 4, 'parent_code' => '5100200', 'digit_1' => '5', 'digit_2' => '1', 'digit_3' => '0', 'digit_4' => '2', 'digit_5' => '4'],
            ['code' => '5100241', 'name' => 'BBM Kendaraan Dinas Keuangan', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 5, 'parent_code' => '5100240', 'digit_1' => '5', 'digit_2' => '1', 'digit_3' => '0', 'digit_4' => '2', 'digit_5' => '4', 'digit_6' => '0', 'digit_7' => '1'],
            ['code' => '5100242', 'name' => 'Parkir & Tol Keuangan', 'normal_balance' => 'debit', 'is_header' => false, 'level' => 5, 'parent_code' => '5100240', 'digit_1' => '5', 'digit_2' => '1', 'digit_3' => '0', 'digit_4' => '2', 'digit_5' => '4', 'digit_6' => '0', 'digit_7' => '2'],
        ];

        foreach ($accounts as $account) {
            // Gunakan updateOrInsert untuk menghindari duplicate dan bisa update jika ada perubahan
            DB::table('accounts')->updateOrInsert(
                ['code' => $account['code']],
                array_merge($account, [
                    'can_transaction' => !$account['is_header'],
                    'is_active' => true,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ Accounts seeded successfully!');
        $this->command->info('📊 Total accounts: ' . count($accounts));
    }
}