<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_mappings', function (Blueprint $table) {
            $table->id();
            $table->enum('report_type', ['lpk', 'lak', 'trial_balance', 'neraca_saldo']);
            $table->string('section', 100)->comment('e.g., Aset Lancar');
            $table->string('subsection', 100)->nullable()->comment('e.g., Kas dan Setara Kas');
            $table->string('account_pattern', 20)->comment('e.g., 1%, 11%');
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->index('report_type');
            $table->index('account_pattern');
        });

        // Sample mappings untuk LPK
        DB::table('report_mappings')->insert([
            ['report_type' => 'lpk', 'section' => 'ASET', 'subsection' => 'Aset Lancar', 'account_pattern' => '11%', 'display_order' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['report_type' => 'lpk', 'section' => 'ASET', 'subsection' => 'Aset Tidak Lancar', 'account_pattern' => '12%', 'display_order' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['report_type' => 'lpk', 'section' => 'LIABILITAS', 'subsection' => 'Liabilitas Jangka Pendek', 'account_pattern' => '21%', 'display_order' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['report_type' => 'lpk', 'section' => 'LIABILITAS', 'subsection' => 'Liabilitas Jangka Panjang', 'account_pattern' => '22%', 'display_order' => 40, 'created_at' => now(), 'updated_at' => now()],
            ['report_type' => 'lpk', 'section' => 'EKUITAS', 'subsection' => 'Modal', 'account_pattern' => '3%', 'display_order' => 50, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('report_mappings');
    }
};
