@extends('layouts.app')

@section('title', 'Master Data - Unit/Prodi')

@section('content')
<div class="px-4 sm:px-0" x-data="{ showForm: false, editMode: false, editId: null, formData: { faculty_id: '', code: '', name: '', type: 'prodi', is_active: true } }">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Master Data - Unit/Prodi</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola data unit pusat & prodi (Digit 4)</p>
        </div>
        <button @click="showForm = !showForm; editMode = false; formData = { faculty_id: '', code: '', name: '', type: 'prodi', is_active: true }" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition">
            <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus'" class="mr-2"></i> 
            <span x-text="showForm ? 'Batal' : 'Tambah Unit/Prodi'"></span>
        </button>
    </div>

    <!-- Form (Collapsible) -->
    <div x-show="showForm" x-collapse class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4" x-text="editMode ? 'Edit Unit/Prodi' : 'Tambah Unit/Prodi Baru'"></h3>
        
        <form :action="editMode ? '/master/units/' + editId : '{{ route('master.units.store') }}'" method="POST">
            @csrf
            <input type="hidden" x-show="editMode" name="_method" value="PUT">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Faculty -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fakultas</label>
                    <select name="faculty_id" x-model="formData.faculty_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Pusat (Unit Setara)</option>
                        @foreach($faculties as $fac)
                            <option value="{{ $fac->id }}">{{ $fac->code }} - {{ $fac->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode <span class="text-red-500">*</span></label>
                    <select name="code" x-model="formData.code" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Pilih</option>
                        @for($i = 0; $i <= 9; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="formData.name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                    <select name="type" x-model="formData.type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="prodi">Prodi</option>
                        <option value="unit_pusat">Unit Pusat</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-4">
                <input type="checkbox" name="is_active" value="1" x-model="formData.is_active" class="h-4 w-4">
                <label class="text-sm">Aktif</label>
                
                <button type="submit" class="ml-auto px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
                <button type="button" @click="showForm = false" class="px-6 py-2 bg-gray-200 rounded-lg">Batal</button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fakultas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($units as $unit)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><span class="font-mono font-bold text-blue-600">{{ $unit->code }}</span></td>
                    <td class="px-6 py-4">{{ $unit->name }}</td>
                    <td class="px-6 py-4">{{ $unit->faculty ? $unit->faculty->name : 'Pusat' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs" :class="'{{ $unit->type }}' === 'prodi' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
                            {{ ucfirst($unit->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($unit->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Aktif</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button @click="editMode = true; showForm = true; editId = {{ $unit->id }}; formData = { faculty_id: '{{ $unit->faculty_id }}', code: '{{ $unit->code }}', name: '{{ $unit->name }}', type: '{{ $unit->type }}', is_active: {{ $unit->is_active ? 'true' : 'false' }} }"
                                class="text-yellow-600 hover:text-yellow-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('master.units.destroy', $unit) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $units->links() }}</div>
</div>
@endsection