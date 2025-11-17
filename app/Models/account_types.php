<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class account_types extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'normal_balance',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function accounts(): HasMany
    {
        return $this->hasMany(accounts::class, 'digit_1', 'code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // Helpers
    public function isDebit(): bool
    {
        return $this->normal_balance === 'debit';
    }

    public function isKredit(): bool
    {
        return $this->normal_balance === 'kredit';
    }
}