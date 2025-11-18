@extends('layouts.app')

@section('title', 'Master Data - Jenis Kegiatan')

@section('content')
<div class="px-4 sm:px-0" x-data="{ 
    showForm: false, 
    editMode: false, 
    editId: null, 
    formData: { 
        code: '', 
        name: '', 
        description: '', 
        is_active: true 
    } 
}">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Master Data - Jenis Kegiatan</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola data jenis kegiatan (Digit 5)</p>
        </div>
        <button @click="showForm = !showForm; editMode = false; formData = { code: '', name: '', description: '', is_active: true }" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition">
            <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus'" class="mr-2"></i> 
            <span x-text="showForm ? 'Batal' : 'Tambah Jenis Kegiatan'"></span>
        </button>
    </div>

    <!-- Form (Collapsible) -->
    <div x-show="showForm" x-collapse class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4" x-text="editMode ? 'Edit Jenis Kegiatan' : 'Tambah Jenis Kegiatan Baru'"></h3>
        
        <form :action="editMode ? '{{ url('master/activity-types') }}/' + editId : '{{ route('master.activity-types.store') }}'" method="POST">
            @csrf
            <div x-show="editMode">
                @method('PUT')
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <select name="code" x-model="formData.code" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Kode</option>
                        @for($i = 0; $i <= 9; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle"></i> Kode 0-9
                    </p>
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Jenis Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" x-model="formData.name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: Penelitian, Pengabdian, dll">
                </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea name="description" x-model="formData.description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          placeholder="Deskripsi optional jenis kegiatan"></textarea>
            </div>

            <!-- Active Status -->
            <div class="flex items-center mb-4">
                <input type="checkbox" name="is_active" value="1" x-model="formData.is_active"
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label class="ml-2 block text-sm text-gray-900">Aktif</label>
            </div>

            <div class="flex gap-2">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
                <button type="button" @click="showForm = false; editMode = false" 
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">
                    <i class="fas fa-times mr-2"></i> Batal
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                        Kode
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Jenis Kegiatan
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Deskripsi
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Jumlah Akun
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($activityTypes as $activityType)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-2xl font-bold font-mono text-blue-600">{{ $activityType->code }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-gray-900">{{ $activityType->name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">
                            {{ $activityType->description ?: '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                            {{ $activityType->accounts()->count() }} Akun
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($activityType->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">
                                <i class="fas fa-check-circle"></i> Aktif
                            </span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">
                                <i class="fas fa-times-circle"></i> Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button @click="editMode = true; showForm = true; editId = {{ $activityType->id }}; formData = { 
                            code: '{{ $activityType->code }}', 
                            name: '{{ addslashes($activityType->name) }}', 
                            description: '{{ addslashes($activityType->description) }}', 
                            is_active: {{ $activityType->is_active ? 'true' : 'false' }} 
                        }"
                                class="text-yellow-600 hover:text-yellow-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('master.activity-types.destroy', $activityType) }}" 
                              method="POST" class="inline-block"
                              onsubmit="return confirm('Yakin hapus jenis kegiatan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500">Belum ada data jenis kegiatan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $activityTypes->links() }}
    </div>
</div>
@endsection