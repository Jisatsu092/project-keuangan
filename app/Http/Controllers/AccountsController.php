<?php
// app/Http/Controllers/AccountController.php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Services\AccountService;
use Illuminate\Http\Request;

class AccountsController extends Controller
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function index(Request $request)
    {
        $query = accounts::with('parent');
        
        // Filter by account type
        if ($request->has('type') && $request->type) {
            $query->where('digit_1', $request->type);
        }
        
        // Filter by faculty/unit
        if ($request->has('faculty') && $request->faculty !== '') {
            $query->where('digit_3', $request->faculty);
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', "%{$request->search}%")
                  ->orWhere('name', 'like', "%{$request->search}%");
            });
        }

        $accounts = $query->orderBy('code')->paginate(20);

        $accountTypes = [
            '1' => 'Aset',
            '2' => 'Hutang', 
            '3' => 'Modal',
            '4' => 'Pendapatan',
            '5' => 'Beban'
        ];

        $faculties = [
            '0' => 'Unit Pusat',
            '1' => 'Fakultas Syariah',
            '2' => 'Fakultas Teknik',
            '3' => 'Fakultas Ekonomi',
            '4' => 'Fakultas Tarbiyah',
            '5' => 'Fakultas Hukum',
        ];

        return view('pages.accounts.index', compact('accounts', 'accountTypes', 'faculties'));
    }

    public function create()
    {
        $parentAccounts = accounts::where('is_header', true)
            ->orderBy('code')
            ->get();
            
        $accountTypes = [
            '1' => 'Aset',
            '2' => 'Hutang', 
            '3' => 'Modal',
            '4' => 'Pendapatan',
            '5' => 'Beban'
        ];

        $faculties = [
            '0' => 'Unit Pusat',
            '1' => 'Fakultas Syariah',
            '2' => 'Fakultas Teknik',
            '3' => 'Fakultas Ekonomi',
            '4' => 'Fakultas Tarbiyah',
            '5' => 'Fakultas Hukum',
        ];

        return view('pages.accounts.create', compact('parentAccounts', 'accountTypes', 'faculties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:accounts,code|regex:/^[1-5][1-2][0-9][0-9][0-9][0-9][0-9]/',
            'name' => 'required|max:255',
            'normal_balance' => 'required|in:debit,kredit',
            'is_header' => 'boolean',
        ]);

        try {
            $accountData = $request->all();
            
            // Parse code using service
            $parsedData = $this->accountService->parseAccountCode($request->code);
            $accountData = array_merge($accountData, $parsedData);
            
            // Set additional fields
            $accountData['level'] = $this->accountService->calculateLevel($request->code);
            $accountData['is_header'] = $request->boolean('is_header');
            $accountData['parent_code'] = $this->accountService->determineParentCode($request->code);
            $accountData['can_transaction'] = !$accountData['is_header'];
            $accountData['is_active'] = true;
            $accountData['created_by'] = auth()->id();
            $accountData['updated_by'] = auth()->id();

            $account = accounts::create($accountData);
            
            // Update full path
            $account->full_path = $this->accountService->buildFullPath($account);
            $account->save();
            
            // Rebuild hierarchy cache
            $this->accountService->rebuildHierarchyCache();

            return redirect()->route('accounts.index')
                ->with('success', 'Akun berhasil dibuat!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat akun: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(accounts $account)
    {
        $children = $account->children()->orderBy('code')->get();
        $transactions = $account->journalDetails()->with('journal')->latest()->limit(10)->get();

        return view('pages.accounts.show', compact('account', 'children', 'transactions'));
    }

    public function edit(accounts $account)
    {
        $parentAccounts = accounts::where('is_header', true)
            ->where('id', '!=', $account->id)
            ->orderBy('code')
            ->get();

        return view('pages.accounts.edit', compact('account', 'parentAccounts'));
    }

    public function update(Request $request, accounts $account)
    {
        $request->validate([
            'name' => 'required|max:255',
            'normal_balance' => 'required|in:debit,kredit',
            'is_active' => 'boolean',
        ]);

        $account->update([
            'name' => $request->name,
            'description' => $request->description,
            'normal_balance' => $request->normal_balance,
            'is_active' => $request->boolean('is_active'),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('accounts.show', $account)
            ->with('success', 'Akun berhasil diperbarui!');
    }

    public function destroy(accounts $account)
    {
        if ($account->journalDetails()->exists()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus akun yang sudah memiliki transaksi!');
        }

        if ($account->children()->exists()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus akun yang memiliki anak!');
        }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Akun berhasil dihapus!');
    }

    public function hierarchy(accounts $account)
    {
        $descendants = $this->accountService->getDescendants($account->code);
        
        return view('pages.accounts.hierarchy', compact('account', 'descendants'));
    }
}