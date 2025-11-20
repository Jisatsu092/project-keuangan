<?php
// app/Http/Controllers/OperationsController.php

namespace App\Http\Controllers;

use App\Models\operations;
use App\Models\account_types;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationsController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', '1'); // Default: Aset
        
        $accountTypes = account_types::active()->orderBy('code')->get();
        
        // Load operations untuk tab aktif
        $operations = operations::with('accountType')
            ->where('account_type_code', $activeTab)
            // ->orderBy('category_type')
            ->orderBy('code')
            ->get();
        
        // Group by category_type (untuk Beban)
        $groupedOperations = $operations->groupBy('category_type');
        
        return view('pages.master.operations.index', compact(
            'accountTypes',
            'operations',
            'groupedOperations',
            'activeTab'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_type_code' => 'required|char:1|exists:account_types,code',
            'code' => 'required|char:1|regex:/^[1-9]$/',
            'name' => 'required|string|max:100',
            'category_type' => 'nullable|in:operasional,program,hibah,donasi,umum',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Check unique constraint
        $exists = operations::where('account_type_code', $validated['account_type_code'])
            ->where('code', $validated['code'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Kode sudah digunakan untuk tipe akun ini!');
        }

        try {
            DB::beginTransaction();

            operations::create([
                'account_type_code' => $validated['account_type_code'],
                'code' => $validated['code'],
                'name' => $validated['name'],
                'category_type' => $validated['category_type'],
                'description' => $validated['description'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();
            return back()->with('success', 'Sub-kategori berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function update(Request $request, operations $operation)
    {
        $validated = $request->validate([
            'code' => 'required|char:1|regex:/^[1-9]$/',
            'name' => 'required|string|max:100',
            'category_type' => 'nullable|in:operasional,program,hibah,donasi,umum',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Check unique constraint (exclude current)
        $exists = operations::where('account_type_code', $operation->account_type_code)
            ->where('code', $validated['code'])
            ->where('id', '!=', $operation->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kode sudah digunakan!');
        }

        try {
            DB::beginTransaction();

            $operation->update([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'category_type' => $validated['category_type'],
                'description' => $validated['description'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();
            return back()->with('success', 'Sub-kategori berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy(operations $operation)
    {
        // Check if used in accounts
        if ($operation->accounts()->exists()) {
            return back()->with('error', 'Tidak bisa hapus! Sub-kategori masih digunakan di akun.');
        }

        try {
            DB::beginTransaction();
            $operation->delete();
            DB::commit();

            return back()->with('success', 'Sub-kategori berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // API: Get operations by account type (untuk form dropdown)
    public function getByAccountType(Request $request)
    {
        $accountTypeCode = $request->get('account_type_code');
        
        $operations = operations::where('account_type_code', $accountTypeCode)
            ->where('is_active', true)
            ->orderBy('category_type')
            ->orderBy('code')
            ->get();
        
        // Group by category_type
        $grouped = $operations->groupBy('category_type')->map(function($items, $key) {
            return [
                'label' => $this->getCategoryLabel($key),
                'items' => $items->map(fn($op) => [
                    'id' => $op->id,
                    'code' => $op->code,
                    'name' => $op->name,
                    'display' => "{$op->code} - {$op->name}",
                ])
            ];
        });
        
        return response()->json($grouped);
    }

    private function getCategoryLabel($type)
    {
        $labels = [
            'operasional' => '🔵 Operasional',
            'program' => '🟣 Program',
            'hibah' => '🎁 Hibah',
            'donasi' => '💝 Donasi',
            'umum' => '📋 Umum',
        ];
        
        return $labels[$type] ?? '📋 Lainnya';
    }
}