<?php
// app/Models/Operation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class operations extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function accounts(): HasMany
    {
        return $this->hasMany(accounts::class, 'digit_2', 'code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helpers
    public function isOperasional(): bool
    {
        return $this->code === '1';
    }

    public function isProgram(): bool
    {
        return $this->code === '2';
    }
}