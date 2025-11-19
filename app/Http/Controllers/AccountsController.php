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
        $filterOperation = $request->get('operation');
        $filterFaculty = $request->get('faculty');

        $query = Accounts::with(['accountType', 'faculty', 'unit'])
            ->orderBy('code');

        // Apply filters
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        if ($filterType) $query->where('digit_1', $filterType);
        if ($filterOperation !== null) $query->where('digit_2', $filterOperation);
        if ($filterFaculty !== null) $query->where('digit_3', $filterFaculty);

        $accounts = $query->get();

        // Multi-level grouping
        $groupedAccounts = [];

        foreach ($accounts as $account) {
            $type = $account->digit_1;
            $operation = $account->digit_2;
            $faculty = $account->digit_3;
            $unit = $account->digit_4;
            $category = $account->digit_5;

            // Initialize structure
            if (!isset($groupedAccounts[$type])) {
                $groupedAccounts[$type] = [
                    'name' => $account->accountType->name ?? "Tipe {$type}",
                    'count' => 0,
                    'operations' => [],
                ];
            }

            if (!isset($groupedAccounts[$type]['operations'][$operation])) {
                $groupedAccounts[$type]['operations'][$operation] = [
                    'count' => 0,
                    'faculties' => [],
                ];
            }

            if (!isset($groupedAccounts[$type]['operations'][$operation]['faculties'][$faculty])) {
                $facultyName = $faculty == '0' ? 'Pusat' : ($account->faculty->name ?? "Fakultas {$faculty}");
                $groupedAccounts[$type]['operations'][$operation]['faculties'][$faculty] = [
                    'name' => $facultyName,
                    'count' => 0,
                    'units' => [],
                ];
            }

            if (!isset($groupedAccounts[$type]['operations'][$operation]['faculties'][$faculty]['units'][$unit])) {
                $unitName = $account->unit->name ?? "Unit {$unit}";
                $groupedAccounts[$type]['operations'][$operation]['faculties'][$faculty]['units'][$unit] = [
                    'name' => $unitName,
                    'count' => 0,
                    'categories' => [],
                ];
            }

            if (!isset($groupedAccounts[$type]['operations'][$operation]['faculties'][$faculty]['units'][$unit]['categories'][$category])) {
                $groupedAccounts[$type]['operations'][$operation]['faculties'][$faculty]['units'][$unit]['categories'][$category] = [];
            }

            // Add account to final array
            $groupedAccounts[$type]['operations'][$operation]['faculties'][$faculty]['units'][$unit]['categories'][$category][] = $account;

            // Increment counts
            $groupedAccounts[$type]['count']++;
            $groupedAccounts[$type]['operations'][$operation]['count']++;
            $groupedAccounts[$type]['operations'][$operation]['faculties'][$faculty]['count']++;
            $groupedAccounts[$type]['operations'][$operation]['faculties'][$faculty]['units'][$unit]['count']++;
        }

        // Master data
        $accountTypes = account_types::active()->get();
        $faculties = faculties::with('units')->active()->get();
        $unitsPusat = units::unitPusat()->active()->get();

        return view('pages.accounts.index', compact(
            'groupedAccounts',
            'accountTypes',
            'faculties',
            'unitsPusat',
            'search',
            'filterType',
            'filterOperation',
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
            'digit_1' => 'required|string|size:1',
            'digit_2' => 'required|string|size:1|in:0,1',
            'faculty_unit_code' => 'required|string|size:2',
            'digit_5' => 'required|string|size:1',
            'sequence_mode' => 'required|in:auto,manual',
            'digit_6' => 'required_if:sequence_mode,manual|nullable|string|size:1',
            'digit_7' => 'required_if:sequence_mode,manual|nullable|string|size:1',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'normal_balance' => 'required|in:debit,kredit',
            'is_active' => 'boolean',
        ]);

        // Business rule: Program hanya untuk fakultas
        $digit3 = substr($validated['faculty_unit_code'], 0, 1);
        if ($validated['digit_2'] == '1' && $digit3 == '0') {
            return back()->withInput()->with('error', 'Program hanya tersedia untuk Fakultas (bukan Pusat)!');
        }

        try {
            DB::beginTransaction();

            // Build 5-digit head
            $headCode = $validated['digit_1'] .
                $validated['digit_2'] .
                $validated['faculty_unit_code'] .
                $validated['digit_5'];

            // Handle sequence
            if ($validated['sequence_mode'] === 'auto') {
                $sequence = $this->getNextSequence($headCode);
            } else {
                $sequence = $validated['digit_6'] . $validated['digit_7'];

                $fullCode = $headCode . $sequence;
                if (Accounts::where('code', $fullCode)->exists()) {
                    DB::rollBack();
                    return back()->withInput()->with('error', "Kode {$fullCode} sudah digunakan!");
                }
            }

            $finalCode = $headCode . $sequence;

            // Parse digits for storage
            $digit3 = substr($validated['faculty_unit_code'], 0, 1);
            $digit4 = substr($validated['faculty_unit_code'], 1, 1);

            Accounts::create([
                'code' => $finalCode,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'normal_balance' => $validated['normal_balance'],
                'digit_1' => $validated['digit_1'],
                'digit_2' => $validated['digit_2'],
                'digit_3' => $digit3,
                'digit_4' => $digit4,
                'digit_5' => $validated['digit_5'],
                'is_active' => $validated['is_active'] ?? true,
                'created_by' => auth()->id(),
            ]);

            DB::commit();
            return back()->with('success', "Akun {$finalCode} berhasil dibuat!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
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

    public function checkDuplicate(Request $request)
    {
        $code = $request->get('code');
        $exists = Accounts::where('code', $code)->exists();

        return response()->json(['exists' => $exists]);
    }

    private function getNextSequence(string $headCode): string
    {
        $existingCodes = Accounts::where('code', 'LIKE', $headCode . '%')
            ->pluck('code')
            ->map(fn($code) => (int) substr($code, 5, 2))
            ->sort()
            ->values()
            ->toArray();

        for ($i = 1; $i <= 99; $i++) {
            if (!in_array($i, $existingCodes)) {
                return str_pad($i, 2, '0', STR_PAD_LEFT);
            }
        }

        throw new \Exception("Sequence penuh! Maksimal 99 akun untuk {$headCode}");
    }
}
