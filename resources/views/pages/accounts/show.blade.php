@extends('layouts.app')

@section('title', 'Detail Akun - ' . $account->code)

@section('content')
<div class="px-4 sm:px-0">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('accounts.index') }}" class="text-gray-700 hover:text-blue-600">
                    <i class="fas fa-home mr-2"></i> Akun
                </a>
            </li>
            @foreach($breadcrumb as $crumb)
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500 text-sm">{{ $crumb['code'] }}</span>
                </div>
            </li>
            @endforeach
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h2 class="text-3xl font-bold text-gray-900 font-mono">{{ $account->code }}</h2>
                @if($account->is_header)
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-folder mr-1"></i> Header
                    </span>
                @endif
                @if(!$account->is_active)
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-ban mr-1"></i> Nonaktif
                    </span>
                @endif
            </div>
            <h3 class="text-xl text-gray-700">{{ $account->name }}</h3>
            @if($account->description)
                <p class="mt-2 text-gray-600">{{ $account->description }}</p>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('accounts.edit', $account) }}" 
               class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <form action="{{ route('accounts.destroy', $account) }}" method="POST" 
                  onsubmit="return confirm('Yakin hapus akun ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">
                    <i class="fas fa-trash mr-2"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Account Properties -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i> Informasi Akun
                </h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Tipe Akun</p>
                        <p class="font-semibold">{{ $account->accountType->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Normal Balance</p>
                        <p class="font-semibold">
                            <span class="px-2 py-1 rounded text-white text-sm
                                @if($account->normal_balance == 'debit') bg-green-500 @else bg-blue-500 @endif">
                                {{ strtoupper($account->normal_balance) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Operasional/Program</p>
                        <p class="font-semibold">{{ $account->operation->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Level Hierarki</p>
                        <p class="font-semibold">Level {{ $account->level }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Fakultas</p>
                        <p class="font-semibold">
                            @if($account->faculty)
                                {{ $account->faculty->name }}
                            @elseif($account->digit_3 == '0')
                                Pusat/Rektorat
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Unit/Prodi</p>
                        <p class="font-semibold">{{ $account->unit->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Digit Breakdown -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-code text-purple-600 mr-2"></i> Breakdown Digit
                </h4>
                <div class="grid grid-cols-7 gap-2">
                    @foreach(['digit_1', 'digit_2', 'digit_3', 'digit_4', 'digit_5', 'digit_6', 'digit_7'] as $index => $digit)
                    <div class="text-center">
                        <div class="bg-blue-100 rounded-lg p-3 mb-2">
                            <p class="text-2xl font-bold text-blue-600 font-mono">
                                {{ $account->{$digit} ?? '0' }}
                            </p>
                        </div>
                        <p class="text-xs text-gray-500">Digit {{ $index + 1 }}</p>
                    </div>
                    @endforeach
                </div>
                @if($account->digit_extra)
                <div class="mt-4 p-3 bg-gray-100 rounded">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-plus-circle mr-1"></i> 
                        <strong>Extra Digits:</strong> {{ $account->digit_extra }}
                    </p>
                </div>
                @endif
            </div>

            <!-- Recent Transactions -->
            @if($recentTransactions->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-history text-green-600 mr-2"></i> Transaksi Terakhir
                </h4>
                <div class="space-y-3">
                    @foreach($recentTransactions as $detail)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                        <div>
                            <p class="font-semibold">{{ $detail->journal->journal_number }}</p>
                            <p class="text-sm text-gray-500">{{ $detail->journal->transaction_date->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            @if($detail->debit > 0)
                                <p class="text-green-600 font-semibold">Rp {{ number_format($detail->debit, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">Debit</p>
                            @else
                                <p class="text-blue-600 font-semibold">Rp {{ number_format($detail->credit, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">Kredit</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Parent Account -->
            @if($account->parent)
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-level-up-alt text-gray-600 mr-2"></i> Parent Account
                </h4>
                <a href="{{ route('accounts.show', $account->parent) }}" 
                   class="block p-3 bg-gray-50 hover:bg-gray-100 rounded transition">
                    <p class="font-mono font-bold text-blue-600">{{ $account->parent->code }}</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $account->parent->name }}</p>
                </a>
            </div>
            @endif

            <!-- Children Accounts -->
            @if($account->children->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-sitemap text-gray-600 mr-2"></i> Child Accounts
                    <span class="text-sm font-normal text-gray-500">({{ $account->children->count() }})</span>
                </h4>
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($account->children as $child)
                    <a href="{{ route('accounts.show', $child) }}" 
                       class="block p-3 bg-gray-50 hover:bg-gray-100 rounded transition">
                        <p class="font-mono text-sm font-bold text-blue-600">{{ $child->code }}</p>
                        <p class="text-xs text-gray-700 mt-1">{{ $child->name }}</p>
                        @if($child->is_header)
                            <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded mt-1 inline-block">
                                Header
                            </span>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-bar text-blue-600 mr-2"></i> Quick Stats
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Can Transaction</span>
                        <span class="font-semibold">
                            @if($account->can_transaction)
                                <i class="fas fa-check-circle text-green-500"></i> Yes
                            @else
                                <i class="fas fa-times-circle text-red-500"></i> No
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status</span>
                        <span class="font-semibold">
                            @if($account->is_active)
                                <span class="text-green-600">Active</span>
                            @else
                                <span class="text-red-600">Inactive</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Children Count</span>
                        <span class="font-semibold">{{ $account->children->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection