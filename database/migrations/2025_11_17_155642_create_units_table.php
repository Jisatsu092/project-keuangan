<?php
// database/migrations/2024_01_01_000004_create_units_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->nullable()->constrained()->onDelete('cascade');
            $table->char('code', 1)->comment('0-9');
            $table->string('name', 100);
            $table->enum('type', ['prodi', 'unit_pusat'])->comment('prodi atau unit setara prodi');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Unique per faculty (unit pusat bisa punya code sama dengan prodi)
            $table->unique(['faculty_id', 'code']);
            $table->index('code');
            $table->index('type');
        });

        // Ambil faculty IDs
        $syariah = DB::table('faculties')->where('code', '1')->value('id');
        $teknik = DB::table('faculties')->where('code', '2')->value('id');
        $ekonomi = DB::table('faculties')->where('code', '3')->value('id');

        // DB::table('units')->insert([
        //     // ============================================
        //     // UNIT PUSAT (faculty_id = null)
        //     // Ini untuk akun dengan digit_3 = 0
        //     // ============================================
        //     ['faculty_id' => null, 'code' => '1', 'name' => 'Rektorat', 'type' => 'unit_pusat', 'created_at' => now(), 'updated_at' => now()],
        //     ['faculty_id' => null, 'code' => '2', 'name' => 'Keuangan', 'type' => 'unit_pusat', 'created_at' => now(), 'updated_at' => now()],
        //     ['faculty_id' => null, 'code' => '3', 'name' => 'PTI', 'type' => 'unit_pusat', 'created_at' => now(), 'updated_at' => now()],
        //     ['faculty_id' => null, 'code' => '4', 'name' => 'SDM', 'type' => 'unit_pusat', 'created_at' => now(), 'updated_at' => now()],
        //     ['faculty_id' => null, 'code' => '5', 'name' => 'Akademik', 'type' => 'unit_pusat', 'created_at' => now(), 'updated_at' => now()],
            
        //     // ============================================
        //     // PRODI FAKULTAS SYARIAH (code = 1)
        //     // ============================================
        //     ['faculty_id' => $syariah, 'code' => '1', 'name' => 'Ekonomi Syariah', 'type' => 'prodi', 'created_at' => now(), 'updated_at' => now()],
        //     ['faculty_id' => $syariah, 'code' => '2', 'name' => 'Hukum Keluarga', 'type' => 'prodi', 'created_at' => now(), 'updated_at' => now()],
        //     ['faculty_id' => $syariah, 'code' => '3', 'name' => 'Perbankan Syariah', 'type' => 'prodi', 'created_at' => now(), 'updated_at' => now()],
            
        //     // ============================================
        //     // PRODI FAKULTAS TEKNIK (code = 2)
        //     // ============================================
        //     ['faculty_id' => $teknik, 'code' => '1', 'name' => 'Teknik Informatika', 'type' => 'prodi', 'created_at' => now(), 'updated_at' => now()],
        //     ['faculty_id' => $teknik, 'code' => '2', 'name' => 'Sistem Informasi', 'type' => 'prodi', 'created_at' => now(), 'updated_at' => now()],
        //     ['faculty_id' => $teknik, 'code' => '3', 'name' => 'Teknik Sipil', 'type' => 'prodi', 'created_at' => now(), 'updated_at' => now()],
        //     ['faculty_id' => $teknik, 'code' => '4', 'name' => 'Teknik Elektro', 'type' => 'prodi', 'created_at' => now(), 'updated_at' => now()],
            
        //     // ============================================
        //     // PRODI FAKULTAS EKONOMI (code = 3)
        //     // ============================================
        //     ['faculty_id' => $ekonomi, 'code' => '1', 'name' => 'Manajemen', 'type' => 'prodi', 'created_at' => now(), 'updated_at' => now()],
        //     ['faculty_id' => $ekonomi, 'code' => '2', 'name' => 'Akuntansi', 'type' => 'prodi', 'created_at' => now(), 'updated_at' => now()],
        //     ['faculty_id' => $ekonomi, 'code' => '3', 'name' => 'Ekonomi Pembangunan', 'type' => 'prodi', 'created_at' => now(), 'updated_at' => now()],
        // ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};