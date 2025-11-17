<?php
// database/migrations/2025_11_17_165107_create_accounts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('7-9+ digit flexible');
            $table->string('name');
            $table->text('description')->nullable();
            
            // Parsed components (auto-filled by observer)
            $table->char('digit_1', 1)->nullable()->comment('Account Type (1-9)');
            $table->char('digit_2', 1)->nullable()->comment('Operation (1-9)');
            $table->char('digit_3', 1)->nullable()->comment('Faculty (0=pusat, 1-9=fakultas)');
            $table->char('digit_4', 1)->nullable()->comment('Unit/Prodi (0-9)');
            $table->char('digit_5', 1)->nullable()->comment('Activity Type (0-9)');
            $table->char('digit_6', 1)->nullable()->comment('Detail 1 (0-9)');
            $table->char('digit_7', 1)->nullable()->comment('Detail 2 (0-9)');
            $table->string('digit_extra', 10)->nullable()->comment('Digit 8+ untuk ekspansi');
            
            // Hierarchy
            $table->string('parent_code', 20)->nullable()->comment('Parent account code');
            $table->unsignedTinyInteger('level')->default(1)->comment('Depth: 1=root, 7=leaf');
            $table->boolean('is_header')->default(false)->comment('true jika ends with zeros');
            $table->text('full_path')->nullable()->comment('5000000/5100000/5102403');
            
            // Financial properties
            $table->enum('normal_balance', ['debit', 'kredit']);
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('can_transaction')->default(true)->comment('false untuk header accounts');
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes - HAPUS FULLTEXT INDEX
            $table->index('code');
            $table->index('parent_code');
            $table->index('level');
            $table->index('is_header');
            $table->index('can_transaction');
            $table->index(['digit_1', 'digit_2']);
            $table->index(['digit_1', 'digit_2', 'digit_3']);
            $table->index(['digit_1', 'digit_2', 'digit_3', 'digit_4']);
            // HAPUS BARIS INI: $table->fullText('name');
        });

        // Self-referencing foreign key
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreign('parent_code')->references('code')->on('accounts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};