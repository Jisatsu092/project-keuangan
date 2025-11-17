<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Buat Akun Baru
            </h2>
            <a href="{{ route('accounts.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('accounts.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kode Akun -->
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700">Kode Akun *</label>
                                <input type="text" name="code" id="code" required
                                       class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                       placeholder="Contoh: 5102403"
                                       value="{{ old('code') }}">
                                <p class="mt-1 text-sm text-gray-500">
                                    Format: 7 digit (contoh: 5102403)
                                </p>
                                @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama Akun -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Akun *</label>
                                <input type="text" name="name" id="name" required
                                       class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                       value="{{ old('name') }}">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Normal Balance -->
                            <div>
                                <label for="normal_balance" class="block text-sm font-medium text-gray-700">Normal Balance *</label>
                                <select name="normal_balance" id="normal_balance" required
                                        class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                    <option value="">Pilih Normal Balance</option>
                                    <option value="debit" {{ old('normal_balance') == 'debit' ? 'selected' : '' }}>Debit</option>
                                    <option value="kredit" {{ old('normal_balance') == 'kredit' ? 'selected' : '' }}>Kredit</option>
                                </select>
                                @error('normal_balance')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Header Account -->
                            <div class="flex items-center">
                                <input type="checkbox" name="is_header" id="is_header" value="1"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                       {{ old('is_header') ? 'checked' : '' }}>
                                <label for="is_header" class="ml-2 block text-sm text-gray-900">
                                    Akun Header (tidak bisa transaksi)
                                </label>
                            </div>

                            <!-- Deskripsi -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea name="description" id="description" rows="3"
                                          class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Informasi Struktur -->
                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Struktur Kode</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div class="bg-gray-50 p-3 rounded">
                                    <div class="font-medium text-gray-900">Digit 1</div>
                                    <div class="text-gray-600">Jenis Akun</div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded">
                                    <div class="font-medium text-gray-900">Digit 2</div>
                                    <div class="text-gray-600">Operasi/Program</div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded">
                                    <div class="font-medium text-gray-900">Digit 3</div>
                                    <div class="text-gray-600">Fakultas/Unit</div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded">
                                    <div class="font-medium text-gray-900">Digit 4-7</div>
                                    <div class="text-gray-600">Detail</div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('accounts.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Akun
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>