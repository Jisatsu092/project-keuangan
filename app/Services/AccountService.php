<?php
// app/Services/AccountService.php

namespace App\Services;

use App\Models\accounts;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function parseAccountCode(string $code): array
    {
        $digits = str_split($code);
        
        return [
            'digit_1' => $digits[0] ?? null,
            'digit_2' => $digits[1] ?? null,
            'digit_3' => $digits[2] ?? null,
            'digit_4' => $digits[3] ?? null,
            'digit_5' => $digits[4] ?? null,
            'digit_6' => $digits[5] ?? null,
            'digit_7' => $digits[6] ?? null,
            'digit_extra' => isset($digits[7]) ? substr($code, 7) : null,
        ];
    }

    public function determineParentCode(string $code): ?string
    {
        $length = strlen($code);
        
        if ($length <= 1) {
            return null;
        }

        // Find the last non-zero digit and truncate after it
        for ($i = $length - 1; $i >= 0; $i--) {
            if ($code[$i] !== '0') {
                $parentLength = $i + 1;
                $parentCode = substr($code, 0, $parentLength);
                
                // Pad with zeros to maintain standard length
                while (strlen($parentCode) < 7) {
                    $parentCode .= '0';
                }
                
                return $parentCode !== $code ? $parentCode : null;
            }
        }

        return null;
    }

    public function calculateLevel(string $code): int
    {
        $trimmed = rtrim($code, '0');
        return strlen($trimmed);
    }

    public function isHeaderAccount(string $code): bool
    {
        return $code === rtrim($code, '0') . str_repeat('0', strlen($code) - strlen(rtrim($code, '0')));
    }

    public function buildFullPath(accounts $account): string
    {
        $path = [];
        $current = $account;
        
        while ($current) {
            array_unshift($path, $current->code);
            $current = $current->parent;
        }
        
        return implode('/', $path);
    }

    public function rebuildHierarchyCache(): void
    {
        DB::table('account_hierarchy')->truncate();
        
        $accounts = accounts::all();
        
        $hierarchyData = [];
        foreach ($accounts as $account) {
            // Add self with depth 0
            $hierarchyData[] = [
                'account_code' => $account->code,
                'ancestor_code' => $account->code,
                'depth' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Add all ancestors
            $parent = $account->parent;
            $depth = 1;
            while ($parent) {
                $hierarchyData[] = [
                    'account_code' => $account->code,
                    'ancestor_code' => $parent->code,
                    'depth' => $depth,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $parent = $parent->parent;
                $depth++;
            }
        }
        
        collect($hierarchyData)->chunk(1000)->each(function ($chunk) {
            DB::table('account_hierarchy')->insert($chunk->toArray());
        });
    }

    public function getDescendants(string $accountCode): Collection
    {
        return DB::table('account_hierarchy')
            ->join('accounts', 'account_hierarchy.account_code', '=', 'accounts.code')
            ->where('account_hierarchy.ancestor_code', $accountCode)
            ->where('account_hierarchy.depth', '>', 0)
            ->select('accounts.*')
            ->get();
    }

    public function validateAccountCode(string $code): bool
    {
        return preg_match('/^[1-5][1-2][0-9][0-9][0-9][0-9][0-9]/', $code);
    }
}