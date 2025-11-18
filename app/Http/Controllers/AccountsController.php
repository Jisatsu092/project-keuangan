<?php
// app/Http/Controllers/AccountsController.php

namespace App\Http\Controllers;

use App\Models\account_types;
use App\Models\Accounts;
use App\Models\AccountType;
use App\Models\activity_types;
use App\Models\Operation;
use App\Models\units;
use App\Models\faculties;
use App\Models\operations;
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

    public function index(Request $request)
    {
        $search = $request->get('search');
        $filterType = $request->get('type');
        $filterFaculty = $request->get('faculty');

        $query = Accounts::with(['accountType', 'operation', 'faculty', 'unit'])
            ->orderBy('code');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        if ($filterType) {
            $query->where('digit_1', $filterType);
        }

        if ($filterFaculty !== null) {
            $query->where('digit_3', $filterFaculty);
        }

        $accounts = $query->paginate(50);

        // Master data for filters & form
        $accountTypes = account_types::active()->get();
        $operations = operations::active()->get();
        $faculties = faculties::active()->get();
        $unitsPusat = Units::unitPusat()->active()->get();
        $activityTypes = activity_types::active()->get();

        return view('pages.accounts.index', compact(
            'accounts', 
            'accountTypes', 
            'operations',
            'faculties', 
            'unitsPusat',
            'activityTypes',
            'search', 
            'filterType', 
            'filterFaculty'
        ));
    }

    public function create()
    {
        $accountTypes = account_types::active()->get();
        $operations = operations::active()->get();
        $faculties = faculties::active()->get();
        $unitsPusat = units::unitPusat()->active()->get();
        $activityTypes = activity_types::active()->get();

        return view('pages.accounts.create', compact('accountTypes', 'operations', 'faculties', 'unitsPusat', 'activityTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|min:7|max:20|unique:accounts,code|regex:/^[0-9]+$/',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'normal_balance' => 'required|in:debit,kredit',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $account = Accounts::create([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'normal_balance' => $validated['normal_balance'],
                'is_active' => $validated['is_active'] ?? true,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Akun berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat akun: ' . $e->getMessage());
        }
    }

    public function show(Accounts $account)
    {
        $account->load(['accountType', 'operation', 'faculty', 'unit', 'activityType', 'parent', 'children']);

        $breadcrumb = $account->breadcrumb;

        $recentTransactions = $account->journalDetails()
            ->with('journal')
            ->latest()
            ->take(5)
            ->get();

        return view('pages.accounts.show', compact('account', 'breadcrumb', 'recentTransactions'));
    }

    public function edit(Accounts $account)
    {
        $accountTypes = account_types::active()->get();
        $operations = operations::active()->get();
        $faculties = faculties::active()->get();
        $unitsPusat = units::unitPusat()->active()->get();
        $activityTypes = activity_types::active()->get();

        return view('pages.accounts.edit', compact('account', 'accountTypes', 'operations', 'faculties', 'unitsPusat', 'activityTypes'));
    }

    public function update(Request $request, Accounts $account)
    {
        $validated = $request->validate([
            'code' => 'required|string|min:7|max:20|unique:accounts,code,' . $account->id . '|regex:/^[0-9]+$/',
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
                ->route('pages.accounts.show', $account)
                ->with('success', 'Akun berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update akun: ' . $e->getMessage());
        }
    }

    public function destroy(Accounts $account)
    {
        if ($account->children()->exists()) {
            return back()->with('error', 'Tidak bisa hapus akun yang masih punya child accounts!');
        }

        if ($account->journalDetails()->exists()) {
            return back()->with('error', 'Tidak bisa hapus akun yang sudah ada transaksinya!');
        }

        try {
            DB::beginTransaction();
            $account->delete();
            DB::commit();

            return redirect()
                ->route('pages.accounts.index')
                ->with('success', 'Akun berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus akun: ' . $e->getMessage());
        }
    }

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