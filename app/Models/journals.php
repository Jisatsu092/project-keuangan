<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class journals extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'journal_number',
        'transaction_date',
        'description',
        'document_reference',
        'status',
        'posted_at',
        'posted_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'posted_at' => 'datetime',
    ];

    // Relationships
    
    public function details(): HasMany
    {
        return $this->hasMany(journal_details::class, 'journal_id');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeVoid($query)
    {
        return $query->where('status', 'void');
    }

    public function scopeByPeriod($query, int $year, ?int $month = null)
    {
        $query->whereYear('transaction_date', $year);
        
        if ($month) {
            $query->whereMonth('transaction_date', $month);
        }
        
        return $query;
    }

    // Helpers
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPosted(): bool
    {
        return $this->status === 'posted';
    }

    public function isVoid(): bool
    {
        return $this->status === 'void';
    }

    public function getTotalDebitAttribute(): float
    {
        return $this->details()->sum('debit');
    }

    public function getTotalCreditAttribute(): float
    {
        return $this->details()->sum('credit');
    }

    public function isBalanced(): bool
    {
        return round($this->total_debit, 2) === round($this->total_credit, 2);
    }
}