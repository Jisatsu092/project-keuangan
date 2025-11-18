<?php
// app/Models/Accounts.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Accounts extends Model
{
    use SoftDeletes;

    protected $table = 'accounts';

    protected $fillable = [
        'code',
        'name',
        'description',
        'digit_1',
        'digit_2',
        'digit_3',
        'digit_4',
        'digit_5',
        'digit_6',
        'digit_7',
        'digit_extra',
        'parent_code',
        'level',
        'is_header',
        'full_path',
        'normal_balance',
        'is_active',
        'can_transaction',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_header' => 'boolean',
        'is_active' => 'boolean',
        'can_transaction' => 'boolean',
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Accounts::class, 'parent_code', 'code');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Accounts::class, 'parent_code', 'code');
    }

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(account_types::class, 'digit_1', 'code');
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(operations::class, 'digit_2', 'code');
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(faculties::class, 'digit_3', 'code');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(units::class, 'digit_4', 'code');
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(activity_types::class, 'digit_5', 'code');
    }

    public function journalDetails(): HasMany
    {
        return $this->hasMany(journal_details::class, 'account_code', 'code');
    }

    public function balances(): HasMany
    {
        return $this->hasMany(account_balances::class, 'account_code', 'code');
    }

    public function hierarchy(): HasMany
    {
        return $this->hasMany(account_hierarchy::class, 'account_code', 'code');
    }

    public function ancestors(): HasMany
    {
        return $this->hasMany(account_hierarchy::class, 'account_code', 'code')
                    ->where('depth', '>', 0)
                    ->orderBy('depth');
    }

    public function descendants(): HasMany
    {
        return $this->hasMany(account_hierarchy::class, 'ancestor_code', 'code')
                    ->where('depth', '>', 0);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeHeader(Builder $query): Builder
    {
        return $query->where('is_header', true);
    }

    public function scopeLeaf(Builder $query): Builder
    {
        return $query->where('is_header', false);
    }

    public function scopeTransactionable(Builder $query): Builder
    {
        return $query->where('can_transaction', true);
    }

    public function scopeByAccountType(Builder $query, string $typeCode): Builder
    {
        return $query->where('digit_1', $typeCode);
    }

    public function scopeByOperation(Builder $query, string $opCode): Builder
    {
        return $query->where('digit_2', $opCode);
    }

    public function scopeByFaculty(Builder $query, string $facultyCode): Builder
    {
        return $query->where('digit_3', $facultyCode);
    }

    public function scopePusat(Builder $query): Builder
    {
        return $query->where('digit_3', '0');
    }

    public function scopeByLevel(Builder $query, int $level): Builder
    {
        return $query->where('level', $level);
    }

    public function scopeSearchCode(Builder $query, string $search): Builder
    {
        return $query->where('code', 'LIKE', "%{$search}%");
    }

    public function scopeSearchName(Builder $query, string $search): Builder
    {
        return $query->where('name', 'LIKE', "%{$search}%");
    }

    // Helpers
    public function isPusat(): bool
    {
        return $this->digit_3 === '0';
    }

    public function isFakultas(): bool
    {
        return $this->digit_3 !== '0' && $this->digit_3 !== null;
    }

    public function isDebit(): bool
    {
        return $this->normal_balance === 'debit';
    }

    public function isKredit(): bool
    {
        return $this->normal_balance === 'kredit';
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->code} - {$this->name}";
    }

    public function getBreadcrumbAttribute(): array
    {
        $breadcrumb = [];
        $current = $this;

        while ($current) {
            array_unshift($breadcrumb, [
                'code' => $current->code,
                'name' => $current->name,
                'level' => $current->level,
            ]);
            $current = $current->parent;
        }

        return $breadcrumb;
    }

    public function getAllDescendants(): array
    {
        return Accounts::where('code', 'LIKE', $this->code . '%')
                      ->where('code', '!=', $this->code)
                      ->orderBy('code')
                      ->get()
                      ->toArray();
    }

    public function isAncestorOf(Accounts $account): bool
    {
        return str_starts_with($account->code, $this->code) && $account->code !== $this->code;
    }

    public function isDescendantOf(Accounts $account): bool
    {
        return str_starts_with($this->code, $account->code) && $this->code !== $account->code;
    }

    // Get children with calculated balance
    public function getChildrenWithBalance(int $year, int $month)
    {
        return $this->children()
            ->with(['balances' => function($q) use ($year, $month) {
                $q->where('period_year', $year)
                  ->where('period_month', $month);
            }])
            ->orderBy('code')
            ->get();
    }

    // Calculate total from children (for header accounts)
    public function calculateTotalFromChildren(int $year, int $month): array
    {
        $children = $this->getChildrenWithBalance($year, $month);
        
        $totalDebit = 0;
        $totalCredit = 0;
        $totalBalance = 0;

        foreach ($children as $child) {
            $balance = $child->balances->first();
            if ($balance) {
                $totalDebit += $balance->total_debit;
                $totalCredit += $balance->total_credit;
                $totalBalance += $balance->ending_balance;
            }
        }

        return [
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'ending_balance' => $totalBalance,
        ];
    }
}