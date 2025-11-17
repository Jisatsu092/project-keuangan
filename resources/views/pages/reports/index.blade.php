<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Laporan Keuangan</h2>
                <div class="flex space-x-2">
                    <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                        Export PDF
                    </button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        Export Excel
                    </button>
                </div>
            </div>

            <!-- Filter Period -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Periode Laporan</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="daily">Harian</option>
                            <option value="monthly" selected>Bulanan</option>
                            <option value="yearly">Tahunan</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11" selected>November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025" selected>2025</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Laporan</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">Semua</option>
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                            <option value="cashflow">Arus Kas</option>
                        </select>
                    </div>
                    <div>
                        <button class="w-full px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">Generate</button>
                    </div>
                </div>
            </div>

            <!-- Summary Report Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-sm p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Total Pemasukan</p>
                            <h3 class="text-3xl font-bold mt-2">Rp 125.5 Jt</h3>
                            <p class="text-green-100 text-sm mt-2">127 transaksi</p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-sm p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-100 text-sm font-medium">Total Pengeluaran</p>
                            <h3 class="text-3xl font-bold mt-2">Rp 87.2 Jt</h3>
                            <p class="text-red-100 text-sm mt-2">120 transaksi</p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-sm p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Net Profit</p>
                            <h3 class="text-3xl font-bold mt-2">Rp 38.3 Jt</h3>
                            <p class="text-blue-100 text-sm mt-2">30.5% margin</p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-sm p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Rata-rata Harian</p>
                            <h3 class="text-3xl font-bold mt-2">Rp 2.5 Jt</h3>
                            <p class="text-purple-100 text-sm mt-2">15 hari aktif</p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Cash Flow Trend -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Trend Arus Kas (6 Bulan)</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                <!-- Category Breakdown -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Breakdown Pengeluaran</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="expenseChart"></canvas>
                    </div>
                </div>

                <!-- Income vs Expense -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Perbandingan Pemasukan & Pengeluaran</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="comparisonChart"></canvas>
                    </div>
                </div>

                <!-- Top Transactions -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Top 5 Transaksi Terbesar</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Penjualan Produk A</p>
                                <p class="text-xs text-gray-600">15 Nov 2025</p>
                            </div>
                            <span class="text-green-600 font-bold">+ Rp 15.750.000</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Penjualan Layanan B</p>
                                <p class="text-xs text-gray-600">12 Nov 2025</p>
                            </div>
                            <span class="text-green-600 font-bold">+ Rp 12.300.000</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Gaji Karyawan</p>
                                <p class="text-xs text-gray-600">13 Nov 2025</p>
                            </div>
                            <span class="text-red-600 font-bold">- Rp 25.000.000</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Pembayaran Supplier</p>
                                <p class="text-xs text-gray-600">14 Nov 2025</p>
                            </div>
                            <span class="text-red-600 font-bold">- Rp 8.500.000</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Penjualan Produk C</p>
                                <p class="text-xs text-gray-600">10 Nov 2025</p>
                            </div>
                            <span class="text-green-600 font-bold">+ Rp 8.200.000</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Report Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Laporan Detail Per Kategori</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Kategori</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Jumlah Transaksi</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Total Pemasukan</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Total Pengeluaran</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Selisih</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">% dari Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">Penjualan</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">87</td>
                                <td class="px-6 py-4 text-sm text-green-600 font-semibold text-right">Rp 85.300.000</td>
                                <td class="px-6 py-4 text-sm text-gray-400 text-right">-</td>
                                <td class="px-6 py-4 text-sm text-green-600 font-bold text-right">+ Rp 85.300.000</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">68.0%</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">Jasa</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">40</td>
                                <td class="px-6 py-4 text-sm text-green-600 font-semibold text-right">Rp 40.200.000</td>
                                <td class="px-6 py-4 text-sm text-gray-400 text-right">-</td>
                                <td class="px-6 py-4 text-sm text-green-600 font-bold text-right">+ Rp 40.200.000</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">32.0%</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">Operasional</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">45</td>
                                <td class="px-6 py-4 text-sm text-gray-400 text-right">-</td>
                                <td class="px-6 py-4 text-sm text-red-600 font-semibold text-right">Rp 35.000.000</td>
                                <td class="px-6 py-4 text-sm text-red-600 font-bold text-right">- Rp 35.000.000</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">40.1%</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">SDM</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">12</td>
                                <td class="px-6 py-4 text-sm text-gray-400 text-right">-</td>
                                <td class="px-6 py-4 text-sm text-red-600 font-semibold text-right">Rp 32.500.000</td>
                                <td class="px-6 py-4 text-sm text-red-600 font-bold text-right">- Rp 32.500.000</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">37.3%</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">Marketing</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">38</td>
                                <td class="px-6 py-4 text-sm text-gray-400 text-right">-</td>
                                <td class="px-6 py-4 text-sm text-red-600 font-semibold text-right">Rp 15.250.000</td>
                                <td class="px-6 py-4 text-sm text-red-600 font-bold text-right">- Rp 15.250.000</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">17.5%</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">Lain-lain</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">25</td>
                                <td class="px-6 py-4 text-sm text-gray-400 text-right">-</td>
                                <td class="px-6 py-4 text-sm text-red-600 font-semibold text-right">Rp 4.500.000</td>
                                <td class="px-6 py-4 text-sm text-red-600 font-bold text-right">- Rp 4.500.000</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">5.1%</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                            <tr>
                                <td class="px-6 py-4 text-sm font-bold text-gray-800">TOTAL</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-800 text-right">247</td>
                                <td class="px-6 py-4 text-sm font-bold text-green-600 text-right">Rp 125.500.000</td>
                                <td class="px-6 py-4 text-sm font-bold text-red-600 text-right">Rp 87.250.000</td>
                                <td class="px-6 py-4 text-sm font-bold text-blue-600 text-right">+ Rp 38.250.000</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-800 text-right">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Trend Chart - Line Chart
        const ctx1 = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'],
                datasets: [{
                    label: 'Pemasukan',
                    data: [95, 105, 98, 115, 110, 125.5],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }, {
                    label: 'Pengeluaran',
                    data: [75, 82, 78, 88, 80, 87.2],
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y + ' juta';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value + 'jt';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Expense Breakdown - Doughnut Chart
        const ctx2 = document.getElementById('expenseChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Operasional', 'SDM', 'Marketing', 'Lain-lain'],
                datasets: [{
                    data: [35, 32.5, 15.25, 4.5],
                    backgroundColor: [
                        '#3b82f6',
                        '#8b5cf6',
                        '#ec4899',
                        '#f59e0b'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': Rp ' + context.parsed + ' jt (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Comparison Bar Chart
        const ctx3 = document.getElementById('comparisonChart').getContext('2d');
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: ['Penjualan', 'Jasa', 'Operasional', 'SDM', 'Marketing', 'Lain-lain'],
                datasets: [{
                    label: 'Pemasukan',
                    data: [85.3, 40.2, 0, 0, 0, 0],
                    backgroundColor: '#10b981',
                    borderRadius: 5
                }, {
                    label: 'Pengeluaran',
                    data: [0, 0, 35, 32.5, 15.25, 4.5],
                    backgroundColor: '#ef4444',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y + ' juta';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value + 'jt';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
</x-app-layout>