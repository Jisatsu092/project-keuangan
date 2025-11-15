<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Keuangan') }}
            </h2>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                + Transaksi Baru
            </button>
        </div>
    </x-slot>

    <!-- Tailwind CDN (taruh di sini dulu, nanti pindah ke app.blade.php) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Pemasukan</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-2">Rp 125.500.000</h3>
                            <p class="text-green-600 text-sm mt-2">↑ 12.5% dari bulan lalu</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Pengeluaran</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-2">Rp 87.250.000</h3>
                            <p class="text-red-600 text-sm mt-2">↑ 8.3% dari bulan lalu</p>
                        </div>
                        <div class="bg-red-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Saldo Kas</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-2">Rp 38.250.000</h3>
                            <p class="text-blue-600 text-sm mt-2">Posisi kas saat ini</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Transaksi Bulan Ini</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-2">247</h3>
                            <p class="text-purple-600 text-sm mt-2">127 masuk, 120 keluar</p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Grafik Arus Kas</h3>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>6 Bulan Terakhir</option>
                            <option>3 Bulan Terakhir</option>
                            <option>1 Tahun Terakhir</option>
                        </select>
                    </div>
                    <canvas id="cashFlowChart" height="80"></canvas>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Kategori Pengeluaran</h3>
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>

            <!-- Transaction Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Transaksi Terbaru</h3>
                        <div class="flex space-x-3">
                            <input type="text" placeholder="Cari transaksi..." class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Semua Tipe</option>
                                <option>Pemasukan</option>
                                <option>Pengeluaran</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Keterangan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Nominal</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-600">15 Nov 2025</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">Penjualan Produk A</td>
                                <td class="px-6 py-4 text-sm text-gray-600">Penjualan</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Pemasukan</span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-600 text-right">+ Rp 15.750.000</td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-blue-600 hover:text-blue-800 mr-3 text-sm">Edit</button>
                                    <button class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-600">14 Nov 2025</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">Pembayaran Supplier</td>
                                <td class="px-6 py-4 text-sm text-gray-600">Operasional</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Pengeluaran</span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-red-600 text-right">- Rp 8.500.000</td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-blue-600 hover:text-blue-800 mr-3 text-sm">Edit</button>
                                    <button class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-600">13 Nov 2025</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">Gaji Karyawan</td>
                                <td class="px-6 py-4 text-sm text-gray-600">SDM</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Pengeluaran</span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-red-600 text-right">- Rp 25.000.000</td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-blue-600 hover:text-blue-800 mr-3 text-sm">Edit</button>
                                    <button class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                    <p class="text-sm text-gray-600">Menampilkan 3 dari 247 transaksi</p>
                    <div class="flex space-x-2">
                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Previous</button>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">1</button>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">2</button>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Next</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cash Flow Chart
            const ctx1 = document.getElementById('cashFlowChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: ['Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'],
                    datasets: [{
                        label: 'Pemasukan',
                        data: [95000000, 105000000, 98000000, 115000000, 110000000, 125500000],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Pengeluaran',
                        data: [75000000, 82000000, 78000000, 88000000, 80000000, 87250000],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000000) + 'jt';
                                }
                            }
                        }
                    }
                }
            });

            // Category Chart
            const ctx2 = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Operasional', 'SDM', 'Marketing', 'Lain-lain'],
                    datasets: [{
                        data: [35, 30, 20, 15],
                        backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>