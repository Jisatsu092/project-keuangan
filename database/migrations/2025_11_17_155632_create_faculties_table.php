<?php
// database/migrations/2024_01_01_000003_create_faculties_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->char('code', 1)->unique()->comment('1-9, tidak ada code 0');
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
        });

        // Seed data - Hanya 1-9, tidak ada code 0
        // Code 0 di digit_3 = tidak ada fakultas (pusat)
        DB::table('faculties')->insert([
            ['code' => '1', 'name' => 'Fakultas Syariah', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '2', 'name' => 'Fakultas Teknik', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '3', 'name' => 'Fakultas Ekonomi', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '4', 'name' => 'Fakultas Tarbiyah', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '5', 'name' => 'Fakultas Hukum', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};