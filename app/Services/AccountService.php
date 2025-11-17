<?php
// app/Services/AccountService.php

namespace App\Services;

use App\Models\accounts;
use App\Models\account_balances;
use App\Models\journal_details;
use App\Models\JournalDetail;
use Illuminate\Support\Facades\DB;

class AccountService
{
    /**
     * Generate account code dari component
     */
    public function generateCode(array $components): string
    {
        return implode('', array_filter($components));
    }

    /**
     * Validate account code format
     */
    public function validateCode(string $code): bool
    {
        // Minimal 7 digit, semua numeric
        return strlen($code) >= 7 && ctype_digit($code);
    }

    /**
     * Check apakah code sudah exist
     */
    public function codeExists(string $code, ?int $exceptId = null): bool
    {
        $query = accounts::where('code', $code);
        
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        
        return $query->exists();
    }

    /**
     * Get account tree (hierarchical)
     */
    public function getAccountTree(?string $parentCode = null, int $maxLevel = null): array
    {
        $query = accounts::with(['accountType', 'operation', 'faculty', 'unit'])
            ->orderBy('code');

        if ($parentCode) {
            // Get children dari parent tertentu
            $query->where('parent_code', $parentCode);
        } else {
            // Get root accounts
            $query->whereNull('parent_code')->orWhere('parent_code', '');
        }

        if ($maxLevel) {
            $query->where('level', '<=', $maxLevel);
        }

        $accounts = $query->get();

        return $accounts->map(function ($account) use ($maxLevel) {
            return [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'level' => $account->level,
                'is_header' => $account->is_header,
                'can_transaction' => $account->can_transaction,
                'normal_balance' => $account->normal_balance,
                'has_children' => $account->children()->exists(),
                'children' => $account->is_header && (!$maxLevel || $account->level < $maxLevel)
                    ? $this->getAccountTree($account->code, $maxLevel)
                    : [],
            ];
        })->toArray();
    }

    /**
     * Get account dengan balance
     */
    public function getAccountWithBalance(string $code, int $year, int $month): ?array
    {
        $account = accounts::where('code', $code)->first();
        
        if (!$account) {
            return null;
        }

        $balance = account_balances::where('account_code', $code)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->first();

        return [
            'account' => $account,
            'balance' => $balance,
            'beginning_balance' => $balance?->beginning_balance ?? 0,
            'total_debit' => $balance?->total_debit ?? 0,
            'total_credit' => $balance?->total_credit ?? 0,
            'ending_balance' => $balance?->ending_balance ?? 0,
        ];
    }

    /**
     * Calculate balance untuk account (dan update table account_balances)
     */
    public function calculateBalance(string $accountCode, int $year, int $month): void
    {
        $account = accounts::where('code', $accountCode)->first();
        
        if (!$account) {
            throw new \Exception("accounts {$accountCode} not found");
        }

        // Get previous month balance
        $prevMonth = $month - 1;
        $prevYear = $year;
        
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }

        $prevBalance = account_balances::where('account_code', $accountCode)
            ->where('period_year', $prevYear)
            ->where('period_month', $prevMonth)
            ->first();

        $beginningBalance = $prevBalance?->ending_balance ?? 0;

        // Sum debit & credit dari journal_details (hanya yang status posted)
        $transactions = journal_details::whereHas('journal', function ($q) use ($year, $month) {
                $q->where('status', 'posted')
                  ->whereYear('transaction_date', $year)
                  ->whereMonth('transaction_date', $month);
            })
            ->where('account_code', $accountCode)
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $totalDebit = $transactions->total_debit ?? 0;
        $totalCredit = $transactions->total_credit ?? 0;

        // Calculate ending balance based on normal balance
        if ($account->normal_balance === 'debit') {
            $endingBalance = $beginningBalance + $totalDebit - $totalCredit;
        } else {
            $endingBalance = $beginningBalance + $totalCredit - $totalDebit;
        }

        // Update or create balance record
        account_balances::updateOrCreate(
            [
                'account_code' => $accountCode,
                'period_year' => $year,
                'period_month' => $month,
            ],
            [
                'beginning_balance' => $beginningBalance,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'ending_balance' => $endingBalance,
                'last_calculated_at' => now(),
            ]
        );
    }

    /**
     * Calculate balance untuk semua children account (recursive)
     */
    public function calculateBalanceRecursive(string $parentCode, int $year, int $month): void
    {
        $parent = accounts::where('code', $parentCode)->first();
        
        if (!$parent) {
            return;
        }

        // Calculate untuk account ini
        if (!$parent->is_header) {
            $this->calculateBalance($parentCode, $year, $month);
        }

        // Calculate untuk children
        $children = accounts::where('parent_code', $parentCode)->get();
        foreach ($children as $child) {
            $this->calculateBalanceRecursive($child->code, $year, $month);
        }

        // Kalau parent adalah header, sum dari children
        if ($parent->is_header) {
            $this->calculateHeaderBalance($parentCode, $year, $month);
        }
    }

    /**
     * Calculate balance untuk header account (sum dari children)
     */
    protected function calculateHeaderBalance(string $headerCode, int $year, int $month): void
    {
        $children = accounts::where('parent_code', $headerCode)->get();

        $totalBeginning = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $totalEnding = 0;

        foreach ($children as $child) {
            $balance = account_balances::where('account_code', $child->code)
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->first();

            if ($balance) {
                $totalBeginning += $balance->beginning_balance;
                $totalDebit += $balance->total_debit;
                $totalCredit += $balance->total_credit;
                $totalEnding += $balance->ending_balance;
            }
        }

        account_balances::updateOrCreate(
            [
                'account_code' => $headerCode,
                'period_year' => $year,
                'period_month' => $month,
            ],
            [
                'beginning_balance' => $totalBeginning,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'ending_balance' => $totalEnding,
                'last_calculated_at' => now(),
            ]
        );
    }

    /**
     * Get trial balance
     */
    public function getTrialBalance(int $year, int $month): array
    {
        $accounts = accounts::where('can_transaction', true)
            ->with(['accountType', 'balances' => function ($q) use ($year, $month) {
                $q->where('period_year', $year)->where('period_month', $month);
            }])
            ->orderBy('code')
            ->get();

        $totalDebit = 0;
        $totalCredit = 0;

        $data = $accounts->map(function ($account) use (&$totalDebit, &$totalCredit) {
            $balance = $account->balances->first();
            $endingBalance = $balance?->ending_balance ?? 0;

            $debit = 0;
            $credit = 0;

            if ($endingBalance != 0) {
                if ($account->normal_balance === 'debit') {
                    $debit = $endingBalance > 0 ? $endingBalance : 0;
                    $credit = $endingBalance < 0 ? abs($endingBalance) : 0;
                } else {
                    $credit = $endingBalance > 0 ? $endingBalance : 0;
                    $debit = $endingBalance < 0 ? abs($endingBalance) : 0;
                }
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            return [
                'code' => $account->code,
                'name' => $account->name,
                'debit' => $debit,
                'credit' => $credit,
            ];
        })->filter(function ($item) {
            return $item['debit'] > 0 || $item['credit'] > 0;
        })->values();

        return [
            'data' => $data,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'is_balanced' => round($totalDebit, 2) === round($totalCredit, 2),
        ];
    }
}