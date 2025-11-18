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
        <button @click="toggleForm()" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition">
            <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus'" class="mr-2"></i> 
            <span x-text="showForm ? 'Tutup Form' : 'Tambah Akun'"></span>
        </button>
    </div>

    <!-- Inline Create Form (Collapsible) -->
    <div x-show="showForm" x-collapse class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-lg p-6 mb-6 border border-blue-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-plus-circle text-blue-600 mr-2"></i> Tambah Akun Baru
        </h3>
        
        <form action="{{ route('accounts.store') }}" method="POST">
            @csrf

            <!-- Code Generator Helper -->
            <div class="bg-white rounded-lg p-4 mb-4 border border-gray-200">
                <p class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-calculator text-blue-600 mr-2"></i> Generator Kode (7 Digit)
                </p>
                
                <div class="grid grid-cols-2 md:grid-cols-7 gap-2 mb-3">
                    <!-- Digit 1: Account Type -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">D1: Tipe</label>
                        <select x-model="form.digit1" @change="updateCode" 
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="0">0</option>
                            @foreach($accountTypes as $type)
                                <option value="{{ $type->code }}">{{ $type->code }} - {{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Digit 2: Operation -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">D2: Ops</label>
                        <select x-model="form.digit2" @change="updateCode" 
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="0">0</option>
                            @foreach($operations as $op)
                                <option value="{{ $op->code }}">{{ $op->code }} - {{ $op->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Digit 3: Faculty -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">D3: Fak</label>
                        <select x-model="form.digit3" @change="updateCode(); loadUnits()" 
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="0">0 - Pusat</option>
                            @foreach($faculties as $fac)
                                <option value="{{ $fac->code }}">{{ $fac->code }} - {{ $fac->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Digit 4: Unit -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">D4: Unit</label>
                        <select x-model="form.digit4" @change="updateCode" 
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="0">0</option>
                            <template x-if="form.digit3 === '0'">
                                @foreach($unitsPusat as $unit)
                                    <option value="{{ $unit->code }}">{{ $unit->code }} - {{ $unit->name }}</option>
                                @endforeach
                            </template>
                            <template x-for="i in 9" :key="i">
                                <option :value="i" x-text="i"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Digit 5: Activity -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">D5: Act</label>
                        <select x-model="form.digit5" @change="updateCode" 
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            @foreach($activityTypes as $act)
                                <option value="{{ $act->code }}">{{ $act->code }} - {{ Str::limit($act->name, 10) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Digit 6 -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">D6</label>
                        <select x-model="form.digit6" @change="updateCode" 
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <template x-for="i in 10" :key="i-1">
                                <option :value="i-1" x-text="i-1"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Digit 7 -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">D7</label>
                        <select x-model="form.digit7" @change="updateCode" 
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <template x-for="i in 10" :key="i-1">
                                <option :value="i-1" x-text="i-1"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Generated Code Preview -->
                <div class="flex items-center justify-between bg-gray-50 border border-gray-300 rounded p-3">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Kode yang dihasilkan:</p>
                        <p class="text-2xl font-mono font-bold text-blue-600" x-text="generatedCode || '0000000'"></p>
                    </div>
                    <button type="button" @click="copyToManual" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
                        <i class="fas fa-copy mr-1"></i> Copy
                    </button>
                </div>
            </div>

            <!-- Manual Input Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Code (Manual) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Akun <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" x-model="form.manualCode" required
                           pattern="[0-9]{7,20}" maxlength="20"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg font-mono"
                           placeholder="7-20 digit">
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Akun <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Nama akun">
                </div>

                <!-- Normal Balance -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Saldo Normal <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="normal_balance" value="debit" checked class="mr-2">
                            <span>Debit</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="normal_balance" value="kredit" class="mr-2">
                            <span>Kredit</span>
                        </label>
                    </div>
                </div>

                <!-- Active Status -->
                <div class="flex items-center pt-7">
                    <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 text-blue-600 rounded mr-2">
                    <label class="text-sm text-gray-900">Aktif</label>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                <textarea name="description" rows="2"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          placeholder="Deskripsi detail akun"></textarea>
            </div>

            <!-- Buttons -->
            <div class="mt-4 flex gap-2">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
                <button type="button" @click="toggleForm()" 
                        class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg">
                    <i class="fas fa-times mr-2"></i> Batal
                </button>
            </div>
        </form>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('accounts.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search"></i> Cari
                </label>
                <input type="text" name="search" value="{{ $search }}" 
                       placeholder="Kode atau Nama"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Filter Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua</option>
                    @foreach($accountTypes as $type)
                        <option value="{{ $type->code }}" @selected($filterType == $type->code)>
                            {{ $type->code }} - {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Faculty -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fakultas</label>
                <select name="faculty" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua</option>
                    <option value="0" @selected($filterFaculty === '0')>0 - Pusat</option>
                    @foreach($faculties as $fac)
                        <option value="{{ $fac->code }}" @selected($filterFaculty == $fac->code)>
                            {{ $fac->code }} - {{ $fac->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    Cari
                </button>
                <a href="{{ route('accounts.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table (sama kayak sebelumnya, gua skip biar gak kepanjangan) -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($accounts as $account)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono text-sm">{{ $account->code }}</td>
                    <td class="px-6 py-4 text-sm">{{ $account->name }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($account->accountType)
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                {{ $account->accountType->name }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">Level {{ $account->level }}</td>
                    <td class="px-6 py-4 text-right text-sm">
                        <a href="{{ route('accounts.show', $account) }}" class="text-blue-600 hover:text-blue-900 mr-2">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('accounts.edit', $account) }}" class="text-yellow-600 hover:text-yellow-900 mr-2">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        Tidak ada data
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $accounts->links() }}</div>
</div>

@push('scripts')
<script>
function accountsPage() {
    return {
        showForm: false,
        form: {
            digit1: '0',
            digit2: '0',
            digit3: '0',
            digit4: '0',
            digit5: '0',
            digit6: '0',
            digit7: '0',
            manualCode: ''
        },
        generatedCode: '',

        toggleForm() {
            this.showForm = !this.showForm;
            if (this.showForm) {
                this.resetForm();
            }
        },

        resetForm() {
            this.form = {
                digit1: '0',
                digit2: '0',
                digit3: '0',
                digit4: '0',
                digit5: '0',
                digit6: '0',
                digit7: '0',
                manualCode: ''
            };
            this.updateCode();
        },

        updateCode() {
            this.generatedCode = this.form.digit1 + this.form.digit2 + this.form.digit3 + 
                                this.form.digit4 + this.form.digit5 + this.form.digit6 + this.form.digit7;
        },

        copyToManual() {
            this.form.manualCode = this.generatedCode;
        },

        loadUnits() {
            // Future: Dynamic load units via AJAX based on faculty
        }
    }
}
</script>
@endpush
@endsection