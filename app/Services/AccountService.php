<?php
// app/Services/AccountService.php

namespace App\Services;

use App\Models\account_balances;
use App\Models\Accounts;
use App\Models\journal_details;
use App\Models\JournalDetail;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function generateCode(array $components): string
    {
        return implode('', array_filter($components));
    }

    public function validateCode(string $code): bool
    {
        return strlen($code) >= 7 && ctype_digit($code);
    }

    public function codeExists(string $code, ?int $exceptId = null): bool
    {
        $query = Accounts::where('code', $code);
        
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        
        return $query->exists();
    }

    /**
     * Get trial balance dengan hierarchy support
     */
    public function getTrialBalance(int $year, int $month): array
    {
        // Get all accounts dengan balance
        $accounts = Accounts::with(['balances' => function($q) use ($year, $month) {
                $q->where('period_year', $year)
                  ->where('period_month', $month);
            }])
            ->orderBy('code')
            ->get();

        $data = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $balance = $account->balances->first();
            
            // Untuk header accounts, calculate total from children
            if ($account->is_header) {
                $childrenTotal = $account->calculateTotalFromChildren($year, $month);
                $endingBalance = $childrenTotal['ending_balance'];
            } else {
                $endingBalance = $balance?->ending_balance ?? 0;
            }

            // Skip jika balance = 0
            if ($endingBalance == 0) {
                continue;
            }

            $debit = 0;
            $credit = 0;

            // Convert ending balance to debit/credit based on normal balance
            if ($account->normal_balance === 'debit') {
                if ($endingBalance > 0) {
                    $debit = $endingBalance;
                } else {
                    $credit = abs($endingBalance);
                }
            } else {
                if ($endingBalance > 0) {
                    $credit = $endingBalance;
                } else {
                    $debit = abs($endingBalance);
                }
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            $data[] = [
                'code' => $account->code,
                'name' => $account->name,
                'level' => $account->level,
                'is_header' => $account->is_header,
                'debit' => $debit,
                'credit' => $credit,
                'parent_code' => $account->parent_code,
            ];
        }

        return [
            'data' => $data,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'is_balanced' => round($totalDebit, 2) === round($totalCredit, 2),
        ];
    }

    /**
     * Calculate balance untuk account
     */
    public function calculateBalance(string $accountCode, int $year, int $month): void
    {
        $account = Accounts::where('code', $accountCode)->first();
        
        if (!$account) {
            throw new \Exception("Account {$accountCode} not found");
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

        // Sum debit & credit dari journal_details (only posted)
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

        // Calculate ending balance
        if ($account->normal_balance === 'debit') {
            $endingBalance = $beginningBalance + $totalDebit - $totalCredit;
        } else {
            $endingBalance = $beginningBalance + $totalCredit - $totalDebit;
        }

        // Update or create balance
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
     * Calculate all balances untuk period tertentu
     */
    public function calculateAllBalances(int $year, int $month): void
    {
        // Calculate untuk semua leaf accounts (yang bisa transaction)
        $accounts = Accounts::where('can_transaction', true)->get();

        foreach ($accounts as $account) {
            $this->calculateBalance($account->code, $year, $month);
        }

        // Calculate untuk header accounts (aggregate dari children)
        $headerAccounts = Accounts::where('is_header', true)
            ->orderBy('level', 'desc') // Start from deepest level
            ->get();

        foreach ($headerAccounts as $header) {
            $childrenTotal = $header->calculateTotalFromChildren($year, $month);
            
            account_balances::updateOrCreate(
                [
                    'account_code' => $header->code,
                    'period_year' => $year,
                    'period_month' => $month,
                ],
                [
                    'beginning_balance' => 0, // Headers don't have beginning balance
                    'total_debit' => $childrenTotal['total_debit'],
                    'total_credit' => $childrenTotal['total_credit'],
                    'ending_balance' => $childrenTotal['ending_balance'],
                    'last_calculated_at' => now(),
                ]
            );
        }
    }
}