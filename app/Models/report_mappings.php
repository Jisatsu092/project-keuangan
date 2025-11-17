<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class report_mappings extends Model
{
    protected $fillable = [
        'report_type',
        'section',
        'subsection',
        'account_pattern',
        'display_order',
    ];

    // Scopes
    public function scopeByReportType($query, string $type)
    {
        return $query->where('report_type', $type)
                     ->orderBy('display_order');
    }

    // Helpers
    public function matchesAccount(string $accountCode): bool
    {
        $pattern = str_replace('%', '.*', $this->account_pattern);
        return preg_match("/^{$pattern}$/", $accountCode);
    }
}