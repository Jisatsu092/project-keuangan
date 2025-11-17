@extends('layouts.app')

@section('title', 'Tambah Akun Baru')

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
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500">Tambah Baru</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Tambah Akun Baru</h2>
        <p class="mt-1 text-sm text-gray-600">Input kode akun 7 digit atau lebih</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-lg" x-data="accountForm()">
        <form action="{{ route('accounts.store') }}" method="POST">
            @csrf

            <div class="p-6 space-y-6">
                <!-- Code Generator (Helper) -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-900 mb-4">
                        <i class="fas fa-calculator mr-2"></i> Helper: Generate Kode
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-7 gap-2 mb-4">
                        <!-- Digit 1 -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">D1: Tipe</label>
                            <select x-model="digit1" @change="updateCode" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                <option value="">-</option>
                                @foreach($accountTypes as $type)
                                    <option value="{{ $type->code }}">{{ $type->code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Digit 2 -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">D2: Ops</label>
                            <select x-model="digit2" @change="updateCode" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                <option value="">-</option>
                                @foreach($operations as $op)
                                    <option value="{{ $op->code }}">{{ $op->code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Digit 3 -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">D3: Fak</label>
                            <select x-model="digit3" @change="updateCode(); loadUnits()" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                <option value="0">0 (Pusat)</option>
                                @foreach($faculties as $fac)
                                    <option value="{{ $fac->code }}">{{ $fac->code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Digit 4 -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">D4: Unit</label>
                            <select x-model="digit4" @change="updateCode" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                <option value="0">0</option>
                                <template x-for="i in 9" :key="i">
                                    <option :value="i" x-text="i"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Digit 5 -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">D5: Act</label>
                            <select x-model="digit5" @change="updateCode" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                <option value="0">0</option>
                                @foreach($activityTypes as $act)
                                    <option value="{{ $act->code }}">{{ $act->code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Digit 6 -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">D6</label>
                            <select x-model="digit6" @change="updateCode" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                <template x-for="i in 10" :key="i-1">
                                    <option :value="i-1" x-text="i-1"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Digit 7 -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">D7</label>
                            <select x-model="digit7" @change="updateCode" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                <template x-for="i in 10" :key="i-1">
                                    <option :value="i-1" x-text="i-1"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Generated Code Preview -->
                    <div class="bg-white border border-gray-300 rounded p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Kode yang dihasilkan:</p>
                                <p class="text-2xl font-mono font-bold text-blue-600" x-text="generatedCode || '-'"></p>
                            </div>
                            <button type="button" @click="copyToInput" 
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                                <i class="fas fa-copy mr-2"></i> Copy ke Form
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Manual Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Akun <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" x-model="manualCode" required
                           pattern="[0-9]{7,20}" maxlength="20"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg font-mono @error('code') border-red-500 @enderror"
                           placeholder="Contoh: 5102403">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle"></i> Minimal 7 digit, maksimal 20 digit (numeric only)
                    </p>
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Akun <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Contoh: BBM Kendaraan Dinas Keuangan">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Deskripsi detail akun (opsional)">{{ old('description') }}</textarea>
                </div>

                <!-- Normal Balance -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Saldo Normal <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                            <input type="radio" name="normal_balance" value="debit" {{ old('normal_balance', 'debit') == 'debit' ? 'checked' : '' }} required class="mr-3">
                            <div>
                                <p class="font-medium text-gray-900">Debit</p>
                                <p class="text-xs text-gray-500">Aset, Beban</p>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                            <input type="radio" name="normal_balance" value="kredit" {{ old('normal_balance') == 'kredit' ? 'checked' : '' }} class="mr-3">
                            <div>
                                <p class="font-medium text-gray-900">Kredit</p>
                                <p class="text-xs text-gray-500">Hutang, Modal, Pendapatan</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Active Status -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">
                        Aktif
                    </label>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-lg">
                <a href="{{ route('accounts.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function accountForm() {
    return {
        digit1: '',
        digit2: '',
        digit3: '0',
        digit4: '0',
        digit5: '0',
        digit6: '0',
        digit7: '0',
        generatedCode: '',
        manualCode: '',

        updateCode() {
            this.generatedCode = this.digit1 + this.digit2 + this.digit3 + this.digit4 + this.digit5 + this.digit6 + this.digit7;
        },

        copyToInput() {
            this.manualCode = this.generatedCode;
        },

        loadUnits() {
            // Future: Ajax load units based on faculty
        }
    }
}
</script>
@endpush
@endsection