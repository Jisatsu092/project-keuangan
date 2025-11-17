<?php
// database/migrations/2024_01_01_000002_create_operations_table.php

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
            $table->char('code', 1)->unique()->comment('1=Ops, 2=Program, dst');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
        });

        DB::table('operations')->insert([
            ['code' => '1', 'name' => 'Operasional', 'description' => 'Kegiatan rutin operasional', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '2', 'name' => 'Program', 'description' => 'Program khusus/project', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};