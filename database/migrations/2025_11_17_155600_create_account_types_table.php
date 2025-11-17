<?php
// database/migrations/2024_01_01_000001_create_account_types_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_types', function (Blueprint $table) {
            $table->id();
            $table->char('code', 1)->unique()->comment('1-9');
            $table->string('name', 100);
            $table->enum('category', ['aset', 'hutang', 'modal', 'pendapatan', 'beban']);
            $table->enum('normal_balance', ['debit', 'kredit']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('category');
        });

        // Seed data
        DB::table('account_types')->insert([
            ['code' => '1', 'name' => 'Aset', 'category' => 'aset', 'normal_balance' => 'debit', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '2', 'name' => 'Hutang', 'category' => 'hutang', 'normal_balance' => 'kredit', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '3', 'name' => 'Modal', 'category' => 'modal', 'normal_balance' => 'kredit', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '4', 'name' => 'Pendapatan', 'category' => 'pendapatan', 'normal_balance' => 'kredit', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '5', 'name' => 'Beban', 'category' => 'beban', 'normal_balance' => 'debit', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('account_types');
    }
};