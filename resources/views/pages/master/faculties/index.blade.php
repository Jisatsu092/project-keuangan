@extends('layouts.app')

@section('title', 'Master Data - Fakultas & Unit')

@section('content')
<div class="px-4 sm:px-0" x-data="facultiesPage()">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Master Data - Fakultas & Unit</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola fakultas (D3) dan unit/prodi (D4) dalam satu tempat</p>
        </div>
        <button @click="toggleFacultyForm()" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition">
            <i class="fas" :class="showFacultyForm ? 'fa-times' : 'fa-plus'" class="mr-2"></i> 
            <span x-text="showFacultyForm ? 'Batal' : 'Tambah Fakultas'"></span>
        </button>
    </div>

    <!-- Faculty Form (Collapsible) -->
    <div x-show="showFacultyForm" x-collapse class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4" x-text="editFacultyMode ? 'Edit Fakultas' : 'Tambah Fakultas Baru'"></h3>
        
        <form :action="editFacultyMode ? '{{ url('master/faculties') }}/' + editFacultyId : '{{ route('master.faculties.store') }}'" method="POST">
            @csrf
            <input type="hidden" x-show="editFacultyMode" name="_method" value="PUT">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <select name="code" x-model="facultyForm.code" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Pilih Kode</option>
                        @for($i = 1; $i <= 9; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Fakultas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" x-model="facultyForm.name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                           placeholder="Contoh: Fakultas Teknik">
                </div>

                <div class="flex items-center pt-7">
                    <input type="checkbox" name="is_active" value="1" x-model="facultyForm.is_active"
                           class="h-4 w-4 text-blue-600 rounded">
                    <label class="ml-2 text-sm text-gray-900">Aktif</label>
                </div>
            </div>

            <div class="mt-4 flex gap-2">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
                <button type="button" @click="toggleFacultyForm()" 
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">
                    <i class="fas fa-times mr-2"></i> Batal
                </button>
            </div>
        </form>
    </div>

    <!-- Faculties List -->
    <div class="space-y-4">
        @forelse($faculties as $faculty)
        <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4" 
             :class="expandedFaculty === {{ $faculty->id }} ? 'border-blue-600' : 'border-gray-300'">
            
            <!-- Faculty Header -->
            <div class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 cursor-pointer"
                 @click="toggleFaculty({{ $faculty->id }})">
                <div class="flex items-center gap-4 flex-1">
                    <div class="flex items-center justify-center w-16 h-16 bg-blue-600 text-white rounded-lg">
                        <span class="text-3xl font-bold font-mono">{{ $faculty->code }}</span>
                    </div>
                    
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900">{{ $faculty->name }}</h3>
                        <p class="text-sm text-gray-500">
                            <i class="fas fa-building mr-1"></i>
                            {{ $faculty->units->count() }} Unit/Prodi
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($faculty->is_active)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">
                                <i class="fas fa-check-circle"></i> Aktif
                            </span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">
                                <i class="fas fa-times-circle"></i> Nonaktif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button"
                            @click.stop="editFaculty({{ $faculty->id }}, '{{ $faculty->code }}', '{{ addslashes($faculty->name) }}', {{ $faculty->is_active ? 'true' : 'false' }})"
                            class="text-yellow-600 hover:text-yellow-900 p-2">
                        <i class="fas fa-edit"></i>
                    </button>
                    
                    <form action="{{ route('master.faculties.destroy', $faculty) }}" 
                          method="POST" class="inline-block"
                          onsubmit="return confirm('Yakin hapus fakultas ini beserta semua unit di dalamnya?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 p-2">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>

                    <i class="fas fa-chevron-down text-gray-400 transition-transform ml-2"
                       :class="expandedFaculty === {{ $faculty->id }} ? 'rotate-180' : ''"></i>
                </div>
            </div>

            <!-- Units Section (Expandable) -->
            <div x-show="expandedFaculty === {{ $faculty->id }}" 
                 x-collapse
                 class="border-t border-gray-200">
                
                <!-- Add Unit Button -->
                <div class="p-4 bg-blue-50 border-b border-blue-100">
                    <button @click="showUnitForm = showUnitForm === {{ $faculty->id }} ? null : {{ $faculty->id }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                        <i class="fas" :class="showUnitForm === {{ $faculty->id }} ? 'fa-times' : 'fa-plus'" class="mr-2"></i>
                        <span x-text="showUnitForm === {{ $faculty->id }} ? 'Batal' : 'Tambah Unit/Prodi'"></span>
                    </button>
                </div>

                <!-- Unit Form (Collapsible) -->
                <div x-show="showUnitForm === {{ $faculty->id }}" 
                     x-collapse
                     class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100">
                    <form :action="editUnitMode ? '/master/units/' + editUnitId : '{{ route('master.units.store') }}'" method="POST">
                        @csrf
                        <input type="hidden" x-show="editUnitMode" name="_method" value="PUT">
                        <input type="hidden" name="faculty_id" value="{{ $faculty->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Kode <span class="text-red-500">*</span>
                                </label>
                                <select name="code" x-model="unitForm.code" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="">Pilih</option>
                                    @for($i = 0; $i <= 9; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" x-model="unitForm.name" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                       placeholder="Contoh: Teknik Informatika">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                                <select name="type" x-model="unitForm.type" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="prodi">Prodi</option>
                                    <option value="unit_pusat">Unit Pusat</option>
                                </select>
                            </div>

                            <div class="flex items-end gap-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1" x-model="unitForm.is_active"
                                           class="h-4 w-4 text-blue-600 rounded mr-2">
                                    <span class="text-sm">Aktif</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                                <i class="fas fa-save mr-1"></i> Simpan Unit
                            </button>
                            <button type="button" 
                                    @click="showUnitForm = null; resetUnitForm()"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Units List -->
                <div class="p-4">
                    @forelse($faculty->units as $unit)
                    <div class="flex items-center justify-between p-3 mb-2 bg-gray-50 rounded-lg hover:bg-gray-100 border border-gray-200">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-10 h-10 bg-blue-100 text-blue-700 rounded font-bold font-mono">
                                {{ $unit->code }}
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $unit->name }}</p>
                                <p class="text-xs text-gray-500">
                                    <span class="px-2 py-0.5 rounded" 
                                          :class="'{{ $unit->type }}' === 'prodi' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
                                        {{ ucfirst($unit->type) }}
                                    </span>
                                    <span class="ml-2">Kode lengkap: <strong>{{ $faculty->code }}{{ $unit->code }}</strong></span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($unit->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Aktif</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Nonaktif</span>
                            @endif

                            <button type="button"
                                    @click="editUnit({{ $unit->id }}, '{{ $unit->code }}', '{{ addslashes($unit->name) }}', '{{ $unit->type }}', {{ $unit->is_active ? 'true' : 'false' }}, {{ $faculty->id }})"
                                    class="text-yellow-600 hover:text-yellow-900 p-2">
                                <i class="fas fa-edit"></i>
                            </button>

                            <form action="{{ route('master.units.destroy', $unit) }}" 
                                  method="POST" class="inline-block"
                                  onsubmit="return confirm('Yakin hapus unit ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 p-2">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Belum ada unit/prodi</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">Belum ada data fakultas</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $faculties->links() }}
    </div>

    <!-- Unit Pusat Section -->
    <div class="mt-8 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg shadow-lg p-6 border border-purple-200">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-landmark text-purple-600 mr-2"></i> Unit Pusat (Kode 0)
                </h3>
                <p class="text-sm text-gray-600">Unit setara fakultas tanpa afiliasi</p>
            </div>
            <button @click="showPusatForm = !showPusatForm"
                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm">
                <i class="fas" :class="showPusatForm ? 'fa-times' : 'fa-plus'" class="mr-2"></i>
                <span x-text="showPusatForm ? 'Batal' : 'Tambah Unit Pusat'"></span>
            </button>
        </div>

        <!-- Unit Pusat Form -->
        <div x-show="showPusatForm" x-collapse class="bg-white rounded-lg p-4 mb-4 border border-purple-200">
            <form :action="editUnitMode ? '/master/units/' + editUnitId : '{{ route('master.units.store') }}'" method="POST">
                @csrf
                <input type="hidden" x-show="editUnitMode" name="_method" value="PUT">
                <input type="hidden" name="faculty_id" value="">
                <input type="hidden" name="type" value="unit_pusat">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kode <span class="text-red-500">*</span>
                        </label>
                        <select name="code" x-model="unitForm.code" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">Pilih</option>
                            @for($i = 0; $i <= 9; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" x-model="unitForm.name" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                               placeholder="Contoh: Rektorat">
                    </div>

                    <div class="flex items-end gap-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" x-model="unitForm.is_active"
                                   class="h-4 w-4 text-purple-600 rounded mr-2">
                            <span class="text-sm">Aktif</span>
                        </label>
                    </div>
                </div>

                <div class="mt-4 flex gap-2">
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <button type="button" 
                            @click="showPusatForm = false; resetUnitForm()"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>

        <!-- Unit Pusat List -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @forelse($unitsPusat as $unit)
            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-purple-200 hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-10 h-10 bg-purple-100 text-purple-700 rounded font-bold font-mono">
                        {{ $unit->code }}
                    </span>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $unit->name }}</p>
                        <p class="text-xs text-gray-500">Kode: <strong>0{{ $unit->code }}</strong></p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if($unit->is_active)
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Aktif</span>
                    @else
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Nonaktif</span>
                    @endif

                    <button type="button"
                            @click="editUnit({{ $unit->id }}, '{{ $unit->code }}', '{{ addslashes($unit->name) }}', '{{ $unit->type }}', {{ $unit->is_active ? 'true' : 'false' }}, null)"
                            class="text-yellow-600 hover:text-yellow-900 p-2">
                        <i class="fas fa-edit"></i>
                    </button>

                    <form action="{{ route('master.units.destroy', $unit) }}" 
                          method="POST" class="inline-block"
                          onsubmit="return confirm('Yakin hapus unit pusat ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 p-2">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-2 text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-4xl mb-2"></i>
                <p>Belum ada unit pusat</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
function facultiesPage() {
    return {
        expandedFaculty: null,
        showFacultyForm: false,
        showUnitForm: null,
        showPusatForm: false,
        editFacultyMode: false,
        editFacultyId: null,
        editUnitMode: false,
        editUnitId: null,
        
        facultyForm: {
            code: '',
            name: '',
            is_active: true
        },
        
        unitForm: {
            code: '',
            name: '',
            type: 'prodi',
            is_active: true
        },

        toggleFaculty(id) {
            this.expandedFaculty = this.expandedFaculty === id ? null : id;
        },

        toggleFacultyForm() {
            this.showFacultyForm = !this.showFacultyForm;
            if (this.showFacultyForm) {
                this.editFacultyMode = false;
                this.facultyForm = { code: '', name: '', is_active: true };
            }
        },

        editFaculty(id, code, name, isActive) {
            this.editFacultyMode = true;
            this.editFacultyId = id;
            this.showFacultyForm = true;
            this.facultyForm = { code, name, is_active: isActive };
        },

        editUnit(id, code, name, type, isActive, facultyId) {
            this.editUnitMode = true;
            this.editUnitId = id;
            this.showUnitForm = facultyId;
            this.showPusatForm = facultyId === null;
            this.unitForm = { code, name, type, is_active: isActive };
        },

        resetUnitForm() {
            this.editUnitMode = false;
            this.editUnitId = null;
            this.unitForm = { code: '', name: '', type: 'prodi', is_active: true };
        }
    }
}
</script>
@endpush
@endsection