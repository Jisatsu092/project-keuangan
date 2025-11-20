@extends('layouts.app')

@section('title', 'Daftar Akun')

@section('content')
<div class="px-4 sm:px-0" x-data="accountsPage()">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Daftar Akun</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola Chart of Accounts (CoA) kampus</p>
        </div>
        <div class="flex gap-2">
            <button @click="expandAll()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm">
                <i class="fas fa-expand-alt mr-2"></i> Expand All
            </button>
            <button @click="collapseAll()" class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded-lg text-sm">
                <i class="fas fa-compress-alt mr-2"></i> Collapse All
            </button>
            <button @click="toggleForm()" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition">
                <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus'" class="mr-2"></i> 
                <span x-text="showForm ? 'Tutup Form' : 'Tambah Akun'"></span>
            </button>
        </div>
    </div>

    <!-- Inline Form -->
    <div x-show="showForm" x-collapse class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-lg p-6 mb-6 border border-blue-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-plus-circle text-blue-600 mr-2"></i> Tambah Akun Baru
        </h3>
        
        <form action="{{ route('accounts.store') }}" method="POST">
            @csrf

            <div class="bg-white rounded-lg p-4 mb-4 border border-gray-200">
                <p class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-calculator text-blue-600 mr-2"></i> Generator Kode (7 Digit)
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-4">
                    <!-- D1: Account Type -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">D1: Tipe Akun</label>
                        <select name="digit_1" x-model="form.digit1" @change="loadOperations(); updateCode()" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded">
                            <option value="">Pilih</option>
                            @foreach($accountTypes as $type)
                                <option value="{{ $type->code }}">{{ $type->code }} - {{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- D2: Operation/Sub-Category -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">D2: Sub-Kategori</label>
                        <select name="digit_2" x-model="form.digit2" @change="checkLocationRestriction(); updateCode()" required
                                :disabled="!form.digit1"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded disabled:bg-gray-100">
                            <option value="">Pilih</option>
                            <template x-for="(group, key) in operationsGrouped" :key="key">
                                <optgroup :label="group.label">
                                    <template x-for="op in group.items" :key="op.code">
                                        <option :value="op.code" x-text="op.display" :data-category="op.category_type"></option>
                                    </template>
                                </optgroup>
                            </template>
                        </select>
                    </div>

                    <!-- D3-D4: Faculty + Unit -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">D3-D4: Fakultas → Unit</label>
                        <select name="faculty_unit_code" x-model="form.facultyUnitCode" @change="updateCode()" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded">
                            <option value="">Pilih Fakultas/Unit</option>
                            
                            <!-- Unit Pusat -->
                            <optgroup label="🏛️ Unit Pusat" x-show="allowPusat">
                                @foreach($unitsPusat as $unit)
                                    <option value="0{{ $unit->code }}">0{{ $unit->code }} - Pusat → {{ $unit->name }}</option>
                                @endforeach
                            </optgroup>

                            <!-- Biro -->
                            <optgroup label="🏢 Biro" x-show="allowPusat">
                                <option value="51">51 - Biro → Unit 1</option>
                                <option value="52">52 - Biro → Unit 2</option>
                            </optgroup>

                            <!-- Fakultas -->
                            <template x-if="allowFakultas">
                                @foreach($faculties as $fac)
                                    <optgroup label="🎓 {{ $fac->name }}">
                                        @foreach($fac->units as $unit)
                                            <option value="{{ $fac->code }}{{ $unit->code }}">{{ $fac->code }}{{ $unit->code }} - {{ $fac->name }} → {{ $unit->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </template>
                        </select>
                        <p class="text-xs text-gray-500 mt-1" x-show="locationHint" x-text="locationHint"></p>
                    </div>

                    <!-- D5: Activity -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">D5: Aktivitas</label>
                        <select name="digit_5" x-model="form.digit5" @change="updateCode()" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded">
                            <option value="">Pilih</option>
                            @foreach($activityTypes as $act)
                                <option value="{{ $act->code }}">{{ $act->code }} - {{ $act->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Sequence Mode -->
                <div class="border-t border-gray-200 pt-4">
                    <p class="text-xs font-semibold text-gray-700 mb-2">
                        <i class="fas fa-hashtag text-purple-600 mr-1"></i> D6-D7: Detail Sequence
                    </p>

                    <div class="flex gap-4 mb-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sequence_mode" value="auto" x-model="form.sequenceMode" @change="updateCode()" checked class="mr-2">
                            <span class="text-sm">🤖 Auto</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sequence_mode" value="manual" x-model="form.sequenceMode" @change="updateCode()" class="mr-2">
                            <span class="text-sm">✍️ Manual</span>
                        </label>
                    </div>

                    <div x-show="form.sequenceMode === 'manual'" x-collapse class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Digit 6</label>
                            <select name="digit_6" x-model="form.digit6" @change="updateCode()" class="w-full px-2 py-2 text-sm border border-gray-300 rounded">
                                <template x-for="i in 10" :key="i-1">
                                    <option :value="i-1" x-text="i-1"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Digit 7</label>
                            <select name="digit_7" x-model="form.digit7" @change="updateCode()" class="w-full px-2 py-2 text-sm border border-gray-300 rounded">
                                <template x-for="i in 10" :key="i-1">
                                    <option :value="i-1" x-text="i-1"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div x-show="form.sequenceMode === 'auto'" class="bg-green-50 border border-green-200 rounded p-2 text-xs text-green-800">
                        <i class="fas fa-info-circle"></i> System akan auto-generate sequence
                    </div>
                </div>

                <!-- Code Preview -->
                <div class="mt-4 bg-gray-50 border border-gray-300 rounded p-3">
                    <p class="text-xs text-gray-500 mb-1">Kode yang akan dibuat:</p>
                    <p class="text-2xl font-mono font-bold text-blue-600" x-text="generatedCode || '0000000'"></p>
                </div>
            </div>

            <!-- Account Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Akun <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg" placeholder="Contoh: Kas Bank BCA">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Saldo Normal</label>
                    <div class="flex gap-4">
                        <label class="flex items-center"><input type="radio" name="normal_balance" value="debit" checked class="mr-2"><span>Debit</span></label>
                        <label class="flex items-center"><input type="radio" name="normal_balance" value="kredit" class="mr-2"><span>Kredit</span></label>
                    </div>
                </div>

                <div class="flex items-center pt-7">
                    <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded mr-2">
                    <label class="text-sm">Aktif</label>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
            </div>

            <div class="mt-4 flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
                <button type="button" @click="toggleForm()" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg">
                    <i class="fas fa-times mr-2"></i> Batal
                </button>
            </div>
        </form>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-search"></i> Cari</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Kode atau Nama" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua</option>
                    @foreach($accountTypes as $type)
                        <option value="{{ $type->code }}">{{ $type->code }} - {{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fakultas</label>
                <select name="faculty" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua</option>
                    <option value="0">0 - Pusat</option>
                    @foreach($faculties as $fac)
                        <option value="{{ $fac->code }}">{{ $fac->code }} - {{ $fac->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg"><i class="fas fa-filter mr-2"></i> Filter</button>
                <a href="{{ route('accounts.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg"><i class="fas fa-redo mr-2"></i> Reset</a>
            </div>
        </form>
    </div>

    <!-- Simplified Display (Option B) -->
    <div class="space-y-4">
        @forelse($groupedAccounts as $typeCode => $typeData)
        <div class="bg-white rounded-lg shadow-md border-l-4 border-blue-600">
            
            <!-- Level 1: Account Type -->
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-150 cursor-pointer"
                 @click="toggle('type_{{ $typeCode }}')">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-600 text-white rounded-lg">
                        <span class="text-2xl font-bold">{{ $typeCode }}</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $typeData['name'] }}</h3>
                        <p class="text-sm text-gray-600">{{ $typeData['count'] }} akun</p>
                    </div>
                </div>
                <i class="fas fa-chevron-down text-gray-600 transition-transform text-xl" :class="isExpanded('type_{{ $typeCode }}') && 'rotate-180'"></i>
            </div>

            <div x-show="isExpanded('type_{{ $typeCode }}')" x-collapse>
                @php
                    $lastCategoryType = null;
                @endphp

                @foreach($typeData['operations'] as $opKey => $opData)
                    @php
                        $categoryType = $opData['category_type'];
                    @endphp

                    {{-- Category Label (only for grouped types like Beban) --}}
                    @if($categoryType && $categoryType !== $lastCategoryType)
                        <div class="bg-gray-100 px-6 py-2 border-t border-gray-200">
                            <p class="text-sm font-semibold text-gray-700 uppercase">
                                @if($categoryType == 'operasional') 🔵 OPERASIONAL
                                @elseif($categoryType == 'program') 🟣 PROGRAM
                                @elseif($categoryType == 'hibah') 🎁 HIBAH
                                @elseif($categoryType == 'donasi') 💝 DONASI
                                @elseif($categoryType == 'umum') 📋 UMUM
                                @endif
                            </p>
                        </div>
                        @php $lastCategoryType = $categoryType; @endphp
                    @endif

                    {{-- Level 2: Operation/Sub-Category --}}
                    <div class="border-t border-gray-200">
                        <div class="flex items-center justify-between p-3 pl-8 bg-gray-50 hover:bg-gray-100 cursor-pointer"
                             @click="toggle('op_{{ $typeCode }}_{{ $opKey }}')">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 bg-blue-100 text-blue-800 rounded flex items-center justify-center font-bold text-sm">{{ $opData['code'] }}</span>
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $opData['name'] }}</h4>
                                    <p class="text-xs text-gray-500">{{ $opData['count'] }} akun</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 transition-transform" :class="isExpanded('op_{{ $typeCode }}_{{ $opKey }}') && 'rotate-180'"></i>
                        </div>

                        {{-- Level 3: Accounts Table --}}
                        <div x-show="isExpanded('op_{{ $typeCode }}_{{ $opKey }}')" x-collapse>
                            <div class="bg-white">
                                <table class="min-w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Saldo</th>
                                            <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($opData['accounts'] as $account)
                                        <tr class="hover:bg-blue-50">
                                            <td class="px-6 py-3 font-mono text-sm font-bold text-blue-600">{{ $account->code }}</td>
                                            <td class="px-6 py-3 text-sm">{{ $account->name }}</td>
                                            <td class="px-6 py-3 text-xs text-gray-600">
                                                {{ $account->digit_3 == '0' ? 'Pusat' : ($account->faculty->name ?? 'Fakultas ' . $account->digit_3) }}
                                                → {{ $account->unit->name ?? 'Unit ' . $account->digit_4 }}
                                            </td>
                                            <td class="px-6 py-3 text-sm">
                                                <span class="px-2 py-1 rounded text-xs {{ $account->normal_balance == 'debit' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                                    {{ ucfirst($account->normal_balance) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3 text-right space-x-2">
                                                <a href="{{ route('accounts.show', $account) }}" class="text-blue-600 hover:text-blue-900"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('accounts.edit', $account) }}" class="text-yellow-600 hover:text-yellow-900"><i class="fas fa-edit"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">Tidak ada data akun</p>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function accountsPage() {
    return {
        showForm: false,
        expanded: {},
        operationsGrouped: {},
        form: {
            digit1: '',
            digit2: '',
            facultyUnitCode: '',
            digit5: '',
            sequenceMode: 'auto',
            digit6: '0',
            digit7: '0',
            categoryType: null
        },
        generatedCode: '',
        locationHint: '',
        allowPusat: true,
        allowFakultas: true,

        toggleForm() {
            this.showForm = !this.showForm;
        },

        toggle(key) {
            this.expanded[key] = !this.expanded[key];
        },

        isExpanded(key) {
            return this.expanded[key] === true;
        },

        expandAll() {
            document.querySelectorAll('[\\@click^="toggle("]').forEach(el => {
                const match = el.getAttribute('@click').match(/toggle\('([^']+)'\)/);
                if (match) this.expanded[match[1]] = true;
            });
        },

        collapseAll() {
            this.expanded = {};
        },

        async loadOperations() {
            if (!this.form.digit1) return;
            
            try {
                const response = await fetch(`/accounts/operations-by-type?type=${this.form.digit1}`);
                this.operationsGrouped = await response.json();
                this.form.digit2 = '';
            } catch (error) {
                console.error('Error loading operations:', error);
            }
        },

        checkLocationRestriction() {
            const select = document.querySelector('select[name="digit_2"]');
            const selectedOption = select?.options[select.selectedIndex];
            this.form.categoryType = selectedOption?.getAttribute('data-category');

            if (this.form.categoryType === 'operasional') {
                this.allowPusat = true;
                this.allowFakultas = false;
                this.locationHint = '⚠️ Operasional hanya untuk Pusat/Biro';
            } else if (this.form.categoryType === 'program') {
                this.allowPusat = false;
                this.allowFakultas = true;
                this.locationHint = '⚠️ Program hanya untuk Fakultas';
            } else {
                this.allowPusat = true;
                this.allowFakultas = true;
                this.locationHint = '';
            }

            // Reset location if not allowed
            if (this.form.facultyUnitCode) {
                const digit3 = this.form.facultyUnitCode[0];
                if (!this.allowPusat && ['0', '5'].includes(digit3)) {
                    this.form.facultyUnitCode = '';
                }
                if (!this.allowFakultas && !['0', '5'].includes(digit3)) {
                    this.form.facultyUnitCode = '';
                }
            }
        },

        updateCode() {
            const seq = this.form.sequenceMode === 'auto' ? 'XX' : (this.form.digit6 || '0') + (this.form.digit7 || '0');
            this.generatedCode = (this.form.digit1 || '0') + (this.form.digit2 || '0') + (this.form.facultyUnitCode || '00') + (this.form.digit5 || '0') + seq;
        }
    }
}
</script>
@endpush
@endsection