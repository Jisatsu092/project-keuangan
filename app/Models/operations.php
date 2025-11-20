<?php
// app/Models/operations.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class operations extends Model
{
    protected $fillable = [
        'account_type_code',
        'code',
        'name',
        'parent_category',
        'is_header',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_header' => 'boolean',
    ];

    // Relationships
    public function accountType(): BelongsTo
    {
        return $this->belongsTo(account_types::class, 'account_type_code', 'code');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Accounts::class, 'digit_2', 'code')
                    ->where('digit_1', $this->account_type_code);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByAccountType($query, string $typeCode)
    {
        return $query->where('account_type_code', $typeCode);
    }

    public function scopeHeaders($query)
    {
        return $query->where('is_header', true);
    }

    public function scopeDetails($query)
    {
        return $query->where('is_header', false);
    }

    public function scopeByParentCategory($query, ?string $category)
    {
        return $query->where('parent_category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('code');
    }

    // Helpers
    public function isHeader(): bool
    {
        return $this->is_header === true;
    }

    public function isDetail(): bool
    {
        return $this->is_header === false;
    }

    public function hasParent(): bool
    {
        return !is_null($this->parent_category);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->is_header) {
            return $this->name;
        }
        return "{$this->code} - {$this->name}";
    }

    public function getCategoryLabelAttribute(): string
    {
        if (!$this->parent_category) return '';
        
        $labels = [
            'operasional' => '🔵 Operasional',
            'program' => '🟣 Program',
            'hibah' => '🎁 Hibah',
            'donasi' => '💝 Donasi',
            'umum' => '📋 Umum',
        ];
        
        return $labels[$this->parent_category] ?? '';
    }
}