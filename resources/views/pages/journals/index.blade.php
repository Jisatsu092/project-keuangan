@extends('layouts.app')

@section('title', 'Daftar Journal Entry')

@section('content')
<div class="px-4 sm:px-0">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Daftar Journal Entry</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola semua journal entry</p>
        </div>
        <a href="{{ route('journals.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition">
            <i class="fas fa-plus mr-2"></i> Buat Journal
        </a>
    </div>

    <!-- Filter dan Search -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form action="{{ route('journals.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                           placeholder="No. Journal atau Deskripsi...">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
                        <option value="void" {{ request('status') == 'void' ? 'selected' : '' }}>Void</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('journals.index') }}" class="ml-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md">
                        <i class="fas fa-refresh mr-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Journal Cards -->
    <div class="space-y-4" x-data="{ 
        expandedCard: null,
        toggleCard(cardId) {
            this.expandedCard = this.expandedCard === cardId ? null : cardId;
        }
    }">
        @forelse($journals as $journal)
        @php
            $totalDebit = $journal->details->sum('debit');
            $totalCredit = $journal->details->sum('credit');
            $isBalanced = abs($totalDebit - $totalCredit) < 0.01;
        @endphp
        
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-md">
            <!-- Card Header -->
            <div class="p-4 cursor-pointer hover:bg-gray-50" 
                 @click="toggleCard('journal-{{ $journal->id }}')">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Status Indicator -->
                        <div class="flex-shrink-0">
                            @if($journal->status === 'draft')
                                <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                            @elseif($journal->status === 'posted')
                                <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                            @else
                                <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                            @endif
                        </div>

                        <!-- Journal Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $journal->journal_number }}
                                </h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($journal->status === 'draft') bg-yellow-100 text-yellow-800
                                    @elseif($journal->status === 'posted') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ strtoupper($journal->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $journal->description ?: 'No description' }}
                            </p>
                            <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                <span>
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $journal->transaction_date->format('d M Y') }}
                                </span>
                                <span>
                                    <i class="fas fa-user mr-1"></i>
                                    {{ $journal->createdBy->name ?? 'System' }}
                                </span>
                                @if($journal->document_reference)
                                <span>
                                    <i class="fas fa-file-alt mr-1"></i>
                                    {{ $journal->document_reference }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Amount Summary -->
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">
                                Rp {{ number_format($totalDebit, 2) }}
                            </div>
                            <div class="text-xs text-gray-500 flex items-center justify-end">
                                <span class="w-2 h-2 rounded-full mr-1 
                                    @if($isBalanced) bg-green-400 @else bg-red-400 @endif"></span>
                                {{ $isBalanced ? 'BALANCED' : 'NOT BALANCED' }}
                            </div>
                        </div>

                        <!-- Expand Icon -->
                        <div class="flex-shrink-0 transform transition-transform duration-300" 
                             :class="{ 'rotate-180': expandedCard === 'journal-{{ $journal->id }}' }">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expandable Content -->
            <div x-show="expandedCard === 'journal-{{ $journal->id }}'" 
                 x-collapse
                 class="border-t border-gray-200 bg-gray-50">
                <div class="p-4">
                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div class="bg-white rounded-lg p-3 text-center shadow-sm">
                            <div class="text-2xl font-bold text-blue-600">{{ $journal->details->count() }}</div>
                            <div class="text-xs text-gray-600">Entries</div>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center shadow-sm">
                            <div class="text-2xl font-bold text-green-600">{{ $journal->details->unique('account_code')->count() }}</div>
                            <div class="text-xs text-gray-600">Akun</div>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center shadow-sm">
                            <div class="text-lg font-bold text-purple-600">Rp {{ number_format($totalDebit, 2) }}</div>
                            <div class="text-xs text-gray-600">Total Debit</div>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center shadow-sm">
                            <div class="text-lg font-bold text-indigo-600">Rp {{ number_format($totalCredit, 2) }}</div>
                            <div class="text-xs text-gray-600">Total Kredit</div>
                        </div>
                    </div>

                    <!-- Details Table -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-4">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">Akun</th>
                                    <th class="px-4 py-3">Nama Akun</th>
                                    <th class="px-4 py-3">Deskripsi</th>
                                    <th class="px-4 py-3 text-right">Debit (Rp)</th>
                                    <th class="px-4 py-3 text-right">Kredit (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($journal->details as $detail)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <span class="font-mono text-blue-600 font-semibold">{{ $detail->account_code }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-gray-900">{{ $detail->account->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-gray-600">{{ $detail->description ?: '-' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if($detail->debit > 0)
                                            <span class="text-green-600 font-medium">{{ number_format($detail->debit, 2) }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if($detail->credit > 0)
                                            <span class="text-blue-600 font-medium">{{ number_format($detail->credit, 2) }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-800 text-white font-medium">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right">Total</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($totalDebit, 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($totalCredit, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center">
                        <div class="text-xs text-gray-500">
                            Created: {{ $journal->created_at->format('d M Y H:i') }}
                            @if($journal->posted_at)
                                • Posted: {{ $journal->posted_at->format('d M Y H:i') }}
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('journals.show', $journal) }}" 
                               class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md transition">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                            
                            @if($journal->status === 'draft')
                                <form action="{{ route('journals.post', $journal) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            onclick="return confirm('Post journal ini? Setelah dipost tidak bisa diedit.')"
                                            class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded-md transition">
                                        <i class="fas fa-check mr-1"></i> Post
                                    </button>
                                </form>
                                
                                <a href="{{ route('journals.edit', $journal) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-sm rounded-md transition">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                            @endif

                            @if($journal->status !== 'void')
                                <form action="{{ route('journals.void', $journal) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            onclick="return confirm('Yakin void journal ini?')"
                                            class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md transition">
                                        <i class="fas fa-ban mr-1"></i> Void
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500">Belum ada journal entry</p>
            <a href="{{ route('journals.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                <i class="fas fa-plus mr-2"></i> Buat Journal Pertama
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $journals->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Smooth transition for collapse */
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
    // Auto-close other cards when one is opened (optional)
    document.addEventListener('alpine:init', () => {
        Alpine.data('journalCards', () => ({
            expandedCard: null,
            toggleCard(cardId) {
                this.expandedCard = this.expandedCard === cardId ? null : cardId;
            }
        }));
    });
</script>
@endpush