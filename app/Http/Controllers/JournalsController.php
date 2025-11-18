<?php
// app/Http/Controllers/JournalsController.php

namespace App\Http\Controllers;

use App\Models\Accounts;
use App\Models\journal_details;
use App\Models\journals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = journals::with(['details', 'createdBy'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('journal_number', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $journals = $query->paginate(20);

        return view('pages.journals.index', compact('journals', 'search', 'status'));
    }

    public function create()
    {
        // Generate journal number
        $lastJournal = journals::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastJournal) {
            $lastNumber = intval(substr($lastJournal->journal_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $journalNumber = 'JV-' . now()->format('Ym') . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // Get transactionable accounts only
        $accounts = Accounts::where('can_transaction', true)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('pages.journals.create', compact('journalNumber', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'journal_number' => 'required|string|unique:journals,journal_number',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'document_reference' => 'nullable|string|max:100',
            
            // Details (arrays)
            'account_codes' => 'required|array|min:2',
            'account_codes.*' => 'required|exists:accounts,code',
            'detail_descriptions' => 'array',
            'debits' => 'required|array',
            'debits.*' => 'required|numeric|min:0',
            'credits' => 'required|array',
            'credits.*' => 'required|numeric|min:0',
        ]);

        // Validate balance
        $totalDebit = array_sum($validated['debits']);
        $totalCredit = array_sum($validated['credits']);

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            return back()
                ->withInput()
                ->with('error', "Journal tidak balance! Debit: " . number_format($totalDebit, 2) . " ≠ Kredit: " . number_format($totalCredit, 2));
        }

        try {
            DB::beginTransaction();

            // Create journal
            $journal = journals::create([
                'journal_number' => $validated['journal_number'],
                'transaction_date' => $validated['transaction_date'],
                'description' => $validated['description'],
                'document_reference' => $validated['document_reference'],
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Create journal details
            foreach ($validated['account_codes'] as $index => $accountCode) {
                $debit = $validated['debits'][$index] ?? 0;
                $credit = $validated['credits'][$index] ?? 0;

                // Skip row if both debit and credit are 0
                if ($debit == 0 && $credit == 0) {
                    continue;
                }

                journal_details::create([
                    'journal_id' => $journal->id,
                    'account_code' => $accountCode,
                    'description' => $validated['detail_descriptions'][$index] ?? null,
                    'debit' => $debit,
                    'credit' => $credit,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('pages.journals.show', $journal)
                ->with('success', 'Journal entry berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan journal: ' . $e->getMessage());
        }
    }

    public function show(journals $journal)
    {
        $journal->load(['details.account', 'createdBy', 'postedBy']);

        return view('pages.journals.show', compact('journal'));
    }

    public function post(journals $journal)
    {
        if ($journal->status !== 'draft') {
            return back()->with('error', 'Hanya journal draft yang bisa di-post!');
        }

        if (!$journal->isBalanced()) {
            return back()->with('error', 'Journal tidak balance! Tidak bisa di-post.');
        }

        try {
            DB::beginTransaction();

            $journal->update([
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Journal berhasil di-post!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal post journal: ' . $e->getMessage());
        }
    }

    public function void(journals $journal)
    {
        if ($journal->status === 'void') {
            return back()->with('error', 'Journal sudah void!');
        }

        try {
            DB::beginTransaction();

            $journal->update([
                'status' => 'void',
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Journal berhasil di-void!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal void journal: ' . $e->getMessage());
        }
    }

    public function destroy(journals $journal)
    {
        if ($journal->status === 'posted') {
            return back()->with('error', 'Tidak bisa hapus journal yang sudah di-post! Void dulu.');
        }

        try {
            DB::beginTransaction();
            $journal->delete();
            DB::commit();

            return redirect()
                ->route('pages.journals.index')
                ->with('success', 'Journal berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus journal: ' . $e->getMessage());
        }
    }
}