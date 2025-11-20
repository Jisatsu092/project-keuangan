@extends('layouts.app')

@section('title', 'Master Data - Sub Kategori Akun')

@section('content')
<div class="px-4 sm:px-0" x-data="operationsPage()">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Master Data - Sub Kategori Akun</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola sub-kategori (Digit 2) untuk setiap tipe akun</p>
        </div>
        <div class="flex gap-2">
            <button @click="showHeaderForm = true" 
                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
                <i class="fas fa-folder-plus mr-2"></i> Tambah Header
            </button>
            <button @click="toggleForm()" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition">
                <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus'" class="mr-2"></i> 
                <span x-text="showForm ? 'Tutup' : 'Tambah Kode'"></span>
            </button>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                @foreach($accountTypes as $type)
                <a href="?tab={{ $type->code }}" 
                   class="py-4 px-6 text-center border-b-2 font-medium text-sm transition {{ $activeTab == $type->code ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <span class="inline-flex items-center">
                        <span class="w-8 h-8 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center mr-2 font-bold">{{ $type->code }}</span>
                        {{ $type->name }}
                    </span>
                </a>
                @endforeach
            </nav>
        </div>
    </div>

    <!-- Header Form (Modal-like) -->
    <div x-show="showHeaderForm" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tambah Header Grouping</h3>
            
            <form action="{{ route('master.operations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="account_type_code" value="{{ $activeTab }}">
                <input type="hidden" name="is_header" value="1">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Header <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Contoh: OPERASIONAL">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Parent Category <span class="text-red-500">*</span></label>
                    <input type="text" name="parent_category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="operasional/program/hibah/donasi">
                    <p class="text-xs text-gray-500 mt-1">Untuk grouping di form & display</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                    <input type="number" name="sort_order" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
                        <i class="fas fa-save mr-2"></i> Simpan Header
                    </button>
                    <button type="button" @click="showHeaderForm = false" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Detail Form -->
    <div x-show="showForm" x-collapse class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4" x-text="editMode ? 'Edit Sub-Kategori' : 'Tambah Sub-Kategori (Kode)'"></h3>
        
        <form :action="editMode ? '/master/operations/' + editId : '{{ route('master.operations.store') }}'" method="POST">
            @csrf
            <input type="hidden" x-show="editMode" name="_method" value="PUT">
            <input type="hidden" name="account_type_code" value="{{ $activeTab }}">
            <input type="hidden" name="is_header" value="0">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode <span class="text-red-500">*</span></label>
                    <select name="code" x-model="form.code" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Pilih</option>
                        @for($i = 1; $i <= 9; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="form.name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Program KKN">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Parent Category</label>
                    <input type="text" name="parent_category" x-model="form.parent_category" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="operasional/program">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ada grouping</p>
                </div>

                <div class="flex items-end">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" x-model="form.is_active" class="h-4 w-4 text-blue-600 rounded mr-2">
                        <span class="text-sm">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" x-model="form.description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
            </div>

            <div class="mt-4 flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
                <button type="button" @click="toggleForm()" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">
                    <i class="fas fa-times mr-2"></i> Batal
                </button>
            </div>
        </form>
    </div>

    <!-- Operations List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @php
            $currentParent = null;
        @endphp

        @forelse($operations as $op)
            @if($op->is_header)
                {{-- Header --}}
                @if($currentParent)
                    </tbody></table></div>
                @endif
                <div class="bg-gradient-to-r from-gray-100 to-gray-200 px-6 py-3 border-b border-gray-300">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-gray-800 uppercase text-sm">{{ $op->name }}</h4>
                        <div class="flex gap-2">
                            <button type="button" @click="editOperation({{ $op->id }}, null, '{{ addslashes($op->name) }}', '{{ $op->parent_category }}', {{ $op->is_active ? 'true' : 'false' }}, true)" 
                                    class="text-yellow-600 hover:text-yellow-900">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('master.operations.destroy', $op) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus header ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                <div><table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Kode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                @php $currentParent = $op->parent_category; @endphp
            @else
                {{-- Detail Row --}}
                @if(!$currentParent && $loop->first)
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                @endif

                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono font-bold text-blue-600 text-lg">{{ $op->code }}</td>
                    <td class="px-6 py-4 font-semibold">{{ $op->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $op->description ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if($op->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs"><i class="fas fa-check-circle"></i> Aktif</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs"><i class="fas fa-times-circle"></i> Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button type="button" @click="editOperation({{ $op->id }}, '{{ $op->code }}', '{{ addslashes($op->name) }}', '{{ $op->parent_category }}', {{ $op->is_active ? 'true' : 'false' }}, false)" 
                                class="text-yellow-600 hover:text-yellow-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('master.operations.destroy', $op) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endif

            @if($loop->last && $currentParent)
                </tbody></table></div>
            @elseif($loop->last && !$currentParent)
                </tbody></table>
            @endif
        @empty
        <div class="p-12 text-center text-gray-500">
            <i class="fas fa-inbox text-6xl mb-4"></i>
            <p>Belum ada sub-kategori</p>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function operationsPage() {
    return {
        showForm: false,
        showHeaderForm: false,
        editMode: false,
        editId: null,
        form: {
            code: '',
            name: '',
            parent_category: '',
            is_active: true
        },

        toggleForm() {
            this.showForm = !this.showForm;
            if (this.showForm) {
                this.editMode = false;
                this.resetForm();
            }
        },

        editOperation(id, code, name, parentCategory, isActive, isHeader) {
            if (isHeader) {
                // Edit header via modal (simplified)
                alert('Edit header: Redirect to dedicated form');
                return;
            }
            
            this.editMode = true;
            this.editId = id;
            this.showForm = true;
            this.form = { code, name, parent_category: parentCategory, is_active: isActive };
        },

        resetForm() {
            this.form = { code: '', name: '', parent_category: '', is_active: true };
        }
    }
}
</script>
@endpush
@endsection