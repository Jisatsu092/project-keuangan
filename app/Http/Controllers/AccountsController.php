<?php
// app/Http/Controllers/AccountController.php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\account_types;
use App\Models\operations;
use App\Models\Faculties;
use App\Models\units;
use App\Models\activity_types;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountsController extends Controller
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * Display listing dengan tree view
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filterType = $request->get('type');
        $filterFaculty = $request->get('faculty');

        $query = accounts::with(['accountType', 'operation', 'faculty', 'unit'])
            ->orderBy('code');

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        // Filter by account type
        if ($filterType) {
            $query->where('digit_1', $filterType);
        }

        // Filter by faculty
        if ($filterFaculty !== null) {
            $query->where('digit_3', $filterFaculty);
        }

        $accounts = $query->paginate(50);

        // Data untuk filter dropdown
        $accountTypes = account_types::active()->get();
        $faculties = Faculties::active()->get();

        return view('pages.accounts.index', compact('accounts', 'accountTypes', 'faculties', 'search', 'filterType', 'filterFaculty'));
    }

    /**
     * Show tree view (Ajax)
     */
    public function tree(Request $request)
    {
        $parentCode = $request->get('parent');
        $maxLevel = $request->get('max_level');

        $tree = $this->accountService->getAccountTree($parentCode, $maxLevel);

        return response()->json($tree);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $accountTypes = account_types::active()->get();
        $operations = operations::active()->get();
        $faculties = Faculties::active()->get();
        $unitsPusat = units::unitPusat()->active()->get();
        $activityTypes = activity_types::active()->get();

        return view('accounts.create', compact('accountTypes', 'operations', 'faculties', 'unitsPusat', 'activityTypes'));
    }

    /**
     * Store new account
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|min:7|max:20|unique:accounts,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'normal_balance' => 'required|in:debit,kredit',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $account = accounts::create([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'normal_balance' => $validated['normal_balance'],
                'is_active' => $validated['is_active'] ?? true,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('accounts.show', $account)
                ->with('success', 'Akun berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal membuat akun: ' . $e->getMessage());
        }
    }

    /**
     * Show detail account
     */
    public function show(accounts $account)
    {
        $account->load(['accountType', 'operation', 'faculty', 'unit', 'activityType', 'parent', 'children']);

        // Get breadcrumb (hierarchy path)
        $breadcrumb = $account->breadcrumb;

        // Get recent transactions (5 terakhir)
        $recentTransactions = $account->journalDetails()
            ->with('journal')
            ->latest()
            ->take(5)
            ->get();

        return view('accounts.show', compact('account', 'breadcrumb', 'recentTransactions'));
    }

    /**
     * Show edit form
     */
    public function edit(accounts $account)
    {
        $accountTypes = account_types::active()->get();
        $operations = operations::active()->get();
        $faculties = Faculties::active()->get();
        $unitsPusat = units::unitPusat()->active()->get();
        $activityTypes = activity_types::active()->get();

        return view('accounts.edit', compact('account', 'accountTypes', 'operations', 'faculties', 'unitsPusat', 'activityTypes'));
    }

    /**
     * Update account
     */
    public function update(Request $request, accounts $account)
    {
        $validated = $request->validate([
            'code' => 'required|string|min:7|max:20|unique:accounts,code,' . $account->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'normal_balance' => 'required|in:debit,kredit',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $account->update([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'normal_balance' => $validated['normal_balance'],
                'is_active' => $validated['is_active'] ?? true,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('accounts.show', $account)
                ->with('success', 'Akun berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal update akun: ' . $e->getMessage());
        }
    }

    /**
     * Delete account
     */
    public function destroy(accounts $account)
    {
        // Check apakah punya children
        if ($account->children()->exists()) {
            return back()->with('error', 'Tidak bisa hapus akun yang masih punya child accounts!');
        }

        // Check apakah ada transaksi
        if ($account->journalDetails()->exists()) {
            return back()->with('error', 'Tidak bisa hapus akun yang sudah ada transaksinya!');
        }

        try {
            DB::beginTransaction();

            $account->delete();

            DB::commit();

            return redirect()
                ->route('accounts.index')
                ->with('success', 'Akun berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus akun: ' . $e->getMessage());
        }
    }

    /**
     * Get units by faculty (Ajax)
     */
    public function getUnitsByFaculty(Request $request)
    {
        $facultyId = $request->get('faculty_id');

        if ($facultyId) {
            $units = units::where('faculty_id', $facultyId)->active()->get();
        } else {
            $units = units::unitPusat()->active()->get();
        }

        return response()->json($units);
    }

    /**
     * Generate code preview (Ajax)
     */
    public function generateCodePreview(Request $request)
    {
        $components = [
            $request->get('digit_1'),
            $request->get('digit_2'),
            $request->get('digit_3'),
            $request->get('digit_4'),
            $request->get('digit_5'),
            $request->get('digit_6'),
            $request->get('digit_7'),
        ];

        $code = $this->accountService->generateCode($components);

        $exists = $this->accountService->codeExists($code);

        return response()->json([
            'code' => $code,
            'exists' => $exists,
            'valid' => $this->accountService->validateCode($code),
        ]);
    }
}