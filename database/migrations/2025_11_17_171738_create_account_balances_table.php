<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_balances', function (Blueprint $table) {
            $table->id();
            $table->string('account_code', 20);
            $table->year('period_year');
            $table->unsignedTinyInteger('period_month');
            
            // Balances
            $table->decimal('beginning_balance', 20, 2)->default(0);
            $table->decimal('total_debit', 20, 2)->default(0);
            $table->decimal('total_credit', 20, 2)->default(0);
            $table->decimal('ending_balance', 20, 2)->default(0);
            
            $table->timestamp('last_calculated_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['account_code', 'period_year', 'period_month']);
            $table->index(['period_year', 'period_month']);
            $table->index('account_code');
            
            $table->foreign('account_code')->references('code')->on('accounts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_balances');
    }
};