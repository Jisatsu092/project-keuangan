<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Transaksi') }}
            </h2>
            <button onclick="openModal('create')"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                + Tambah Transaksi
            </button>
        </div>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Transaksi</label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Tipe</option>
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            <option value="penjualan">Penjualan</option>
                            <option value="operasional">Operasional</option>
                            <option value="sdm">SDM</option>
                            <option value="marketing">Marketing</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                        <input type="date" value="2025-11-01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                        <input type="date" value="2025-11-15"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-4 flex justify-between items-center">
                    <input type="text" placeholder="Cari keterangan, nomor invoice..."
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-96">
                    <div class="flex space-x-2">
                        <button
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">Filter</button>
                        <button
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">Reset</button>
                        <button class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">Export
                            Excel</button>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                    <p class="text-gray-600 text-sm font-medium">Total Pemasukan</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-2">Rp 125.500.000</h3>
                    <p class="text-green-600 text-sm mt-1">127 transaksi</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
                    <p class="text-gray-600 text-sm font-medium">Total Pengeluaran</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-2">Rp 87.250.000</h3>
                    <p class="text-red-600 text-sm mt-1">120 transaksi</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                    <p class="text-gray-600 text-sm font-medium">Selisih (Net)</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-2">Rp 38.250.000</h3>
                    <p class="text-blue-600 text-sm mt-1">Surplus bulan ini</p>
                </div>
            </div>

            <!-- Transaction Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">No. Invoice
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Keterangan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Kategori
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Nominal
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">INV-2025-001</td>
                                <td class="px-6 py-4 text-sm text-gray-600">15 Nov 2025</td>
                                <td class="px-6 py-4 text-sm text-gray-800">Penjualan Produk A ke PT. Maju Jaya</td>
                                <td class="px-6 py-4 text-sm text-gray-600">Penjualan</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Pemasukan</span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-600 text-right">+ Rp 15.750.000
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="openModal('view', 1)"
                                        class="text-gray-600 hover:text-gray-800 mr-2">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button onclick="openModal('edit', 1)"
                                        class="text-blue-600 hover:text-blue-800 mr-2">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button onclick="confirmDelete(1)" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">EXP-2025-045</td>
                                <td class="px-6 py-4 text-sm text-gray-600">14 Nov 2025</td>
                                <td class="px-6 py-4 text-sm text-gray-800">Pembayaran Supplier Bahan Baku</td>
                                <td class="px-6 py-4 text-sm text-gray-600">Operasional</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Pengeluaran</span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-red-600 text-right">- Rp 8.500.000</td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="openModal('view', 2)"
                                        class="text-gray-600 hover:text-gray-800 mr-2">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button onclick="openModal('edit', 2)"
                                        class="text-blue-600 hover:text-blue-800 mr-2">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button onclick="confirmDelete(2)" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">EXP-2025-046</td>
                                <td class="px-6 py-4 text-sm text-gray-600">13 Nov 2025</td>
                                <td class="px-6 py-4 text-sm text-gray-800">Gaji Karyawan Bulan November</td>
                                <td class="px-6 py-4 text-sm text-gray-600">SDM</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Pengeluaran</span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-red-600 text-right">- Rp 25.000.000</td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="openModal('view', 3)"
                                        class="text-gray-600 hover:text-gray-800 mr-2">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button onclick="openModal('edit', 3)"
                                        class="text-blue-600 hover:text-blue-800 mr-2">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button onclick="confirmDelete(3)" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">INV-2025-002</td>
                                <td class="px-6 py-4 text-sm text-gray-600">12 Nov 2025</td>
                                <td class="px-6 py-4 text-sm text-gray-800">Penjualan Layanan Konsultasi</td>
                                <td class="px-6 py-4 text-sm text-gray-600">Jasa</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Pemasukan</span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-600 text-right">+ Rp 12.300.000
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="openModal('view', 4)"
                                        class="text-gray-600 hover:text-gray-800 mr-2">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button onclick="openModal('edit', 4)"
                                        class="text-blue-600 hover:text-blue-800 mr-2">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button onclick="confirmDelete(4)" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">EXP-2025-047</td>
                                <td class="px-6 py-4 text-sm text-gray-600">11 Nov 2025</td>
                                <td class="px-6 py-4 text-sm text-gray-800">Pembelian Alat Kantor</td>
                                <td class="px-6 py-4 text-sm text-gray-600">Operasional</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Pengeluaran</span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-red-600 text-right">- Rp 3.250.000</td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="openModal('view', 5)"
                                        class="text-gray-600 hover:text-gray-800 mr-2">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button onclick="openModal('edit', 5)"
                                        class="text-blue-600 hover:text-blue-800 mr-2">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button onclick="confirmDelete(5)" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                    <p class="text-sm text-gray-600">Menampilkan 5 dari 247 transaksi</p>
                    <div class="flex space-x-2">
                        <button
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Previous</button>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">1</button>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">2</button>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">3</button>
                        <button
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Next</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Create/Edit -->
    <div id="transactionModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800" id="modalTitle">Tambah Transaksi</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form id="transactionForm">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Invoice</label>
                            <input type="text" value="INV-2025-003"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                            <input type="date" value="2025-11-15"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                            <select
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="income">Pemasukan</option>
                                <option value="expense">Pengeluaran</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                            <select
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="penjualan">Penjualan</option>
                                <option value="jasa">Jasa</option>
                                <option value="operasional">Operasional</option>
                                <option value="sdm">SDM</option>
                                <option value="marketing">Marketing</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                            <textarea rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Detail transaksi..."></textarea>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nominal (Rp)</label>
                            <input type="number" value="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                            <select
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="credit">Kartu Kredit</option>
                                <option value="debit">Kartu Debit</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti (Optional)</label>
                            <input type="file"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeModal()"
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Batal</button>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function openModal(mode, id = null) {
            const modal = document.getElementById('transactionModal');
            const title = document.getElementById('modalTitle'); if (mode === 'create') {
                title.textContent = 'Tambah Transaksi';
            } else if (mode === 'edit') {
                title.textContent = 'Edit Transaksi';
            } else if (mode === 'view') {
                title.textContent = 'Detail Transaksi';
            } modal.classList.remove('hidden');
        } function closeModal() {
            document.getElementById('transactionModal').classList.add('hidden');
        } function confirmDelete(id) {
            if (confirm('Yakin ingin menghapus transaksi ini?')) {
                alert('Transaksi ID: ' + id + ' dihapus!');
            }
        }
    </script>
</x-app-layout>
````