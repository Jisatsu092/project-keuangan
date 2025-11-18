@extends('layouts.app')

@section('title', 'Detail Journal Entry - ' . $journal->journal_number)

@section('content')
<div class="px-4 sm:px-0">
    
    <!-- Header dengan Status -->
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Detail Journal Entry</h2>
                <p class="mt-1 text-sm text-gray-600">No. Journal: {{ $journal->journal_number }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Status Badge -->
                @if($journal->status === 'draft')
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-edit mr-1"></i> DRAFT
                    </span>
                @elseif($journal->status === 'posted')
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-check-circle mr-1"></i> POSTED
                    </span>
                @else
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-ban mr-1"></i> VOID
                    </span>
                @endif

                <!-- Action Buttons -->
                <div class="flex space-x-2">
                    @if($journal->status === 'draft')
                        <form action="{{ route('journals.post', $journal) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Post journal ini? Setelah dipost tidak bisa diedit.')"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm">
                                <i class="fas fa-check mr-1"></i> Post
                            </button>
                        </form>
                    @endif

                    @if($journal->status !== 'void')
                        <form action="{{ route('journals.void', $journal) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Yakin void journal ini?')"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm">
                                <i class="fas fa-ban mr-1"></i> Void
                            </button>
                        </form>
                    @endif

                    @if($journal->status === 'draft')
                        <a href="{{ route('journals.edit', $journal) }}" 
                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                    @endif

                    <a href="{{ route('journals.index') }}" 
                       class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Journal Info -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Journal</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Journal</label>
                    <p class="text-lg font-mono font-bold text-blue-600">{{ $journal->journal_number }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Transaksi</label>
                    <p class="text-lg text-gray-900">{{ \Carbon\Carbon::parse($journal->transaction_date)->format('d F Y') }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <p class="text-gray-900">{{ $journal->description ?: '-' }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Dokumen Referensi</label>
                    <p class="text-gray-900">{{ $journal->document_reference ?: '-' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dibuat Oleh</label>
                    <p class="text-gray-900">{{ $journal->createdBy->name ?? 'System' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dibuat</label>
                    <p class="text-gray-900">{{ $journal->created_at->format('d F Y H:i') }}</p>
                </div>

                @if($journal->posted_at)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diposting Oleh</label>
                    <p class="text-gray-900">{{ $journal->postedBy->name ?? 'System' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Posting</label>
                    <p class="text-gray-900">{{ $journal->posted_at->format('d F Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Balance Summary -->
        <div class="bg-white rounded-lg shadow p-6 h-fit">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan</h3>
            
            @php
                $totalDebit = $journal->details->sum('debit');
                $totalCredit = $journal->details->sum('credit');
                $isBalanced = abs($totalDebit - $totalCredit) < 0.01;
            @endphp

            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-green-50 rounded">
                    <span class="text-sm font-medium text-gray-700">Total Debit</span>
                    <span class="text-lg font-bold text-green-600">Rp {{ number_format($totalDebit, 2) }}</span>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded">
                    <span class="text-sm font-medium text-gray-700">Total Kredit</span>
                    <span class="text-lg font-bold text-blue-600">Rp {{ number_format($totalCredit, 2) }}</span>
                </div>
                
                <div class="flex justify-between items-center p-3 rounded {{ $isBalanced ? 'bg-green-100' : 'bg-red-100' }}">
                    <span class="text-sm font-medium text-gray-700">Selisih</span>
                    <span class="text-lg font-bold {{ $isBalanced ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format(abs($totalDebit - $totalCredit), 2) }}
                    </span>
                </div>

                <div class="pt-3 border-t border-gray-200">
                    <div class="flex items-center {{ $isBalanced ? 'text-green-600' : 'text-red-600' }}">
                        <i class="fas mr-2 {{ $isBalanced ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i>
                        <span class="font-semibold">{{ $isBalanced ? 'BALANCE ✓' : 'NOT BALANCED!' }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="bg-gray-50 p-3 rounded">
                        <div class="text-2xl font-bold text-blue-600">{{ $journal->details->count() }}</div>
                        <div class="text-xs text-gray-600">Total Entries</div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <div class="text-2xl font-bold text-purple-600">{{ $journal->details->unique('account_code')->count() }}</div>
                        <div class="text-xs text-gray-600">Akun Terlibat</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Journal Details -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gray-800 px-6 py-4">
            <h3 class="text-lg font-semibold text-white">Detail Transaksi</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akun</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Akun</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit (Rp)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($journal->details as $index => $detail)
                    <tr class="hover:bg-gray-50 {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-3 text-sm text-gray-500 text-center">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-blue-600 font-semibold">{{ $detail->account_code }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-900">{{ $detail->account->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">{{ $detail->description ?: '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($detail->debit > 0)
                                <span class="text-sm font-semibold text-green-600">
                                    Rp {{ number_format($detail->debit, 2) }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($detail->credit > 0)
                                <span class="text-sm font-semibold text-blue-600">
                                    Rp {{ number_format($detail->credit, 2) }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-800 text-white">
                    <tr>
                        <th colspan="4" class="px-4 py-3 text-right text-sm font-semibold">TOTAL</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold">
                            Rp {{ number_format($totalDebit, 2) }}
                        </th>
                        <th class="px-4 py-3 text-right text-sm font-semibold">
                            Rp {{ number_format($totalCredit, 2) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Audit Trail -->
    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Audit Trail</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase">Dibuat</label>
                <p class="text-gray-900">{{ $journal->created_at->format('d F Y H:i') }} oleh {{ $journal->createdBy->name ?? 'System' }}</p>
            </div>
            @if($journal->updated_at != $journal->created_at)
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase">Diupdate</label>
                <p class="text-gray-900">{{ $journal->updated_at->format('d F Y H:i') }}</p>
            </div>
            @endif
            @if($journal->posted_at)
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase">Diposting</label>
                <p class="text-gray-900">{{ $journal->posted_at->format('d F Y H:i') }} oleh {{ $journal->postedBy->name ?? 'System' }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection