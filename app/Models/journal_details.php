<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class journal_details extends Model
{
    protected $fillable = [
        'journal_id',
        'account_code',
        'description',
        'debit',
        'credit',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    // Relationships
    public function journal(): BelongsTo
    {
        return $this->belongsTo(journals::class, 'journal_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(accounts::class, 'account_code', 'code');
    }

    // Helpers
    public function getAmountAttribute(): float
    {
        return $this->debit > 0 ? $this->debit : $this->credit;
    }

    public function getTypeAttribute(): string
    {
        return $this->debit > 0 ? 'debit' : 'kredit';
    }
}