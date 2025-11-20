<?php
// database/migrations/2025_11_17_155621_create_operations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->char('account_type_code', 1)->comment('1-5: Foreign key ke account_types.code');
            $table->char('code', 1)->nullable()->comment('1-9: Sub-category code (NULL untuk header)');
            $table->string('name', 100);
            $table->string('parent_category', 50)->nullable()->comment('Parent grouping (operasional/program/hibah)');
            $table->boolean('is_header')->default(false)->comment('true = header grouping, false = actual code');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0)->comment('Urutan tampilan');
            $table->timestamps();
            
            // Indexes
            $table->index('account_type_code');
            $table->index('code');
            $table->index('parent_category');
            $table->index('is_header');
            
            // FIXED: Unique constraint dengan parent_category
            // Ini allow code bentrok selama parent_category beda
            $table->unique(['account_type_code', 'code', 'parent_category'], 'operations_unique_constraint');
        });

        // Seed Sample Data
        DB::table('operations')->insert([
            // =====================================
            // 1. ASET (No header, direct codes)
            // =====================================
            ['account_type_code' => '1', 'code' => '1', 'name' => 'Dari Hibah', 'parent_category' => null, 'is_header' => false, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['account_type_code' => '1', 'code' => '2', 'name' => 'Dari Donatur', 'parent_category' => null, 'is_header' => false, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['account_type_code' => '1', 'code' => '3', 'name' => 'Pembelian', 'parent_category' => null, 'is_header' => false, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],

            // =====================================
            // 2. LIABILITAS
            // =====================================
            ['account_type_code' => '2', 'code' => '1', 'name' => 'Hutang Jangka Pendek', 'parent_category' => null, 'is_header' => false, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['account_type_code' => '2', 'code' => '2', 'name' => 'Hutang Jangka Panjang', 'parent_category' => null, 'is_header' => false, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],

            // =====================================
            // 3. EKUITAS
            // =====================================
            ['account_type_code' => '3', 'code' => '1', 'name' => 'Modal Awal', 'parent_category' => null, 'is_header' => false, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['account_type_code' => '3', 'code' => '2', 'name' => 'Modal Donasi', 'parent_category' => null, 'is_header' => false, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],

            // =====================================
            // 4. PENDAPATAN
            // =====================================
            ['account_type_code' => '4', 'code' => '1', 'name' => 'UKT Mahasiswa', 'parent_category' => null, 'is_header' => false, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['account_type_code' => '4', 'code' => '2', 'name' => 'Donasi', 'parent_category' => null, 'is_header' => false, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['account_type_code' => '4', 'code' => '3', 'name' => 'Jasa Layanan', 'parent_category' => null, 'is_header' => false, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],

            // =====================================
            // 5. BEBAN - HEADERS
            // =====================================
            ['account_type_code' => '5', 'code' => null, 'name' => '🔵 OPERASIONAL', 'parent_category' => 'operasional', 'is_header' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['account_type_code' => '5', 'code' => null, 'name' => '🟣 PROGRAM', 'parent_category' => 'program', 'is_header' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],

            // =====================================
            // 5. BEBAN - OPERASIONAL (Details)
            // parent_category = 'operasional'
            // =====================================
            ['account_type_code' => '5', 'code' => '1', 'name' => 'Belanja Pegawai', 'parent_category' => 'operasional', 'is_header' => false, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['account_type_code' => '5', 'code' => '2', 'name' => 'Belanja Barang', 'parent_category' => 'operasional', 'is_header' => false, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['account_type_code' => '5', 'code' => '3', 'name' => 'Belanja Modal', 'parent_category' => 'operasional', 'is_header' => false, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],

            // =====================================
            // 5. BEBAN - PROGRAM (Details)
            // parent_category = 'program'
            // CODE BENTROK ALLOWED! (1, 2, 3 sama dengan operasional)
            // =====================================
            ['account_type_code' => '5', 'code' => '1', 'name' => 'Program KKN', 'parent_category' => 'program', 'is_header' => false, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['account_type_code' => '5', 'code' => '2', 'name' => 'Program Pengabdian', 'parent_category' => 'program', 'is_header' => false, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['account_type_code' => '5', 'code' => '3', 'name' => 'Program Penelitian', 'parent_category' => 'program', 'is_header' => false, 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};