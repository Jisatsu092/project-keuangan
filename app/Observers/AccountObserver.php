<?php
// app/Observers/AccountsObserver.php

namespace App\Observers;

use App\Models\account_hierarchy;
use App\Models\Accounts;

class AccountsObserver
{
    public function creating(Accounts $account): void
    {
        $this->parseAccountCode($account);
        $this->setHierarchyAttributes($account);
    }

    public function created(Accounts $account): void
    {
        $this->buildHierarchyCache($account);
    }

    public function updating(Accounts $account): void
    {
        if ($account->isDirty('code')) {
            $this->parseAccountCode($account);
            $this->setHierarchyAttributes($account);
        }
    }

    public function updated(Accounts $account): void
    {
        if ($account->wasChanged('code')) {
            $this->rebuildHierarchyCache($account);
        }
    }

    public function deleting(Accounts $account): void
    {
        account_hierarchy::where('account_code', $account->code)
            ->orWhere('ancestor_code', $account->code)
            ->delete();
    }

    protected function parseAccountCode(Accounts $account): void
    {
        $code = $account->code;
        $length = strlen($code);

        $account->digit_1 = substr($code, 0, 1) ?: null;
        $account->digit_2 = substr($code, 1, 1) ?: null;
        $account->digit_3 = substr($code, 2, 1) ?: null;
        $account->digit_4 = substr($code, 3, 1) ?: null;
        $account->digit_5 = substr($code, 4, 1) ?: null;
        $account->digit_6 = substr($code, 5, 1) ?: null;
        $account->digit_7 = substr($code, 6, 1) ?: null;

        if ($length > 7) {
            $account->digit_extra = substr($code, 7);
        } else {
            $account->digit_extra = null;
        }
    }

    protected function setHierarchyAttributes(Accounts $account): void
    {
        $code = $account->code;
        $length = strlen($code);

        $account->is_header = preg_match('/0+$/', $code) && $length > 1;

        if ($account->is_header) {
            $account->can_transaction = false;
        }

        $trimmed = rtrim($code, '0');
        $account->level = strlen($trimmed);

        $parentCode = $this->findParentCode($code);
        if ($parentCode && Accounts::where('code', $parentCode)->exists()) {
            $account->parent_code = $parentCode;

            $parent = Accounts::where('code', $parentCode)->first();
            $account->full_path = ($parent->full_path ?: $parentCode) . '/' . $code;
        } else {
            $account->parent_code = null;
            $account->full_path = $code;
        }
    }

    protected function findParentCode(string $code): ?string
    {
        $length = strlen($code);

        if ($length < 2 || preg_match('/^[0-9]0+$/', $code)) {
            return null;
        }

        for ($i = $length - 1; $i >= 0; $i--) {
            if ($code[$i] !== '0') {
                $parentCode = substr($code, 0, $i) . '0' . substr($code, $i + 1);
                
                if (Accounts::where('code', $parentCode)->exists()) {
                    return $parentCode;
                }
                
                return $this->findParentCode($parentCode);
            }
        }

        return null;
    }

    protected function buildHierarchyCache(Accounts $account): void
    {
        account_hierarchy::create([
            'account_code' => $account->code,
            'ancestor_code' => $account->code,
            'depth' => 0,
        ]);

        $currentParentCode = $account->parent_code;
        $depth = 1;

        while ($currentParentCode) {
            account_hierarchy::create([
                'account_code' => $account->code,
                'ancestor_code' => $currentParentCode,
                'depth' => $depth,
            ]);

            $parent = Accounts::where('code', $currentParentCode)->first();
            $currentParentCode = $parent ? $parent->parent_code : null;
            $depth++;
        }
    }

    protected function rebuildHierarchyCache(Accounts $account): void
    {
        account_hierarchy::where('account_code', $account->code)->delete();
        
        $this->buildHierarchyCache($account);

        $children = Accounts::where('parent_code', $account->getOriginal('code'))->get();
        foreach ($children as $child) {
            $child->parent_code = $account->code;
            $child->saveQuietly();
        }
    }
}