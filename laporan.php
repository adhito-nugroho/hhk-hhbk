<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
require_once 'components/DataTable.php';

$page_title = 'Laporan Produksi';
$page_description = 'Laporan dan analisis data produksi HHK dan HHBK';

$filter_periode = $_GET['periode'] ?? date('Y-m');
$filter_kabupaten = $_GET['kabupaten'] ?? '';
$filter_kecamatan = $_GET['kecamatan'] ?? '';
$filter_desa = $_GET['desa'] ?? '';
$filter_komoditas = $_GET['komoditas'] ?? '';

include 'includes/header.php';
?>

<!-- Page Content -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="text-lg font-medium text-gray-900">Laporan Produksi</h3>
        <p class="text-sm text-gray-600">Laporan dan analisis data produksi HHK dan HHBK</p>
    </div>
    <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Export Excel
    </button>
</div>

<!-- Filter Form -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Laporan</h3>
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
            <input type="month" name="periode" value="<?php echo $filter_periode; ?>" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kabupaten</label>
            <select name="kabupaten" id="kabupatenSelect" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Semua Kabupaten</option>
                <?php echo getKabupatenFilterOptions($filter_kabupaten); ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kecamatan</label>
            <select name="kecamatan" id="kecamatanSelect" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Semua Kecamatan</option>
                <?php echo getKecamatanFilterOptions($filter_kabupaten, $filter_kecamatan); ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Desa</label>
            <select name="desa" id="desaSelect" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Semua Desa</option>
                <?php echo getDesaFilterOptions($filter_kecamatan, $filter_desa); ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Komoditas</label>
            <select name="komoditas" id="komoditasSelect" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Semua Komoditas</option>
                <?php echo getKomoditasFilterOptions($filter_komoditas); ?>
            </select>
        </div>
        
        <div class="lg:col-span-5 flex justify-end space-x-3">
            <button type="submit" onclick="return validateFilterForm()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">
                Filter
            </button>
            <button type="button" onclick="resetFilters()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md">
                Reset
            </button>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Total Produksi</h3>
                <p class="text-2xl font-semibold text-gray-800"><?php echo getTotalProduksiFiltered(); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2H5a2 2 0 00-2 2v2m14 0V5a2 2 0 00-2-2H5a2 2 0 00-2 2v4"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Total Volume</h3>
                <p class="text-2xl font-semibold text-gray-800"><?php echo getTotalVolumeFiltered(); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Wilayah Terlibat</h3>
                <p class="text-2xl font-semibold text-gray-800"><?php echo getTotalWilayahFiltered(); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Rata-rata per Wilayah</h3>
                <p class="text-2xl font-semibold text-gray-800"><?php echo getRataRataPerWilayah(); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Produksi per Komoditas</h3>
            <div class="text-sm text-gray-500">
                <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                Top 8 Komoditas
            </div>
        </div>
        <div class="mb-4 text-sm text-gray-600">
            <p>Komoditas dengan produksi tertinggi berdasarkan filter yang dipilih</p>
        </div>
        <div style="height: 300px;">
            <canvas id="chartKomoditas"></canvas>
        </div>
        <div class="mt-4 text-xs text-gray-500 text-center">
            <p>Klik pada grafik untuk melihat detail komoditas</p>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Produksi per Wilayah</h3>
            <div class="text-sm text-gray-500">
                <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                Top 10 Wilayah
            </div>
        </div>
        <div class="mb-4 text-sm text-gray-600">
            <p>Wilayah dengan produksi tertinggi berdasarkan filter yang dipilih</p>
        </div>
        <div style="height: 300px;">
            <canvas id="chartWilayah"></canvas>
        </div>
        <div class="mt-4 text-xs text-gray-500 text-center">
            <p>Klik pada grafik untuk melihat detail wilayah</p>
        </div>
    </div>
</div>

<!-- Additional Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Trend Produksi per Periode -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Trend Produksi per Periode</h3>
        <div class="mb-4 text-sm text-gray-600">
            <p>Perkembangan produksi dari waktu ke waktu</p>
        </div>
        <div style="height: 300px;">
            <canvas id="chartTrendProduksi"></canvas>
        </div>
    </div>
    
    <!-- Produksi per Kategori -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Produksi per Kategori</h3>
        <div class="mb-4 text-sm text-gray-600">
            <p>Perbandingan produksi HHK vs HHBK</p>
        </div>
        <div style="height: 300px;">
            <canvas id="chartKategori"></canvas>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-4 text-center">
            <div class="bg-blue-50 p-3 rounded-lg">
                <p class="text-2xl font-bold text-blue-600"><?php echo getHHKPercentageFiltered(); ?>%</p>
                <p class="text-xs text-blue-600">HHK</p>
            </div>
            <div class="bg-green-50 p-3 rounded-lg">
                <p class="text-2xl font-bold text-green-600"><?php echo getHHBKPercentageFiltered(); ?>%</p>
                <p class="text-xs text-green-600">HHBK</p>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Data Produksi</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KTH</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komoditas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php echo getProduksiFilteredData(); ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Initialize cascading dropdowns
    $(document).ready(function() {
        // Handle kabupaten change
        $('#kabupatenSelect').on('change', function() {
            const kabupatenId = $(this).val();
            
            // Reset dependent dropdowns
            $('#kecamatanSelect').val('').prop('disabled', !kabupatenId);
            $('#desaSelect').val('').prop('disabled', true);
            
            if (kabupatenId) {
                loadKecamatanFilter(kabupatenId);
            } else {
                $('#kecamatanSelect').html('<option value="">Semua Kecamatan</option>');
                $('#desaSelect').html('<option value="">Semua Desa</option>');
            }
        });

        // Handle kecamatan change
        $('#kecamatanSelect').on('change', function() {
            const kecamatanId = $(this).val();
            
            // Reset desa dropdown
            $('#desaSelect').val('').prop('disabled', !kecamatanId);
            
            if (kecamatanId) {
                loadDesaFilter(kecamatanId);
            } else {
                $('#desaSelect').html('<option value="">Semua Desa</option>');
            }
        });

        // Initialize based on current filter values
        initializeCascadingDropdowns();
    });

    // Initialize cascading dropdowns with current filter values
    function initializeCascadingDropdowns() {
        const kabupatenId = $('#kabupatenSelect').val();
        const kecamatanId = $('#kecamatanSelect').val();
        
        // Set initial disabled state
        $('#kecamatanSelect').prop('disabled', !kabupatenId);
        $('#desaSelect').prop('disabled', !kecamatanId);
        
        if (kabupatenId) {
            loadKecamatanFilter(kabupatenId, kecamatanId);
        }
        if (kecamatanId) {
            loadDesaFilter(kecamatanId);
        }
    }



    // Chart Komoditas - Enhanced
    const ctxKomoditas = document.getElementById('chartKomoditas').getContext('2d');
    new Chart(ctxKomoditas, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(getKomoditasChartLabels()); ?>,
            datasets: [{
                data: <?php echo json_encode(getKomoditasChartData()); ?>,
                backgroundColor: [
                    '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
                    '#8B5CF6', '#06B6D4', '#84CC16', '#F97316'
                ],
                borderColor: [
                    '#2563EB', '#059669', '#D97706', '#DC2626',
                    '#7C3AED', '#0891B2', '#65A30D', '#EA580C'
                ],
                borderWidth: 2,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
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
                }
            }
        }
    });

    // Chart Wilayah - Enhanced
    const ctxWilayah = document.getElementById('chartWilayah').getContext('2d');
    new Chart(ctxWilayah, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(getWilayahChartLabels()); ?>,
            datasets: [{
                label: 'Total Produksi',
                data: <?php echo json_encode(getWilayahChartData()); ?>,
                backgroundColor: '#10B981',
                borderColor: '#059669',
                borderWidth: 2,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
            },
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
            }
        }
    });

    // Chart Trend Produksi - New
    const ctxTrendProduksi = document.getElementById('chartTrendProduksi').getContext('2d');
    new Chart(ctxTrendProduksi, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(getTrendProduksiLabels()); ?>,
            datasets: [{
                label: 'Total Produksi',
                data: <?php echo json_encode(getTrendProduksiData()); ?>,
                borderColor: '#8B5CF6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#8B5CF6',
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
                    borderColor: '#8B5CF6',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            return 'Periode: ' + context[0].label;
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

    // Chart Kategori - New
    const ctxKategori = document.getElementById('chartKategori').getContext('2d');
    new Chart(ctxKategori, {
        type: 'doughnut',
        data: {
            labels: ['HHK (Hasil Hutan Kayu)', 'HHBK (Hasil Hutan Bukan Kayu)'],
            datasets: [{
                data: <?php echo json_encode(getKategoriChartData()); ?>,
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

    function loadKecamatanFilter(kabupatenId, selectedKecamatanId = null) {
        const kecamatanSelect = $('#kecamatanSelect');
        const desaSelect = $('#desaSelect');
        
        // Clear dependent dropdowns
        kecamatanSelect.html('<option value="">Semua Kecamatan</option>');
        desaSelect.html('<option value="">Semua Desa</option>');
        
        if (kabupatenId) {
            fetch(`api/get_kecamatan.php?kabupaten_id=${kabupatenId}`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Semua Kecamatan</option>';
                    
                    if (data && data.length > 0) {
                        data.forEach(item => {
                            const isSelected = selectedKecamatanId && selectedKecamatanId == item.id;
                            options += `<option value="${item.id}" ${isSelected ? 'selected' : ''}>${item.nama}</option>`;
                        });
                    } else {
                        options = '<option value="">Tidak ada kecamatan</option>';
                    }
                    
                    kecamatanSelect.html(options);
                    
                    // If there was a selected kecamatan, load its desa
                    if (selectedKecamatanId) {
                        loadDesaFilter(selectedKecamatanId);
                    }
                })
                .catch(error => {
                    console.error('Error loading kecamatan:', error);
                    kecamatanSelect.html('<option value="">Error loading kecamatan</option>');
                });
        }
    }
    
    function loadDesaFilter(kecamatanId, selectedDesaId = null) {
        const desaSelect = $('#desaSelect');
        
        // Clear dropdown
        desaSelect.html('<option value="">Semua Desa</option>');
        
        if (kecamatanId) {
            fetch(`api/get_desa.php?kecamatan_id=${kecamatanId}`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Semua Desa</option>';
                    
                    if (data && data.length > 0) {
                        data.forEach(item => {
                            const isSelected = selectedDesaId && selectedDesaId == item.id;
                            options += `<option value="${item.id}" ${isSelected ? 'selected' : ''}>${item.nama}</option>`;
                        });
                    } else {
                        options = '<option value="">Tidak ada desa</option>';
                    }
                    
                    desaSelect.html(options);
                })
                .catch(error => {
                    console.error('Error loading desa:', error);
                    desaSelect.html('<option value="">Error loading desa</option>');
                });
        }
    }

    // Enhanced reset function
    function resetFilters() {
        $('#kabupatenSelect').val('');
        $('#kecamatanSelect').val('').prop('disabled', true);
        $('#desaSelect').val('').prop('disabled', true);
        $('#komoditasSelect').val('');
        
        // Clear and reset cascading dropdowns
        $('#kecamatanSelect').html('<option value="">Semua Kecamatan</option>');
        $('#desaSelect').html('<option value="">Semua Desa</option>');
    }

    // Validate filter form before submission
    function validateFilterForm() {
        const kabupatenId = $('#kabupatenSelect').val();
        const kecamatanId = $('#kecamatanSelect').val();
        const desaId = $('#desaSelect').val();
        
        // Check if kecamatan is selected but kabupaten is not
        if (kecamatanId && !kabupatenId) {
            alert('Pilih Kabupaten terlebih dahulu sebelum memilih Kecamatan');
            return false;
        }
        
        // Check if desa is selected but kecamatan is not
        if (desaId && !kecamatanId) {
            alert('Pilih Kecamatan terlebih dahulu sebelum memilih Desa');
            return false;
        }
        
        return true;
    }

    function exportToExcel() {
        // Simple export functionality - in production you might want to use a proper library
        const table = document.querySelector('table');
        const html = table.outerHTML;
        const url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        const downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);
        downloadLink.href = url;
        downloadLink.download = 'laporan_produksi.xls';
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>

<?php
function getTotalProduksiFiltered() {
    try {
        $where = buildWhereClause();
        $query = "SELECT COUNT(*) as total FROM produksi p " . $where['joins'] . " WHERE 1=1 " . $where['conditions'];
        $result = getSingleRow($query, $where['params']);
        return $result ? $result['total'] : '0';
    } catch(Exception $e) {
        return '0';
    }
}

function getTotalVolumeFiltered() {
    try {
        $where = buildWhereClause();
        $query = "SELECT SUM(p.qty) as total FROM produksi p " . $where['joins'] . " WHERE 1=1 " . $where['conditions'];
        $result = getSingleRow($query, $where['params']);
        return number_format($result ? ($result['total'] ?: 0) : 0, 3);
    } catch(Exception $e) {
        return '0.000';
    }
}

function getTotalWilayahFiltered() {
    try {
        $where = buildWhereClause();
        $query = "SELECT COUNT(DISTINCT CONCAT(p.kabupaten_id, '-', p.kecamatan_id, '-', p.desa_id)) as total 
                  FROM produksi p " . $where['joins'] . " WHERE 1=1 " . $where['conditions'];
        $result = getSingleRow($query, $where['params']);
        return $result ? $result['total'] : '0';
    } catch(Exception $e) {
        return '0';
    }
}

function getRataRataPerWilayah() {
    try {
        $where = buildWhereClause();
        $query = "SELECT AVG(avg_qty) as rata_rata FROM (
                    SELECT AVG(p.qty) as avg_qty 
                    FROM produksi p " . $where['joins'] . " 
                    WHERE 1=1 " . $where['conditions'] . "
                    GROUP BY p.kabupaten_id, p.kecamatan_id, p.desa_id
                  ) as subquery";
        $result = getSingleRow($query, $where['params']);
        return number_format($result ? ($result['rata_rata'] ?: 0) : 0, 3);
    } catch(Exception $e) {
        return '0.000';
    }
}

function getProduksiFilteredData() {
    try {
        $where = buildWhereClause();
        $query = "SELECT p.*, k.nama as kabupaten, kec.nama as kecamatan, d.nama as desa,
                         kth.nama as kth_nama, kom.nama as komoditas, kom.kategori as komoditas_kategori, s.nama as satuan
                  FROM produksi p
                  JOIN kabupaten k ON p.kabupaten_id = k.id
                  JOIN kecamatan kec ON p.kecamatan_id = kec.id
                  JOIN desa d ON p.desa_id = d.id
                  LEFT JOIN kth kth ON p.kth_id = kth.id
                  JOIN komoditas kom ON p.komoditas_id = kom.id
                  JOIN satuan s ON p.satuan_id = s.id
                  WHERE 1=1 " . $where['conditions'] . "
                  ORDER BY p.periode DESC, p.created_at DESC";
        
        $rows = getMultipleRows($query, $where['params']);
        
        $output = '';
        if (empty($rows)) {
            $output .= '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data yang ditemukan</td></tr>';
        } else {
            foreach ($rows as $row) {
                $output .= '<tr>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . formatPeriode($row['periode']) . '</td>';
                $output .= '<td class="px-6 py-4 text-sm text-gray-900">' . $row['desa'] . ', ' . $row['kecamatan'] . ', ' . $row['kabupaten'] . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ($row['kth_nama'] ?: '-') . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . $row['komoditas'] . ' (' . $row['komoditas_kategori'] . ')</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . $row['qty'] . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . $row['satuan'] . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ucfirst($row['sumber']) . '</td>';
                $output .= '</tr>';
            }
        }
        return $output;
    } catch(Exception $e) {
        return '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Data tidak tersedia</td></tr>';
    }
}

function getKomoditasChartLabels() {
    try {
        $where = buildWhereClause();
        $query = "SELECT kom.nama, kom.kategori FROM komoditas kom 
                  JOIN produksi p ON kom.id = p.komoditas_id " . $where['joins'] . "
                  WHERE 1=1 " . $where['conditions'] . "
                  GROUP BY kom.id, kom.nama, kom.kategori ORDER BY SUM(p.qty) DESC LIMIT 8";
        $rows = getMultipleRows($query, $where['params']);
        
        $labels = [];
        foreach ($rows as $row) {
            $labels[] = $row['nama'] . ' (' . $row['kategori'] . ')';
        }
        return $labels;
    } catch(Exception $e) {
        return [];
    }
}

function getKomoditasChartData() {
    try {
        $where = buildWhereClause();
        $query = "SELECT SUM(p.qty) as total FROM komoditas kom 
                  JOIN produksi p ON kom.id = p.komoditas_id " . $where['joins'] . "
                  WHERE 1=1 " . $where['conditions'] . "
                  GROUP BY kom.id, kom.nama, kom.kategori ORDER BY SUM(p.qty) DESC LIMIT 8";
        $rows = getMultipleRows($query, $where['params']);
        
        $data = [];
        foreach ($rows as $row) {
            $data[] = $row['total'];
        }
        return $data;
    } catch(Exception $e) {
        return [];
    }
}

function getWilayahChartLabels() {
    try {
        $where = buildWhereClause();
        $query = "SELECT CONCAT(d.nama, ', ', kec.nama) as wilayah FROM produksi p 
                  JOIN desa d ON p.desa_id = d.id 
                  JOIN kecamatan kec ON p.kecamatan_id = kec.id " . $where['joins'] . "
                  WHERE 1=1 " . $where['conditions'] . "
                  GROUP BY p.desa_id, d.nama, kec.nama ORDER BY SUM(p.qty) DESC LIMIT 10";
        $rows = getMultipleRows($query, $where['params']);
        
        $labels = [];
        foreach ($rows as $row) {
            $labels[] = $row['wilayah'];
        }
        return $labels;
    } catch(Exception $e) {
        return [];
    }
}

function getWilayahChartData() {
    try {
        $where = buildWhereClause();
        $query = "SELECT SUM(p.qty) as total FROM produksi p 
                  JOIN desa d ON p.desa_id = d.id 
                  JOIN kecamatan kec ON p.kecamatan_id = kec.id " . $where['joins'] . "
                  WHERE 1=1 " . $where['conditions'] . "
                  GROUP BY p.desa_id, d.nama, kec.nama ORDER BY SUM(p.qty) DESC LIMIT 10";
        $rows = getMultipleRows($query, $where['params']);
        
        $data = [];
        foreach ($rows as $row) {
            $data[] = $row['total'];
        }
        return $data;
    } catch(Exception $e) {
        return [];
    }
}

function getTrendProduksiLabels() {
    try {
        $where = buildWhereClause();
        $query = "SELECT DATE_FORMAT(p.periode, '%Y-%m') as bulan 
                  FROM produksi p " . $where['joins'] . "
                  WHERE 1=1 " . $where['conditions'] . "
                  GROUP BY DATE_FORMAT(p.periode, '%Y-%m') 
                  ORDER BY bulan DESC 
                  LIMIT 12";
        $rows = getMultipleRows($query, $where['params']);
        
        $labels = [];
        foreach ($rows as $row) {
            $labels[] = date('M Y', strtotime($row['bulan'] . '-01'));
        }
        return array_reverse($labels);
    } catch(Exception $e) {
        return [];
    }
}

function getTrendProduksiData() {
    try {
        $where = buildWhereClause();
        $query = "SELECT DATE_FORMAT(p.periode, '%Y-%m') as bulan, SUM(p.qty) as total 
                  FROM produksi p " . $where['joins'] . "
                  WHERE 1=1 " . $where['conditions'] . "
                  GROUP BY DATE_FORMAT(p.periode, '%Y-%m') 
                  ORDER BY bulan DESC 
                  LIMIT 12";
        $rows = getMultipleRows($query, $where['params']);
        
        $data = [];
        foreach ($rows as $row) {
            $data[] = $row['total'] ?: 0;
        }
        return array_reverse($data);
    } catch(Exception $e) {
        return [];
    }
}

function getKategoriChartData() {
    try {
        $where = buildWhereClause();
        $query = "SELECT k.kategori, SUM(p.qty) as total 
                  FROM produksi p 
                  JOIN komoditas k ON p.komoditas_id = k.id " . $where['joins'] . "
                  WHERE 1=1 " . $where['conditions'] . "
                  GROUP BY k.kategori";
        $rows = getMultipleRows($query, $where['params']);
        
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

function getHHKPercentageFiltered() {
    try {
        $data = getKategoriChartData();
        $total = array_sum($data);
        if ($total == 0) return 0;
        return round(($data[0] / $total) * 100, 1);
    } catch(Exception $e) {
        return 0;
    }
}

function getHHBKPercentageFiltered() {
    try {
        $data = getKategoriChartData();
        $total = array_sum($data);
        if ($total == 0) return 0;
        return round(($data[1] / $total) * 100, 1);
    } catch(Exception $e) {
        return 0;
    }
}

function buildWhereClause() {
    global $filter_periode, $filter_kabupaten, $filter_kecamatan, $filter_desa, $filter_komoditas;
    
    $joins = "";
    $conditions = "";
    $params = [];
    
    if ($filter_periode && !empty($filter_periode)) {
        $conditions .= " AND SUBSTRING(p.periode, 1, 7) = ?";
        $params[] = $filter_periode;
    }
    
    if ($filter_kabupaten && !empty($filter_kabupaten)) {
        $conditions .= " AND p.kabupaten_id = ?";
        $params[] = $filter_kabupaten;
    }
    
    if ($filter_kecamatan && !empty($filter_kecamatan)) {
        $conditions .= " AND p.kecamatan_id = ?";
        $params[] = $filter_kecamatan;
    }
    
    if ($filter_desa && !empty($filter_desa)) {
        $conditions .= " AND p.desa_id = ?";
        $params[] = $filter_desa;
    }
    
    if ($filter_komoditas && !empty($filter_komoditas)) {
        $conditions .= " AND p.komoditas_id = ?";
        $params[] = $filter_komoditas;
    }
    
    return ['joins' => $joins, 'conditions' => $conditions, 'params' => $params];
}

function getKabupatenFilterOptions($selected) {
    try {
        $rows = getMultipleRows("SELECT id, nama FROM kabupaten ORDER BY nama");
        
        $output = '';
        if (empty($rows)) {
            $output = '<option value="">Tidak ada kabupaten</option>';
        } else {
            foreach ($rows as $row) {
                $selected_attr = ($selected == $row['id']) ? 'selected' : '';
                $output .= '<option value="' . $row['id'] . '" ' . $selected_attr . '>' . htmlspecialchars($row['nama']) . '</option>';
            }
        }
        return $output;
    } catch(Exception $e) {
        return '';
    }
}

function getKecamatanFilterOptions($kabupaten_id, $selected) {
    try {
        if (!$kabupaten_id || empty($kabupaten_id)) {
            return '<option value="">Pilih Kabupaten terlebih dahulu</option>';
        }
        
        $rows = getMultipleRows("SELECT id, nama FROM kecamatan WHERE kabupaten_id = ? ORDER BY nama", [$kabupaten_id]);
        
        $output = '';
        if (empty($rows)) {
            $output = '<option value="">Tidak ada kecamatan</option>';
        } else {
            foreach ($rows as $row) {
                $selected_attr = ($selected == $row['id']) ? 'selected' : '';
                $output .= '<option value="' . $row['id'] . '" ' . $selected_attr . '>' . htmlspecialchars($row['nama']) . '</option>';
            }
        }
        return $output;
    } catch(Exception $e) {
        return '<option value="">Error loading kecamatan</option>';
    }
}

function getDesaFilterOptions($kecamatan_id, $selected) {
    try {
        if (!$kecamatan_id || empty($kecamatan_id)) {
            return '<option value="">Pilih Kecamatan terlebih dahulu</option>';
        }
        
        $rows = getMultipleRows("SELECT id, nama FROM desa WHERE kecamatan_id = ? ORDER BY nama", [$kecamatan_id]);
        
        $output = '';
        if (empty($rows)) {
            $output = '<option value="">Tidak ada desa</option>';
        } else {
            foreach ($rows as $row) {
                $selected_attr = ($selected == $row['id']) ? 'selected' : '';
                $output .= '<option value="' . $row['id'] . '" ' . $selected_attr . '>' . htmlspecialchars($row['nama']) . '</option>';
            }
        }
        return $output;
    } catch(Exception $e) {
        return '<option value="">Error loading desa</option>';
    }
}

function getKomoditasFilterOptions($selected) {
    try {
        $rows = getMultipleRows("SELECT id, nama, kategori FROM komoditas WHERE aktif = 1 ORDER BY kategori, nama");
        
        $output = '';
        $current_kategori = '';
        
        foreach ($rows as $row) {
            if ($current_kategori != $row['kategori']) {
                if ($current_kategori != '') {
                    $output .= '</optgroup>';
                }
                $current_kategori = $row['kategori'];
                $kategori_label = ($row['kategori'] == 'HHK') ? 'Hasil Hutan Kayu' : 'Hasil Hutan Bukan Kayu';
                $output .= '<optgroup label="' . $kategori_label . ' (' . $row['kategori'] . ')">';
            }
            
            $selected_attr = ($selected == $row['id']) ? 'selected' : '';
            $output .= '<option value="' . $row['id'] . '" ' . $selected_attr . '>' . htmlspecialchars($row['nama']) . '</option>';
        }
        
        if ($current_kategori != '') {
            $output .= '</optgroup>';
        }
        
        return $output;
    } catch(Exception $e) {
        return '';
    }
}

function formatPeriode($periode) {
    if (!$periode) return '-';
    
    // Format periode dari YYYY-MM-DD menjadi "Bulan Tahun"
    $bulan_names = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    
    // Handle format YYYY-MM-DD
    if (preg_match('/^(\d{4})-(\d{2})-\d{2}$/', $periode, $matches)) {
        $tahun = $matches[1];
        $bulan = $matches[2];
        return isset($bulan_names[$bulan]) ? $bulan_names[$bulan] . ' ' . $tahun : $periode;
    }
    
    // Handle format MM/YYYY (fallback)
    $parts = explode('/', $periode);
    if (count($parts) == 2) {
        $bulan = $parts[0];
        $tahun = $parts[1];
        return isset($bulan_names[$bulan]) ? $bulan_names[$bulan] . ' ' . $tahun : $periode;
    }
    
    return $periode;
}

include 'includes/footer.php';
?> 