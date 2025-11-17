<?php
// app/Models/ActivityType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class activity_types extends Model
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
        return $this->hasMany(accounts::class, 'digit_5', 'code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helpers
    public function getFullNameAttribute(): string
    {
        return "{$this->code} - {$this->name}";
    }
}