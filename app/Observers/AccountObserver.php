<?php
// app/Observers/AccountObserver.php

namespace App\Observers;

use App\Models\account_hierarchy;
use App\Models\accounts;
use App\Models\AccountHierarchy;

class AccountObserver
{
    /**
     * Handle the accounts "creating" event.
     * Parse digits, set parent, level, is_header SEBELUM insert ke DB
     */
    public function creating(accounts $account): void
    {
        $this->parseAccountCode($account);
        $this->setHierarchyAttributes($account);
    }

    /**
     * Handle the accounts "created" event.
     * Build hierarchy cache SETELAH insert ke DB
     */
    public function created(accounts $account): void
    {
        $this->buildHierarchyCache($account);
    }

    /**
     * Handle the accounts "updating" event.
     */
    public function updating(accounts $account): void
    {
        // Kalau code berubah, re-parse
        if ($account->isDirty('code')) {
            $this->parseAccountCode($account);
            $this->setHierarchyAttributes($account);
        }
    }

    /**
     * Handle the accounts "updated" event.
     */
    public function updated(accounts $account): void
    {
        // Kalau code berubah, rebuild hierarchy
        if ($account->wasChanged('code')) {
            $this->rebuildHierarchyCache($account);
        }
    }

    /**
     * Handle the accounts "deleting" event.
     */
    public function deleting(accounts $account): void
    {
        // Hapus hierarchy cache
        account_hierarchy::where('account_code', $account->code)
            ->orWhere('ancestor_code', $account->code)
            ->delete();
    }

    /**
     * Parse account code menjadi digit components
     */
    protected function parseAccountCode(accounts $account): void
    {
        $code = $account->code;
        $length = strlen($code);

        // Parse 7 digit pertama
        $account->digit_1 = substr($code, 0, 1) ?: null;
        $account->digit_2 = substr($code, 1, 1) ?: null;
        $account->digit_3 = substr($code, 2, 1) ?: null;
        $account->digit_4 = substr($code, 3, 1) ?: null;
        $account->digit_5 = substr($code, 4, 1) ?: null;
        $account->digit_6 = substr($code, 5, 1) ?: null;
        $account->digit_7 = substr($code, 6, 1) ?: null;

        // Sisanya masuk digit_extra (untuk ekspansi 8+ digit)
        if ($length > 7) {
            $account->digit_extra = substr($code, 7);
        } else {
            $account->digit_extra = null;
        }
    }

    /**
     * Set hierarchy attributes: parent_code, level, is_header, full_path
     */
    protected function setHierarchyAttributes(accounts $account): void
    {
        $code = $account->code;
        $length = strlen($code);

        // Detect is_header (ends with zeros)
        $account->is_header = preg_match('/0+$/', $code) && $length > 1;

        // Header accounts tidak bisa transaction
        if ($account->is_header) {
            $account->can_transaction = false;
        }

        // Calculate level (berapa digit non-zero dari kanan)
        // Contoh: 5000000 = level 1, 5100000 = level 2, 5102403 = level 5
        $trimmed = rtrim($code, '0');
        $account->level = strlen($trimmed);

        // Find parent code
        $parentCode = $this->findParentCode($code);
        if ($parentCode && accounts::where('code', $parentCode)->exists()) {
            $account->parent_code = $parentCode;

            // Build full path
            $parent = accounts::where('code', $parentCode)->first();
            $account->full_path = ($parent->full_path ?: $parentCode) . '/' . $code;
        } else {
            $account->parent_code = null;
            $account->full_path = $code;
        }
    }

    /**
     * Find parent code dengan logic:
     * - Replace digit terakhir non-zero dengan 0
     * - Cari dari kanan ke kiri sampai ketemu parent yang exist
     */
    protected function findParentCode(string $code): ?string
    {
        $length = strlen($code);

        // Kalau panjang < 2 atau semua 0 kecuali digit pertama, gak ada parent
        if ($length < 2 || preg_match('/^[1-9]0+$/', $code)) {
            return null;
        }

        // Cari posisi digit non-zero terakhir
        for ($i = $length - 1; $i >= 0; $i--) {
            if ($code[$i] !== '0') {
                // Replace dengan 0
                $parentCode = substr($code, 0, $i) . '0' . substr($code, $i + 1);
                
                // Cek apakah parent exist
                if (accounts::where('code', $parentCode)->exists()) {
                    return $parentCode;
                }
                
                // Kalau gak exist, coba cari parent dari parent (recursive)
                return $this->findParentCode($parentCode);
            }
        }

        return null;
    }

    /**
     * Build hierarchy cache di account_hierarchy table
     */
    protected function buildHierarchyCache(accounts $account): void
    {
        // Insert self (depth 0)
        account_hierarchy::create([
            'account_code' => $account->code,
            'ancestor_code' => $account->code,
            'depth' => 0,
        ]);

        // Insert ancestors (depth 1, 2, 3, ...)
        $currentParentCode = $account->parent_code;
        $depth = 1;

        while ($currentParentCode) {
            account_hierarchy::create([
                'account_code' => $account->code,
                'ancestor_code' => $currentParentCode,
                'depth' => $depth,
            ]);

            // Cari parent dari parent
            $parent = accounts::where('code', $currentParentCode)->first();
            $currentParentCode = $parent ? $parent->parent_code : null;
            $depth++;
        }
    }

    /**
     * Rebuild hierarchy cache (kalau code berubah)
     */
    protected function rebuildHierarchyCache(accounts $account): void
    {
        // Hapus existing
        account_hierarchy::where('account_code', $account->code)->delete();
        
        // Build ulang
        $this->buildHierarchyCache($account);

        // Update all children yang parent_code-nya nunjuk ke account ini
        $children = accounts::where('parent_code', $account->getOriginal('code'))->get();
        foreach ($children as $child) {
            $child->parent_code = $account->code;
            $child->saveQuietly(); // Tanpa trigger observer lagi
        }
    }
}