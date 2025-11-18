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

    // Relationships - PERBAIKI foreign key
    public function units(): HasMany
    {
        return $this->hasMany(units::class, 'faculty_id'); // TAMBAHKAN: foreign key
    }

    public function prodis(): HasMany
    {
        return $this->hasMany(units::class, 'faculty_id')->where('type', 'prodi'); // TAMBAHKAN: foreign key
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