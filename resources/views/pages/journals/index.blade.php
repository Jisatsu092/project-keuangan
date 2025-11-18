@extends('layouts.app')

@section('title', 'Buat Journal Entry')

@section('content')
<div class="px-4 sm:px-0" x-data="journalForm()">
    
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Buat Journal Entry Baru</h2>
        <p class="mt-1 text-sm text-gray-600">Input transaksi dengan sistem double-entry bookkeeping</p>
    </div>

    <!-- Form -->
    <form action="{{ route('journals.store') }}" method="POST" @submit="validateBalance">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Header Info -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Journal</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Journal Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            No. Journal <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="journal_number" value="{{ $journalNumber }}" required readonly
                               class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-mono">
                    </div>

                    <!-- Transaction Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Transaksi <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="transaction_date" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea name="description" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                  placeholder="Deskripsi transaksi">{{ old('description') }}</textarea>
                    </div>

                    <!-- Document Reference -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            No. Dokumen Referensi
                        </label>
                        <input type="text" name="document_reference" value="{{ old('document_reference') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="Contoh: INV-2024-001">
                    </div>
                </div>
            </div>

            <!-- Balance Summary (Sticky) -->
            <div class="bg-white rounded-lg shadow p-6 lg:sticky lg:top-4 h-fit">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded">
                        <span class="text-sm font-medium text-gray-700">Total Debit</span>
                        <span class="text-lg font-bold text-green-600" x-text="formatCurrency(totalDebit)">Rp 0</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded">
                        <span class="text-sm font-medium text-gray-700">Total Kredit</span>
                        <span class="text-lg font-bold text-blue-600" x-text="formatCurrency(totalCredit)">Rp 0</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 rounded"
                         :class="isBalanced ? 'bg-green-100' : 'bg-red-100'">
                        <span class="text-sm font-medium text-gray-700">Selisih</span>
                        <span class="text-lg font-bold" 
                              :class="isBalanced ? 'text-green-600' : 'text-red-600'"
                              x-text="formatCurrency(Math.abs(totalDebit - totalCredit))">Rp 0</span>
                    </div>

                    <div class="pt-3 border-t border-gray-200">
                        <div class="flex items-center" :class="isBalanced ? 'text-green-600' : 'text-red-600'">
                            <i class="fas mr-2" :class="isBalanced ? 'fa-check-circle' : 'fa-exclamation-triangle'"></i>
                            <span class="font-semibold" x-text="isBalanced ? 'BALANCE ✓' : 'NOT BALANCED!'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Journal Details (Dynamic Rows) -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="bg-gray-800 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">Detail Transaksi</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-8">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase" style="min-width: 300px;">
                                Akun <span class="text-red-500">*</span>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase" style="min-width: 200px;">
                                Deskripsi
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase" style="min-width: 150px;">
                                Debit (Rp) <span class="text-red-500">*</span>
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase" style="min-width: 150px;">
                                Kredit (Rp) <span class="text-red-500">*</span>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(row, index) in rows" :key="row.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-500" x-text="index + 1"></td>
                                
                                <!-- Account Select -->
                                <td class="px-4 py-3">
                                    <select :name="'account_codes[' + index + ']'" x-model="row.account_code" required
                                            @change="onAccountChange(index)"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                        <option value="">Pilih Akun...</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->code }}">
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- Description -->
                                <td class="px-4 py-3">
                                    <input type="text" :name="'detail_descriptions[' + index + ']'" x-model="row.description"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                           placeholder="Deskripsi detail">
                                </td>

                                <!-- Debit -->
                                <td class="px-4 py-3">
                                    <input type="number" :name="'debits[' + index + ']'" x-model.number="row.debit" 
                                           @input="calculateTotals(); clearOpposite(index, 'debit')"
                                           min="0" step="0.01" required
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded text-right focus:ring-2 focus:ring-blue-500"
                                           placeholder="0.00">
                                </td>

                                <!-- Kredit -->
                                <td class="px-4 py-3">
                                    <input type="number" :name="'credits[' + index + ']'" x-model.number="row.credit" 
                                           @input="calculateTotals(); clearOpposite(index, 'credit')"
                                           min="0" step="0.01" required
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded text-right focus:ring-2 focus:ring-blue-500"
                                           placeholder="0.00">
                                </td>

                                <!-- Delete Button -->
                                <td class="px-4 py-3 text-center">
                                    <button type="button" @click="removeRow(index)" 
                                            x-show="rows.length > 2"
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Add Row Button (Large) -->
            <div class="bg-gray-50 px-6 py-6 border-t-2 border-gray-200">
                <button type="button" @click="addRow()" 
                        class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg transition transform hover:scale-105">
                    <i class="fas fa-plus-circle text-2xl mr-2"></i>
                    <span class="text-lg">Tambah Baris Transaksi</span>
                </button>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('journals.index') }}" 
               class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-times mr-2"></i> Batal
            </a>
            <button type="submit" 
                    :disabled="!isBalanced"
                    :class="isBalanced ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'"
                    class="px-6 py-3 text-white rounded-lg transition">
                <i class="fas fa-save mr-2"></i> Simpan Journal
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function journalForm() {
    return {
        rows: [
            { id: 1, account_code: '', description: '', debit: 0, credit: 0 },
            { id: 2, account_code: '', description: '', debit: 0, credit: 0 }
        ],
        nextId: 3,
        totalDebit: 0,
        totalCredit: 0,
        isBalanced: false,

        addRow() {
            this.rows.push({
                id: this.nextId++,
                account_code: '',
                description: '',
                debit: 0,
                credit: 0
            });
        },

        removeRow(index) {
            if (this.rows.length > 2) {
                this.rows.splice(index, 1);
                this.calculateTotals();
            }
        },

        clearOpposite(index, type) {
            // Kalau user input debit, clear kredit (dan sebaliknya)
            if (type === 'debit' && this.rows[index].debit > 0) {
                this.rows[index].credit = 0;
            } else if (type === 'credit' && this.rows[index].credit > 0) {
                this.rows[index].debit = 0;
            }
        },

        calculateTotals() {
            this.totalDebit = this.rows.reduce((sum, row) => sum + (parseFloat(row.debit) || 0), 0);
            this.totalCredit = this.rows.reduce((sum, row) => sum + (parseFloat(row.credit) || 0), 0);
            this.isBalanced = Math.abs(this.totalDebit - this.totalCredit) < 0.01;
        },

        formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount);
        },

        validateBalance(e) {
            if (!this.isBalanced) {
                e.preventDefault();
                alert('Journal tidak balance! Total Debit harus sama dengan Total Kredit.');
                return false;
            }
        },

        onAccountChange(index) {
            // Future: Auto-fill description or normal balance
        }
    }
}
</script>
@endpush
@endsection