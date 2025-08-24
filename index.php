<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_title = 'Dashboard';
$page_description = 'Ringkasan data produksi HHK dan HHBK';

include 'includes/header.php';
?>

<!-- Dashboard Content -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Produksi Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Total Produksi</h3>
                <p class="text-2xl font-semibold text-gray-800"><?php echo getTotalProduksi(); ?></p>
                <p class="text-xs text-gray-500 mt-1">Data produksi keseluruhan</p>
            </div>
        </div>
    </div>

    <!-- Total Volume Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2H5a2 2 0 00-2 2v2m14 0V5a2 2 0 00-2-2H5a2 2 0 00-2 2v4"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Total Volume</h3>
                <p class="text-2xl font-semibold text-gray-800"><?php echo getTotalVolume(); ?></p>
                <p class="text-xs text-gray-500 mt-1">Volume produksi total</p>
            </div>
        </div>
    </div>

    <!-- Total Komoditas Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Total Komoditas</h3>
                <p class="text-2xl font-semibold text-gray-800"><?php echo getTotalKomoditas(); ?></p>
                <p class="text-xs text-gray-500 mt-1">Jenis komoditas aktif</p>
            </div>
        </div>
    </div>

    <!-- Total KTH Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Total KTH</h3>
                <p class="text-2xl font-semibold text-gray-800"><?php echo getTotalKTH(); ?></p>
                <p class="text-xs text-gray-500 mt-1">Kelompok Tani Hutan</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Produksi per Bulan Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Produksi per Bulan</h3>
            <div class="text-sm text-gray-500">
                <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                Trend Produksi
            </div>
        </div>
        <div class="mb-4 text-sm text-gray-600">
            <p>Menampilkan trend produksi 12 bulan terakhir</p>
        </div>
        <div style="height: 300px;">
            <canvas id="chartProduksiBulan"></canvas>
        </div>
        <div class="mt-4 text-xs text-gray-500 text-center">
            <p>Klik pada grafik untuk melihat detail per bulan</p>
        </div>
    </div>
    
    <!-- Produksi per Kategori Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Produksi per Kategori</h3>
            <div class="flex space-x-2">
                <div class="flex items-center text-sm">
                    <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                    HHK
                </div>
                <div class="flex items-center text-sm">
                    <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                    HHBK
                </div>
            </div>
        </div>
        <div class="mb-4 text-sm text-gray-600">
            <p>Perbandingan produksi HHK vs HHBK</p>
        </div>
        <div style="height: 300px;">
            <canvas id="chartProduksiKategori"></canvas>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-4 text-center">
            <div class="bg-blue-50 p-3 rounded-lg">
                <p class="text-2xl font-bold text-blue-600"><?php echo getHHKPercentage(); ?>%</p>
                <p class="text-xs text-blue-600">HHK</p>
            </div>
            <div class="bg-green-50 p-3 rounded-lg">
                <p class="text-2xl font-bold text-green-600"><?php echo getHHBKPercentage(); ?>%</p>
                <p class="text-xs text-green-600">HHBK</p>
            </div>
        </div>
    </div>
</div>

<!-- Additional Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Top 5 Komoditas Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Top 5 Komoditas</h3>
        <div class="mb-4 text-sm text-gray-600">
            <p>Komoditas dengan produksi tertinggi</p>
        </div>
        <div style="height: 300px;">
            <canvas id="chartTopKomoditas"></canvas>
        </div>
    </div>
    
    <!-- Produksi per Wilayah Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Produksi per Wilayah</h3>
        <div class="mb-4 text-sm text-gray-600">
            <p>Wilayah dengan produksi tertinggi</p>
        </div>
        <div style="height: 300px;">
            <canvas id="chartWilayah"></canvas>
        </div>
    </div>
</div>

<!-- Recent Data Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Produksi -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Data Produksi Terbaru</h3>
            <p class="text-sm text-gray-500 mt-1">5 data produksi terbaru</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komoditas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volume</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php echo getRecentProduksi(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="space-y-3">
            <a href="produksi.php" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="text-green-800 font-medium">Tambah Data Produksi</span>
            </a>
            
            <a href="laporan.php" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-blue-800 font-medium">Lihat Laporan</span>
            </a>
            
            <a href="komoditas.php" class="flex items-center p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span class="text-yellow-800 font-medium">Kelola Komoditas</span>
            </a>
            
            <a href="kth.php" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="text-purple-800 font-medium">Kelola KTH</span>
            </a>
        </div>
    </div>
</div>

<script>
    // Chart Produksi per Bulan - Enhanced
    const ctxProduksiBulan = document.getElementById('chartProduksiBulan').getContext('2d');
    new Chart(ctxProduksiBulan, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(getChartLabels()); ?>,
            datasets: [{
                label: 'Total Produksi',
                data: <?php echo json_encode(getChartData()); ?>,
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10B981',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#10B981',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            return 'Bulan: ' + context[0].label;
                        },
                        label: function(context) {
                            return 'Total Produksi: ' + Number(context.parsed.y).toLocaleString('id-ID', {
                                minimumFractionDigits: 3,
                                maximumFractionDigits: 3
                            });
                        }
                    }
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return Number(value).toLocaleString('id-ID');
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false
                    }
                }
            }
        }
    });

    // Chart Produksi per Kategori - Enhanced
    const ctxProduksiKategori = document.getElementById('chartProduksiKategori').getContext('2d');
    new Chart(ctxProduksiKategori, {
        type: 'doughnut',
        data: {
            labels: ['HHK (Hasil Hutan Kayu)', 'HHBK (Hasil Hutan Bukan Kayu)'],
            datasets: [{
                data: <?php echo json_encode(getKategoriData()); ?>,
                backgroundColor: ['#3B82F6', '#10B981'],
                borderColor: ['#2563EB', '#059669'],
                borderWidth: 2,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#10B981',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + Number(context.parsed).toLocaleString('id-ID', {
                                minimumFractionDigits: 3,
                                maximumFractionDigits: 3
                            }) + ' (' + percentage + '%)';
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            }
        }
    });

    // Chart Top 5 Komoditas - New
    const ctxTopKomoditas = document.getElementById('chartTopKomoditas').getContext('2d');
    new Chart(ctxTopKomoditas, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(getTopKomoditasLabels()); ?>,
            datasets: [{
                label: 'Volume Produksi',
                data: <?php echo json_encode(getTopKomoditasData()); ?>,
                backgroundColor: [
                    '#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6'
                ],
                borderColor: [
                    '#DC2626', '#D97706', '#059669', '#2563EB', '#7C3AED'
                ],
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#10B981',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Volume: ' + Number(context.parsed.x).toLocaleString('id-ID', {
                                minimumFractionDigits: 3,
                                maximumFractionDigits: 3
                            });
                        }
                    }
                },
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return Number(value).toLocaleString('id-ID');
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false
                    }
                }
            }
        }
    });

    // Chart Wilayah - New
    const ctxWilayah = document.getElementById('chartWilayah').getContext('2d');
    new Chart(ctxWilayah, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(getWilayahLabels()); ?>,
            datasets: [{
                label: 'Volume Produksi',
                data: <?php echo json_encode(getWilayahData()); ?>,
                backgroundColor: [
                    '#8B5CF6', '#06B6D4', '#84CC16', '#F97316', '#EC4899'
                ],
                borderColor: [
                    '#7C3AED', '#0891B2', '#65A30D', '#EA580C', '#DB2777'
                ],
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#10B981',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Volume: ' + Number(context.parsed.y).toLocaleString('id-ID', {
                                minimumFractionDigits: 3,
                                maximumFractionDigits: 3
                            });
                        }
                    }
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return Number(value).toLocaleString('id-ID');
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        maxRotation: 30,
                        minRotation: 30
                    }
                }
            }
        }
    });
</script>

<?php
function getTotalProduksi() {
    try {
        $result = getSingleRow("SELECT COUNT(*) as total FROM produksi");
        return $result ? number_format($result['total']) : '0';
    } catch(Exception $e) {
        return '0';
    }
}

function getTotalVolume() {
    try {
        $result = getSingleRow("SELECT SUM(qty) as total FROM produksi");
        return $result ? number_format($result['total'] ?: 0, 3) : '0.000';
    } catch(Exception $e) {
        return '0.000';
    }
}

function getTotalKomoditas() {
    try {
        $result = getSingleRow("SELECT COUNT(*) as total FROM komoditas WHERE aktif = 1");
        return $result ? number_format($result['total']) : '0';
    } catch(Exception $e) {
        return '0';
    }
}

function getTotalKTH() {
    try {
        $result = getSingleRow("SELECT COUNT(*) as total FROM kth");
        return $result ? number_format($result['total']) : '0';
    } catch(Exception $e) {
        return '0';
    }
}

function getChartLabels() {
    try {
        $rows = getMultipleRows("
            SELECT DATE_FORMAT(periode, '%Y-%m') as bulan 
            FROM produksi 
            GROUP BY DATE_FORMAT(periode, '%Y-%m') 
            ORDER BY bulan DESC 
            LIMIT 12
        ");
        
        $labels = [];
        foreach ($rows as $row) {
            $labels[] = date('M Y', strtotime($row['bulan'] . '-01'));
        }
        return array_reverse($labels);
    } catch(Exception $e) {
        return [];
    }
}

function getChartData() {
    try {
        $rows = getMultipleRows("
            SELECT DATE_FORMAT(periode, '%Y-%m') as bulan, SUM(qty) as total 
            FROM produksi 
            GROUP BY DATE_FORMAT(periode, '%Y-%m') 
            ORDER BY bulan DESC 
            LIMIT 12
        ");
        
        $data = [];
        foreach ($rows as $row) {
            $data[] = $row['total'] ?: 0;
        }
        return array_reverse($data);
    } catch(Exception $e) {
        return [];
    }
}

function getKategoriData() {
    try {
        $rows = getMultipleRows("
            SELECT k.kategori, SUM(p.qty) as total 
            FROM produksi p 
            JOIN komoditas k ON p.komoditas_id = k.id 
            GROUP BY k.kategori
        ");
        
        $data = [0, 0]; // [HHK, HHBK]
        foreach ($rows as $row) {
            if ($row['kategori'] == 'HHK') {
                $data[0] = $row['total'] ?: 0;
            } else {
                $data[1] = $row['total'] ?: 0;
            }
        }
        return $data;
    } catch(Exception $e) {
        return [0, 0];
    }
}

function getHHKPercentage() {
    try {
        $data = getKategoriData();
        $total = array_sum($data);
        if ($total == 0) return 0;
        return round(($data[0] / $total) * 100, 1);
    } catch(Exception $e) {
        return 0;
    }
}

function getHHBKPercentage() {
    try {
        $data = getKategoriData();
        $total = array_sum($data);
        if ($total == 0) return 0;
        return round(($data[1] / $total) * 100, 1);
    } catch(Exception $e) {
        return 0;
    }
}

function getTopKomoditasLabels() {
    try {
        $rows = getMultipleRows("
            SELECT k.nama, k.kategori 
            FROM produksi p 
            JOIN komoditas k ON p.komoditas_id = k.id 
            GROUP BY k.id, k.nama, k.kategori 
            ORDER BY SUM(p.qty) DESC 
            LIMIT 5
        ");
        
        $labels = [];
        foreach ($rows as $row) {
            $labels[] = $row['nama'] . ' (' . $row['kategori'] . ')';
        }
        return $labels;
    } catch(Exception $e) {
        return [];
    }
}

function getTopKomoditasData() {
    try {
        $rows = getMultipleRows("
            SELECT SUM(p.qty) as total 
            FROM produksi p 
            JOIN komoditas k ON p.komoditas_id = k.id 
            GROUP BY k.id, k.nama, k.kategori 
            ORDER BY SUM(p.qty) DESC 
            LIMIT 5
        ");
        
        $data = [];
        foreach ($rows as $row) {
            $data[] = $row['total'] ?: 0;
        }
        return $data;
    } catch(Exception $e) {
        return [];
    }
}

function getWilayahLabels() {
    try {
        $rows = getMultipleRows("
            SELECT CONCAT(d.nama, ', ', kec.nama) as wilayah 
            FROM produksi p 
            JOIN desa d ON p.desa_id = d.id 
            JOIN kecamatan kec ON p.kecamatan_id = kec.id 
            GROUP BY p.desa_id, d.nama, kec.nama 
            ORDER BY SUM(p.qty) DESC 
            LIMIT 5
        ");
        
        $labels = [];
        foreach ($rows as $row) {
            $labels[] = $row['wilayah'];
        }
        return $labels;
    } catch(Exception $e) {
        return [];
    }
}

function getWilayahData() {
    try {
        $rows = getMultipleRows("
            SELECT SUM(p.qty) as total 
            FROM produksi p 
            JOIN desa d ON p.desa_id = d.id 
            JOIN kecamatan kec ON p.kecamatan_id = kec.id 
            GROUP BY p.desa_id, d.nama, kec.nama 
            ORDER BY SUM(p.qty) DESC 
            LIMIT 5
        ");
        
        $data = [];
        foreach ($rows as $row) {
            $data[] = $row['total'] ?: 0;
        }
        return $data;
    } catch(Exception $e) {
        return [];
    }
}

function getRecentProduksi() {
    try {
        $rows = getMultipleRows("
            SELECT p.periode, k.nama as komoditas, p.qty 
            FROM produksi p 
            JOIN komoditas k ON p.komoditas_id = k.id 
            ORDER BY p.created_at DESC 
            LIMIT 5
        ");
        
        $output = '';
        if (empty($rows)) {
            $output .= '<tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td></tr>';
        } else {
            foreach ($rows as $row) {
                $output .= '<tr>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . date('d/m/Y', strtotime($row['periode'])) . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['komoditas']) . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . number_format($row['qty'], 3) . '</td>';
                $output .= '</tr>';
            }
        }
        return $output;
    } catch(Exception $e) {
        return '<tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Error loading data</td></tr>';
    }
}

include 'includes/footer.php';
?> 