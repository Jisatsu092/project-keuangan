<?php
// app/Models/Faculty.php

namespace App\Models;

use App\Models\units;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class faculties extends Model
{
    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function units(): HasMany
    {
        return $this->hasMany(units::class);
    }

    public function prodis(): HasMany
    {
        return $this->hasMany(units::class)->where('type', 'prodi');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(accounts::class, 'digit_3', 'code');
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