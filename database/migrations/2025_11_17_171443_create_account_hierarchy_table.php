<?php
// database/migrations/2024_01_01_000007_create_account_hierarchy_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_hierarchy', function (Blueprint $table) {
            $table->id();
            $table->string('account_code', 20);
            $table->string('ancestor_code', 20);
            $table->unsignedTinyInteger('depth')->comment('0=self, 1=parent, 2=grandparent');
            $table->timestamps();
            
            $table->unique(['account_code', 'ancestor_code']);
            $table->index('account_code');
            $table->index('ancestor_code');
            $table->index('depth');
            
            $table->foreign('account_code')->references('code')->on('accounts')->onDelete('cascade');
            $table->foreign('ancestor_code')->references('code')->on('accounts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_hierarchy');
    }
};