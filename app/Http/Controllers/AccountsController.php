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

        $query = Accounts::with(['accountType', 'operation', 'faculty', 'unit', 'activityType'])
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

        // Simplified grouping: Type → Operation (with category label)
        $groupedAccounts = [];

        foreach ($accounts as $account) {
            $type = $account->digit_1;
            $opCode = $account->digit_2;

            // Initialize type
            if (!isset($groupedAccounts[$type])) {
                $groupedAccounts[$type] = [
                    'name' => $account->accountType->name ?? "Tipe {$type}",
                    'count' => 0,
                    'operations' => [],
                ];
            }

            // Get operation details
            $operation = operations::where('account_type_code', $type)
                ->where('code', $opCode)
                ->first();

            $categoryType = $operation->category_type ?? null;
            $opName = $operation->name ?? "Operasi {$opCode}";

            // Group key: operation code + category type (untuk distinguish bentrok)
            $opKey = $opCode . '_' . ($categoryType ?? 'default');

            // Initialize operation
            if (!isset($groupedAccounts[$type]['operations'][$opKey])) {
                $groupedAccounts[$type]['operations'][$opKey] = [
                    'code' => $opCode,
                    'name' => $opName,
                    'category_type' => $categoryType,
                    'count' => 0,
                    'accounts' => [],
                ];
            }

            // Add account
            $groupedAccounts[$type]['operations'][$opKey]['accounts'][] = $account;
            $groupedAccounts[$type]['operations'][$opKey]['count']++;
            $groupedAccounts[$type]['count']++;
        }

        // Sort operations by category_type (operasional first, then program)
        foreach ($groupedAccounts as &$typeData) {
            $typeData['operations'] = collect($typeData['operations'])->sortBy(function ($op) {
                $order = ['operasional' => 1, 'program' => 2, 'hibah' => 3, 'donasi' => 4, 'umum' => 5];
                return $order[$op['category_type']] ?? 99;
            })->toArray();
        }

        // Master data
        $accountTypes = account_types::active()->get();
        $allOperations = operations::active()->get()->groupBy('account_type_code');
        $faculties = faculties::with('units')->active()->get();
        $unitsPusat = units::unitPusat()->active()->get();
        $activityTypes = activity_types::active()->get();

        return view('pages.accounts.index', compact(
            'groupedAccounts',
            'accountTypes',
            'allOperations',
            'faculties',
            'unitsPusat',
            'activityTypes',
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
            'digit_2' => 'required|string|size:1',
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

        $operation = operations::where('account_type_code', $validated['digit_1'])
            ->where('code', $validated['digit_2'])
            ->first();

        if ($operation && $operation->category_type === 'program' && $digit3 === '0') {
            return back()->withInput()->with('error', 'Program hanya tersedia untuk Fakultas (bukan Pusat)!');
        }

        if ($operation && $operation->category_type === 'operasional' && !in_array($digit3, ['0', '5'])) {
            return back()->withInput()->with('error', 'Operasional hanya untuk Pusat atau Biro!');
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

    public function getOperationsByType(Request $request)
    {
        $accountTypeCode = $request->get('type');

        $operations = operations::where('account_type_code', $accountTypeCode)
            ->where('is_active', true)
            ->orderBy('category_type')
            ->orderBy('code')
            ->get();

        // Group by category_type untuk dropdown
        $grouped = $operations->groupBy('category_type')->map(function ($items, $key) {
            $labels = [
                'operasional' => '🔵 Operasional',
                'program' => '🟣 Program',
                'hibah' => '🎁 Hibah',
                'donasi' => '💝 Donasi',
                'umum' => '📋 Umum',
            ];

            return [
                'label' => $labels[$key] ?? '📋 Lainnya',
                'items' => $items->map(fn($op) => [
                    'code' => $op->code,
                    'name' => $op->name,
                    'category_type' => $op->category_type,
                    'display' => "{$op->code} - {$op->name}",
                ])
            ];
        });

        return response()->json($grouped);
    }
}
