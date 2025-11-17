<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class account_balances extends Model
{
    protected $fillable = [
        'account_code',
        'period_year',
        'period_month',
        'beginning_balance',
        'total_debit',
        'total_credit',
        'ending_balance',
        'last_calculated_at',
    ];

    protected $casts = [
        'period_year' => 'integer',
        'period_month' => 'integer',
        'beginning_balance' => 'decimal:2',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'ending_balance' => 'decimal:2',
        'last_calculated_at' => 'datetime',
    ];

    // Relationships
    public function account(): BelongsTo
    {
        return $this->belongsTo(accounts::class, 'account_code', 'code');
    }

    // Scopes
    public function scopeByPeriod($query, int $year, int $month)
    {
        return $query->where('period_year', $year)
                     ->where('period_month', $month);
    }

    public function scopeByYear($query, int $year)
    {
        return $query->where('period_year', $year);
    }

    // Helpers
    public function getMutationAttribute(): float
    {
        return $this->total_debit - $this->total_credit;
    }

    public function getPeriodNameAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $months[$this->period_month] . ' ' . $this->period_year;
    }
}