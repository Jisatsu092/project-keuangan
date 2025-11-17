<?php
// app/Models/AccountHierarchy.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class account_hierarchy extends Model
{
    protected $table = 'account_hierarchy';
    
    protected $fillable = [
        'account_code',
        'ancestor_code',
        'depth',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(accounts::class, 'account_code', 'code');
    }

    public function ancestor(): BelongsTo
    {
        return $this->belongsTo(accounts::class, 'ancestor_code', 'code');
    }
}