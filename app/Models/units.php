<?php
// app/Models/Unit.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class units extends Model
{
    protected $fillable = [
        'faculty_id', // UBAH: faculties_id -> faculty_id
        'code',
        'name',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function faculty(): BelongsTo // UBAH: faculties -> faculty (singular)
    {
        return $this->belongsTo(faculties::class, 'faculty_id'); // UBAH: tambahkan foreign key
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(accounts::class, 'digit_4', 'code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeProdi($query)
    {
        return $query->where('type', 'prodi');
    }

    public function scopeUnitPusat($query)
    {
        return $query->where('type', 'unit_pusat')->whereNull('faculty_id'); // UBAH: faculties_id -> faculty_id
    }

    public function scopeByFaculty($query, $facultyId) // UBAH: ByFaculties -> ByFaculty
    {
        return $query->where('faculty_id', $facultyId); // UBAH: faculties_id -> faculty_id
    }

    // Helpers
    public function isProdi(): bool
    {
        return $this->type === 'prodi';
    }

    public function isUnitPusat(): bool
    {
        return $this->type === 'unit_pusat' && $this->faculty_id === null; // UBAH: faculties_id -> faculty_id
    }

    public function getFullNameAttribute(): string
    {
        if ($this->isUnitPusat()) {
            return "{$this->name} (Pusat)";
        }
        
        return $this->faculty ? "{$this->faculty->name} - {$this->name}" : $this->name;
    }

    public function getDisplayCodeAttribute(): string
    {
        if ($this->isUnitPusat()) {
            return "0{$this->code}"; // e.g., 02 untuk Keuangan
        }
        
        return $this->faculty ? "{$this->faculty->code}{$this->code}" : $this->code; // e.g., 11 untuk Syariah-Ekonomi Syariah
    }
}