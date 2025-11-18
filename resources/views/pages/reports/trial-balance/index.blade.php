@extends('layouts.app')

@section('title', 'Trial Balance')

@section('content')
<div class="px-4 sm:px-0" x-data="trialBalanceApp()">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Trial Balance</h2>
            <p class="mt-1 text-sm text-gray-600">Neraca Percobaan dengan Hierarchy</p>
        </div>
    </div>

    <!-- Period & Controls -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Period Selector -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <select x-model="year" @change="loadReport()" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <select x-model="month" @change="loadReport()" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $idx => $monthName)
                        <option value="{{ $idx + 1 }}">{{ $monthName }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Display -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tampilkan</label>
                <select x-model="displayMode" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="all">Semua Akun</option>
                    <option value="header">Header Only</option>
                    <option value="detail">Detail Only</option>
                </select>
            </div>

            <!-- Max Level -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max Level</label>
                <select x-model="maxLevel" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="99">All Levels</option>
                    <option value="1">Level 1</option>
                    <option value="2">Level 2</option>
                    <option value="3">Level 3</option>
                    <option value="4">Level 4</option>
                    <option value="5">Level 5</option>
                </select>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 flex gap-2">
            <button @click="expandAll()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                <i class="fas fa-expand mr-1"></i> Expand All
            </button>
            <button @click="collapseAll()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm">
                <i class="fas fa-compress mr-1"></i> Collapse All
            </button>
            <button onclick="window.print()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm">
                <i class="fas fa-print mr-1"></i> Print
            </button>
        </div>
    </div>

    <!-- Balance Status -->
    <div class="mb-6 p-4 rounded-lg" :class="report.is_balanced ? 'bg-green-100 border border-green-500' : 'bg-red-100 border border-red-500'">
        <div class="flex items-center">
            <i class="fas text-2xl mr-3" :class="report.is_balanced ? 'fa-check-circle text-green-600' : 'fa-exclamation-triangle text-red-600'"></i>
            <div>
                <p class="font-semibold" :class="report.is_balanced ? 'text-green-800' : 'text-red-800'" x-text="report.is_balanced ? 'Balance OK ✓' : 'NOT BALANCED!'"></p>
                <p class="text-sm" :class="report.is_balanced ? 'text-green-600' : 'text-red-600'">
                    <span>Total Debit: Rp <span x-text="formatNumber(report.total_debit)"></span></span>
                    <span class="mx-2">|</span>
                    <span>Total Kredit: Rp <span x-text="formatNumber(report.total_credit)"></span></span>
                </p>
            </div>
        </div>
    </div>

    <!-- Search Box -->
    <div class="mb-4">
        <input type="text" x-model="searchQuery" @input="filterAccounts()" 
               placeholder="🔍 Cari akun..." 
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg">
    </div>

    <!-- Report Table (Expandable Hierarchy) -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold uppercase">Akun</th>
                    <th class="px-6 py-4 text-right text-sm font-bold uppercase" style="width: 200px;">Debit (Rp)</th>
                    <th class="px-6 py-4 text-right text-sm font-bold uppercase" style="width: 200px;">Kredit (Rp)</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="account in filteredAccounts" :key="account.code">
                    <tr x-show="shouldShowAccount(account)" 
                        class="hover:bg-gray-50 transition"
                        :class="getRowClass(account)">
                        
                        <!-- Account Name with Indent -->
                        <td class="px-6 py-3">
                            <div class="flex items-center" :style="'padding-left: ' + ((account.level - 1) * 24) + 'px'">
                                <!-- Expand/Collapse Button -->
                                <button x-show="account.is_header && account.has_children" 
                                        @click="toggleExpand(account.code)"
                                        type="button"
                                        class="mr-2 text-gray-600 hover:text-blue-600 focus:outline-none">
                                    <i class="fas" :class="account.expanded ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                </button>
                                
                                <!-- Icon -->
                                <i class="fas mr-2" 
                                   :class="account.is_header ? 'fa-folder text-yellow-600' : 'fa-file text-blue-500'"></i>
                                
                                <!-- Code & Name -->
                                <div>
                                    <span class="font-mono text-sm" :class="account.is_header ? 'font-bold' : 'font-medium'" x-text="account.code"></span>
                                    <span class="ml-2" :class="account.is_header ? 'font-bold' : ''" x-text="account.name"></span>
                                    
                                    <!-- Badge -->
                                    <span x-show="account.is_header" class="ml-2 px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold">
                                        TOTAL
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Debit -->
                        <td class="px-6 py-3 text-right">
                            <span :class="account.is_header ? 'font-bold text-lg' : 'font-medium'" x-text="account.debit > 0 ? formatNumber(account.debit) : '-'"></span>
                        </td>

                        <!-- Kredit -->
                        <td class="px-6 py-3 text-right">
                            <span :class="account.is_header ? 'font-bold text-lg' : 'font-medium'" x-text="account.credit > 0 ? formatNumber(account.credit) : '-'"></span>
                        </td>
                    </tr>
                </template>

                <!-- Empty State -->
                <tr x-show="filteredAccounts.length === 0">
                    <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-5xl mb-4 text-gray-300"></i>
                        <p>Tidak ada data untuk periode ini</p>
                    </td>
                </tr>
            </tbody>

            <!-- Footer Total -->
            <tfoot class="bg-gray-100 font-bold text-lg">
                <tr>
                    <td class="px-6 py-4 text-right uppercase">TOTAL:</td>
                    <td class="px-6 py-4 text-right text-blue-600" x-text="formatNumber(report.total_debit)"></td>
                    <td class="px-6 py-4 text-right text-blue-600" x-text="formatNumber(report.total_credit)"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            nav, footer, button, .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</div>

@push('scripts')
<script>
function trialBalanceApp() {
    return {
        year: {{ $year }},
        month: {{ $month }},
        displayMode: 'all',
        maxLevel: 99,
        searchQuery: '',
        
        report: @json($report),
        
        accounts: @json($report['data'] ?? []),
        filteredAccounts: [],
        expandedAccounts: {},

        init() {
            this.processAccounts();
            this.filterAccounts();
            // Default: expand level 1-2
            this.accounts.forEach(acc => {
                if (acc.level <= 2) {
                    this.expandedAccounts[acc.code] = true;
                    acc.expanded = true;
                }
            });
        },

        processAccounts() {
            // Add hierarchy info
            this.accounts.forEach(acc => {
                acc.has_children = this.accounts.some(child => child.code.startsWith(acc.code) && child.code !== acc.code);
                acc.expanded = false;
            });
        },

        toggleExpand(code) {
            const account = this.accounts.find(a => a.code === code);
            if (account) {
                account.expanded = !account.expanded;
                this.expandedAccounts[code] = account.expanded;
            }
        },

        expandAll() {
            this.accounts.forEach(acc => {
                acc.expanded = true;
                this.expandedAccounts[acc.code] = true;
            });
        },

        collapseAll() {
            this.accounts.forEach(acc => {
                acc.expanded = false;
                this.expandedAccounts[acc.code] = false;
            });
        },

        shouldShowAccount(account) {
            // Filter by display mode
            if (this.displayMode === 'header' && !account.is_header) return false;
            if (this.displayMode === 'detail' && account.is_header) return false;

            // Filter by level
            if (account.level > this.maxLevel) return false;

            // Check if parent is expanded
            if (account.level > 1) {
                const parentCode = this.findParentCode(account.code);
                if (parentCode && !this.expandedAccounts[parentCode]) {
                    return false;
                }
            }

            return true;
        },

        findParentCode(code) {
            // Find parent by removing last non-zero digit
            for (let i = code.length - 1; i >= 0; i--) {
                if (code[i] !== '0') {
                    const parentCode = code.substring(0, i) + '0' + code.substring(i + 1);
                    if (this.accounts.some(a => a.code === parentCode)) {
                        return parentCode;
                    }
                    return this.findParentCode(parentCode);
                }
            }
            return null;
        },

        filterAccounts() {
            if (!this.searchQuery) {
                this.filteredAccounts = this.accounts;
                return;
            }

            const query = this.searchQuery.toLowerCase();
            this.filteredAccounts = this.accounts.filter(acc => 
                acc.code.toLowerCase().includes(query) || 
                acc.name.toLowerCase().includes(query)
            );

            // Auto-expand parents of matched accounts
            this.filteredAccounts.forEach(acc => {
                let parentCode = this.findParentCode(acc.code);
                while (parentCode) {
                    this.expandedAccounts[parentCode] = true;
                    const parent = this.accounts.find(a => a.code === parentCode);
                    if (parent) parent.expanded = true;
                    parentCode = this.findParentCode(parentCode);
                }
            });
        },

        getRowClass(account) {
            if (account.is_header) {
                if (account.level === 1) return 'bg-blue-50 border-t-2 border-blue-500';
                if (account.level === 2) return 'bg-gray-50';
                return 'bg-gray-50';
            }
            return '';
        },

        formatNumber(num) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        },

        loadReport() {
            window.location.href = `?year=${this.year}&month=${this.month}`;
        }
    }
}
</script>
@endpush
@endsection