<?php
// database/migrations/2024_01_01_000005_create_activity_types_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_types', function (Blueprint $table) {
            $table->id();
            $table->char('code', 1)->unique()->comment('0-9');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
        });

        DB::table('activity_types')->insert([
            ['code' => '0', 'name' => 'Umum', 'description' => 'Kegiatan umum/tidak terkategori', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '1', 'name' => 'Gaji & Tunjangan', 'description' => 'Gaji, honorarium, tunjangan', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '2', 'name' => 'Operasional Kantor', 'description' => 'ATK, supplies, admin', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '3', 'name' => 'Pemeliharaan', 'description' => 'Perawatan aset, gedung, kendaraan', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '4', 'name' => 'Transport', 'description' => 'BBM, tol, parkir, perjalanan dinas', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '5', 'name' => 'Konsumsi', 'description' => 'Makan, snack, catering', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '6', 'name' => 'Utilitas', 'description' => 'Listrik, air, internet, telepon', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '7', 'name' => 'Pelatihan & Pengembangan', 'description' => 'Diklat, seminar, workshop', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '8', 'name' => 'Administrasi', 'description' => 'Surat menyurat, legalisir, perizinan', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '9', 'name' => 'Lain-lain', 'description' => 'Pengeluaran lainnya', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_types');
    }
};