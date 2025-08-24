<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
require_once 'components/DataTable.php';

$page_title = 'Data Produksi';
$page_description = 'Kelola data produksi HHK dan HHBK';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                addProduksi($_POST);
                break;
            case 'edit':
                editProduksi($_POST);
                break;
            case 'delete':
                deleteProduksi($_POST['id']);
                break;
        }
    }
}

function addProduksi($data) {
    global $success, $error;
    try {
        // Format periode dari bulan dan tahun
        $periode = $data['periode_bulan'] . '/' . $data['periode_tahun'];
        
        $result = insertData("INSERT INTO produksi (periode, kabupaten_id, kecamatan_id, desa_id, kth_id, penyuluh_id, komoditas_id, qty, satuan_id, sumber, tanggal_input, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
            $periode,
            $data['kabupaten_id'],
            $data['kecamatan_id'],
            $data['desa_id'],
            $data['kth_id'] ?: null,
            $data['penyuluh_id'] ?: null,
            $data['komoditas_id'],
            $data['qty'],
            $data['satuan_id'],
            'input', // Sumber otomatis 'input' untuk form input
            $data['tanggal_input'],
            $_SESSION['user_id']
        ]);
        if ($result) {
            $success = "Data produksi berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan data produksi!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

function editProduksi($data) {
    global $success, $error;
    try {
        // Format periode dari bulan dan tahun
        $periode = $data['periode_bulan'] . '/' . $data['periode_tahun'];
        
        $result = updateData("UPDATE produksi SET periode = ?, kabupaten_id = ?, kecamatan_id = ?, desa_id = ?, kth_id = ?, penyuluh_id = ?, komoditas_id = ?, qty = ?, satuan_id = ?, tanggal_input = ? WHERE id = ?", [
            $periode,
            $data['kabupaten_id'],
            $data['kecamatan_id'],
            $data['desa_id'],
            $data['kth_id'] ?: null,
            $data['penyuluh_id'] ?: null,
            $data['komoditas_id'],
            $data['qty'],
            $data['satuan_id'],
            $data['tanggal_input'],
            $data['id']
        ]);
        if ($result) {
            $success = "Data produksi berhasil diupdate!";
        } else {
            $error = "Gagal mengupdate data produksi!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

function deleteProduksi($id) {
    global $success, $error;
    try {
        $result = deleteData("DELETE FROM produksi WHERE id = ?", [$id]);
        if ($result) {
            $success = "Data produksi berhasil dihapus!";
        } else {
            $error = "Gagal menghapus data produksi!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Initialize DataTable
$db = getDatabaseConnection();
initDataTable(
    $db, 
    'produksi', 
    ['id', 'periode', 'kabupaten_id', 'kecamatan_id', 'desa_id', 'kth_id', 'penyuluh_id', 'komoditas_id', 'qty', 'satuan_id', 'sumber', 'tanggal_input', 'created_at'],
    ['periode'], // searchable columns
    ['periode', 'qty', 'created_at'] // orderable columns
);

include 'includes/header.php';
?>

<!-- Page Content -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="text-lg font-medium text-gray-900">Data Produksi</h3>
        <p class="text-sm text-gray-600">Kelola data produksi HHK dan HHBK</p>
    </div>
    <div class="flex space-x-3">
        <button onclick="showHelp()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Bantuan
        </button>
        <a href="import_produksi.php" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
            </svg>
            Import Excel
        </a>
        <button onclick="openModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Produksi
        </button>
    </div>
</div>

<!-- Info Card -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <div class="flex items-start">
        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h4 class="text-sm font-medium text-blue-800 mb-1">Panduan Input Data Produksi</h4>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• <strong>Periode:</strong> Pilih bulan dan tahun produksi (contoh: Januari 2024)</li>
                <li>• <strong>Wilayah:</strong> Pilih Kabupaten → Kecamatan → Desa → KTH (opsional)</li>
                <li>• <strong>Komoditas:</strong> Pilih jenis komoditas HHK atau HHBK</li>
                <li>• <strong>Jumlah:</strong> Masukkan jumlah produksi dalam angka (bisa desimal)</li>
                <li>• <strong>Tanggal Input:</strong> Tanggal ketika data diinput (default: hari ini)</li>
                <li>• <strong>Import Excel:</strong> Untuk data dalam jumlah besar, gunakan tombol "Import Excel"</li>
            </ul>
        </div>
    </div>
</div>

<!-- Filter Periode -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Periode</h3>
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
            <select name="filter_bulan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Semua Bulan</option>
                <option value="01" <?php echo (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] == '01') ? 'selected' : ''; ?>>Januari</option>
                <option value="02" <?php echo (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] == '02') ? 'selected' : ''; ?>>Februari</option>
                <option value="03" <?php echo (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] == '03') ? 'selected' : ''; ?>>Maret</option>
                <option value="04" <?php echo (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] == '04') ? 'selected' : ''; ?>>April</option>
                <option value="05" <?php echo (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] == '05') ? 'selected' : ''; ?>>Mei</option>
                <option value="06" <?php echo (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] == '06') ? 'selected' : ''; ?>>Juni</option>
                <option value="07" <?php echo (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] == '07') ? 'selected' : ''; ?>>Juli</option>
                <option value="08" <?php echo (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] == '08') ? 'selected' : ''; ?>>Agustus</option>
                <option value="09" <?php echo (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] == '09') ? 'selected' : ''; ?>>September</option>
                <option value="10" <?php echo (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] == '10') ? 'selected' : ''; ?>>Oktober</option>
                <option value="11" <?php echo (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] == '11') ? 'selected' : ''; ?>>November</option>
                <option value="12" <?php echo (isset($_GET['filter_bulan']) && $_GET['filter_bulan'] == '12') ? 'selected' : ''; ?>>Desember</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
            <select name="filter_tahun" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Semua Tahun</option>
                <?php 
                $currentYear = date('Y');
                for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                    $selected = (isset($_GET['filter_tahun']) && $_GET['filter_tahun'] == $year) ? 'selected' : '';
                    echo "<option value=\"$year\" $selected>$year</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="flex items-end space-x-3">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">
                Filter
            </button>
            <a href="produksi.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Search and Filter -->
<form method="GET" class="mb-4">
    <?php 
    // Preserve filter parameters in search
    $filter_params = '';
    if (isset($_GET['filter_bulan']) && $_GET['filter_bulan']) {
        $filter_params .= '&filter_bulan=' . $_GET['filter_bulan'];
    }
    if (isset($_GET['filter_tahun']) && $_GET['filter_tahun']) {
        $filter_params .= '&filter_tahun=' . $_GET['filter_tahun'];
    }
    echo str_replace('action=search', 'action=search' . $filter_params, renderDataTableSearchBox()); 
    ?>
</form>

<!-- Data Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <?php echo renderDataTableSortableHeader('periode', 'Periode', 'produksi.php'); ?>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KTH</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penyuluh</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komoditas</th>
                    <?php echo renderDataTableSortableHeader('qty', 'Jumlah', 'produksi.php'); ?>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php echo getProduksiData(); ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php 
    // Build filter conditions for pagination
    $pagination_where_conditions = [];
    $pagination_where_params = [];
    
    if (isset($_GET['filter_bulan']) && $_GET['filter_bulan']) {
        $pagination_where_conditions[] = "SUBSTRING(p.periode, 6, 2) = ?";
        $pagination_where_params[] = $_GET['filter_bulan'];
    }
    
    if (isset($_GET['filter_tahun']) && $_GET['filter_tahun']) {
        $pagination_where_conditions[] = "SUBSTRING(p.periode, 1, 4) = ?";
        $pagination_where_params[] = $_GET['filter_tahun'];
    }
    
    // Get pagination data from filtered results
    $pagination_data = getDataTableDataWithFilter($pagination_where_conditions, $pagination_where_params);
    
    // Build pagination with filter parameters
    $filter_params = '';
    if (isset($_GET['filter_bulan']) && $_GET['filter_bulan']) {
        $filter_params .= '&filter_bulan=' . $_GET['filter_bulan'];
    }
    if (isset($_GET['filter_tahun']) && $_GET['filter_tahun']) {
        $filter_params .= '&filter_tahun=' . $_GET['filter_tahun'];
    }
    
    echo renderCustomPagination($pagination_data, 'produksi.php' . $filter_params); 
    ?>
</div>

<!-- Modal Form -->
<div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Tambah Data Produksi</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="produksiForm" method="POST" class="space-y-6">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="formId">
                
                <!-- Periode dan Tanggal Input -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h4 class="text-sm font-semibold text-blue-800 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Informasi Periode
                    </h4>
                    <p class="text-xs text-blue-700 mb-3">Pilih periode bulan dan tahun produksi, serta tanggal input data</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                            <select name="periode_bulan" id="periode_bulan" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Bulan</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                            <select name="periode_tahun" id="periode_tahun" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Tahun</option>
                                <?php 
                                $currentYear = date('Y');
                                for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                                    echo "<option value=\"$year\">$year</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Input</label>
                            <input type="date" name="tanggal_input" id="tanggal_input" required 
                                   value="<?php echo date('Y-m-d'); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Wilayah -->
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <h4 class="text-sm font-semibold text-green-800 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Informasi Wilayah
                    </h4>
                    <p class="text-xs text-green-700 mb-3">Pilih lokasi wilayah produksi (Kabupaten → Kecamatan → Desa → KTH)</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kabupaten <span class="text-red-500">*</span></label>
                            <select name="kabupaten_id" id="kabupaten_id" required onchange="loadKecamatan()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Pilih Kabupaten</option>
                                <?php echo getKabupatenOptions(); ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kecamatan <span class="text-red-500">*</span></label>
                            <select name="kecamatan_id" id="kecamatan_id" required onchange="loadDesa()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Desa <span class="text-red-500">*</span></label>
                            <select name="desa_id" id="desa_id" required onchange="loadKTH()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Pilih Desa</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">KTH (Opsional)</label>
                            <select name="kth_id" id="kth_id"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Pilih KTH</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Produksi -->
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <h4 class="text-sm font-semibold text-yellow-800 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Informasi Produksi
                    </h4>
                    <p class="text-xs text-yellow-700 mb-3">Masukkan detail komoditas, jumlah produksi, dan satuan</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Penyuluh (Opsional)</label>
                            <select name="penyuluh_id" id="penyuluh_id"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                <option value="">Pilih Penyuluh</option>
                                <?php echo getPenyuluhOptions(); ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Komoditas <span class="text-red-500">*</span></label>
                            <select name="komoditas_id" id="komoditas_id" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                <option value="">Pilih Komoditas</option>
                                <?php echo getKomoditasOptions(); ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah <span class="text-red-500">*</span></label>
                            <input type="number" name="qty" id="qty" step="0.001" min="0" required 
                                   placeholder="Contoh: 100.500"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                            <p class="text-xs text-gray-500 mt-1">Masukkan jumlah produksi dalam angka (bisa desimal)</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Satuan <span class="text-red-500">*</span></label>
                            <select name="satuan_id" id="satuan_id" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                <option value="">Pilih Satuan</option>
                                <?php echo getSatuanOptions(); ?>
                            </select>
                        </div>
                        

                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal()" 
                            class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    
    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }
    
    function editProduksi(id, periode, kabupaten_id, kecamatan_id, desa_id, kth_id, penyuluh_id, komoditas_id, qty, satuan_id, sumber, tanggal_input) {
        document.getElementById('modalTitle').textContent = 'Edit Data Produksi';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('formId').value = id;
        
        // Parse periode (format: MM/YYYY)
        if (periode) {
            const [bulan, tahun] = periode.split('/');
            document.getElementById('periode_bulan').value = bulan;
            document.getElementById('periode_tahun').value = tahun;
        }
        
        document.getElementById('tanggal_input').value = tanggal_input || new Date().toISOString().split('T')[0];
        document.getElementById('kabupaten_id').value = kabupaten_id;
        document.getElementById('komoditas_id').value = komoditas_id;
        document.getElementById('qty').value = qty;
        document.getElementById('satuan_id').value = satuan_id;
        
        // Load dependent dropdowns
        if (kabupaten_id) {
            loadKecamatan(kecamatan_id);
        }
        if (kecamatan_id) {
            loadDesa(desa_id);
        }
        if (desa_id) {
            loadKTH(kth_id);
        }
        if (penyuluh_id) {
            document.getElementById('penyuluh_id').value = penyuluh_id;
        }
        
        document.getElementById('modal').classList.remove('hidden');
    }
    
    function deleteProduksi(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data produksi ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="${id}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    function loadKecamatan(selectedId = null) {
        const kabupatenId = document.getElementById('kabupaten_id').value;
        const kecamatanSelect = document.getElementById('kecamatan_id');
        
        kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
        document.getElementById('desa_id').innerHTML = '<option value="">Pilih Desa</option>';
        document.getElementById('kth_id').innerHTML = '<option value="">Pilih KTH</option>';
        
        if (kabupatenId) {
            fetch(`api/get_kecamatan.php?kabupaten_id=${kabupatenId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.nama;
                        if (selectedId && selectedId == item.id) {
                            option.selected = true;
                        }
                        kecamatanSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading kecamatan:', error);
                });
        }
    }
    
    function loadDesa(selectedId = null) {
        const kecamatanId = document.getElementById('kecamatan_id').value;
        const desaSelect = document.getElementById('desa_id');
        
        desaSelect.innerHTML = '<option value="">Pilih Desa</option>';
        document.getElementById('kth_id').innerHTML = '<option value="">Pilih KTH</option>';
        
        if (kecamatanId) {
            fetch(`api/get_desa.php?kecamatan_id=${kecamatanId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.nama;
                        if (selectedId && selectedId == item.id) {
                            option.selected = true;
                        }
                        desaSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading desa:', error);
                });
        }
    }
    
    function loadKTH(selectedId = null) {
        const desaId = document.getElementById('desa_id').value;
        const kthSelect = document.getElementById('kth_id');
        
        kthSelect.innerHTML = '<option value="">Pilih KTH</option>';
        
        if (desaId) {
            fetch(`api/get_kth.php?desa_id=${desaId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.nama;
                        if (selectedId && selectedId == item.id) {
                            option.selected = true;
                        }
                        kthSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading KTH:', error);
                });
        }
    }
    
    // Form validation
    document.getElementById('produksiForm').addEventListener('submit', function(e) {
        const periodeBulan = document.getElementById('periode_bulan').value;
        const periodeTahun = document.getElementById('periode_tahun').value;
        const qty = document.getElementById('qty').value;
        
        if (!periodeBulan || !periodeTahun) {
            e.preventDefault();
            alert('Periode bulan dan tahun harus diisi!');
            return false;
        }
        
        if (!qty || parseFloat(qty) <= 0) {
            e.preventDefault();
            alert('Jumlah produksi harus lebih dari 0!');
            return false;
        }
        
        // Show loading state
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Menyimpan...';
        submitBtn.disabled = true;
        
        // Re-enable after 3 seconds if form doesn't submit
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });
    
    // Auto-focus on first field when modal opens
    function openModal() {
        document.getElementById('modal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Tambah Data Produksi';
        document.getElementById('formAction').value = 'add';
        document.getElementById('produksiForm').reset();
        
        // Set default tanggal input to today
        document.getElementById('tanggal_input').value = new Date().toISOString().split('T')[0];
        
        // Reset dependent dropdowns
        document.getElementById('kecamatan_id').innerHTML = '<option value="">Pilih Kecamatan</option>';
        document.getElementById('desa_id').innerHTML = '<option value="">Pilih Desa</option>';
        document.getElementById('kth_id').innerHTML = '<option value="">Pilih KTH</option>';
        
        // Focus on first field
        setTimeout(() => {
            document.getElementById('periode_bulan').focus();
        }, 100);
    }
    
    // Show help modal
    function showHelp() {
        const helpContent = `
            <div class="bg-white p-6 rounded-lg max-w-2xl mx-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Panduan Input Data Produksi</h3>
                    <button onclick="closeHelp()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-4 text-sm text-gray-700">
                    <div>
                        <h4 class="font-medium text-blue-800 mb-2">📅 Informasi Periode</h4>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li><strong>Bulan:</strong> Pilih bulan produksi (Januari - Desember)</li>
                            <li><strong>Tahun:</strong> Pilih tahun produksi</li>
                            <li><strong>Tanggal Input:</strong> Tanggal ketika data diinput (default: hari ini)</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-green-800 mb-2">📍 Informasi Wilayah</h4>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li><strong>Kabupaten:</strong> Pilih kabupaten lokasi produksi</li>
                            <li><strong>Kecamatan:</strong> Akan muncul setelah memilih kabupaten</li>
                            <li><strong>Desa:</strong> Akan muncul setelah memilih kecamatan</li>
                            <li><strong>KTH:</strong> Kelompok Tani Hutan (opsional)</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-yellow-800 mb-2">📊 Informasi Produksi</h4>
                                                 <ul class="list-disc list-inside space-y-1 ml-4">
                             <li><strong>Penyuluh:</strong> Penyuluh pertanian (opsional)</li>
                             <li><strong>Komoditas:</strong> Pilih jenis komoditas HHK atau HHBK</li>
                             <li><strong>Jumlah:</strong> Masukkan jumlah produksi (contoh: 100.500)</li>
                             <li><strong>Satuan:</strong> Pilih satuan (KG, Batang, Liter, dll)</li>
                         </ul>
                    </div>
                                         <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                         <p class="text-yellow-800"><strong>💡 Tips:</strong></p>
                         <ul class="list-disc list-inside space-y-1 ml-4 mt-1">
                             <li>Semua field bertanda <span class="text-red-500">*</span> wajib diisi</li>
                             <li>Jumlah produksi harus lebih dari 0</li>
                             <li>Data akan tersimpan setelah klik tombol "Simpan Data"</li>
                             <li>Untuk data dalam jumlah besar, gunakan fitur <strong>Import Excel</strong></li>
                         </ul>
                     </div>
                </div>
            </div>
        `;
        
        const helpModal = document.createElement('div');
        helpModal.id = 'helpModal';
        helpModal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50';
        helpModal.innerHTML = helpContent;
        document.body.appendChild(helpModal);
    }
    
    function closeHelp() {
        const helpModal = document.getElementById('helpModal');
        if (helpModal) {
            helpModal.remove();
        }
    }
</script>

<?php
function getProduksiData() {
    try {
        // Apply period filter
        $where_conditions = [];
        $where_params = [];
        
        if (isset($_GET['filter_bulan']) && $_GET['filter_bulan']) {
            $where_conditions[] = "SUBSTRING(p.periode, 6, 2) = ?";
            $where_params[] = $_GET['filter_bulan'];
        }
        
        if (isset($_GET['filter_tahun']) && $_GET['filter_tahun']) {
            $where_conditions[] = "SUBSTRING(p.periode, 1, 4) = ?";
            $where_params[] = $_GET['filter_tahun'];
        }
        
        // Get filtered data
        $result = getDataTableDataWithFilter($where_conditions, $where_params);
        $data = $result['data'];
        
        $output = '';
        if (empty($data)) {
            $output .= '<tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">Tidak ada data yang ditemukan</td></tr>';
        } else {
            foreach ($data as $row) {
                $wilayahInfo = getWilayahInfo($row['kabupaten_id'], $row['kecamatan_id'], $row['desa_id']);
                $kthInfo = getKTHInfo($row['kth_id']);
                $penyuluhInfo = getPenyuluhInfo($row['penyuluh_id']);
                $komoditasInfo = getKomoditasInfo($row['komoditas_id']);
                $satuanInfo = getSatuanInfo($row['satuan_id']);
                
                $output .= '<tr>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . formatPeriode($row['periode']) . '</td>';
                $output .= '<td class="px-6 py-4 text-sm text-gray-900">' . $wilayahInfo . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ($kthInfo ?: '-') . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ($penyuluhInfo ?: '-') . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . $komoditasInfo . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . number_format($row['qty'], 3) . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . $satuanInfo . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ($row['sumber'] == 'input' ? 'Input Manual' : 'Import Data') . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">';
                $output .= '<button onclick="editProduksi(' . $row['id'] . ', \'' . $row['periode'] . '\', ' . $row['kabupaten_id'] . ', ' . $row['kecamatan_id'] . ', ' . $row['desa_id'] . ', ' . ($row['kth_id'] ?: 'null') . ', ' . ($row['penyuluh_id'] ?: 'null') . ', ' . $row['komoditas_id'] . ', ' . $row['qty'] . ', ' . $row['satuan_id'] . ', \'' . $row['sumber'] . '\', \'' . $row['tanggal_input'] . '\')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>';
                $output .= '<button onclick="deleteProduksi(' . $row['id'] . ')" class="text-red-600 hover:text-red-900">Hapus</button>';
                $output .= '</td>';
                $output .= '</tr>';
            }
        }
        
        return $output;
    } catch(Exception $e) {
        return '<tr><td colspan="9" class="px-6 py-4 text-center text-red-600">Error: ' . $e->getMessage() . '</td></tr>';
    }
}

function getWilayahInfo($kabupaten_id, $kecamatan_id, $desa_id) {
    try {
        $result = getSingleRow("
            SELECT d.nama as desa, k.nama as kecamatan, kab.nama as kabupaten 
            FROM desa d 
            JOIN kecamatan k ON d.kecamatan_id = k.id 
            JOIN kabupaten kab ON k.kabupaten_id = kab.id 
            WHERE d.id = ?
        ", [$desa_id]);
        
        if ($result) {
            return htmlspecialchars($result['desa']) . ', ' . htmlspecialchars($result['kecamatan']) . ', ' . htmlspecialchars($result['kabupaten']);
        }
        return '-';
    } catch(Exception $e) {
        return '-';
    }
}

function getKTHInfo($kth_id) {
    if (!$kth_id) return null;
    try {
        $result = getSingleRow("SELECT nama FROM kth WHERE id = ?", [$kth_id]);
        return $result ? htmlspecialchars($result['nama']) : null;
    } catch(Exception $e) {
        return null;
    }
}

function getPenyuluhInfo($penyuluh_id) {
    if (!$penyuluh_id) return null;
    try {
        $result = getSingleRow("SELECT nama FROM penyuluh WHERE id = ?", [$penyuluh_id]);
        return $result ? htmlspecialchars($result['nama']) : null;
    } catch(Exception $e) {
        return null;
    }
}

function getKomoditasInfo($komoditas_id) {
    try {
        $result = getSingleRow("SELECT nama, kategori FROM komoditas WHERE id = ?", [$komoditas_id]);
        if ($result) {
            return htmlspecialchars($result['nama']) . ' (' . $result['kategori'] . ')';
        }
        return '-';
    } catch(Exception $e) {
        return '-';
    }
}

function getSatuanInfo($satuan_id) {
    try {
        $result = getSingleRow("SELECT nama FROM satuan WHERE id = ?", [$satuan_id]);
        return $result ? htmlspecialchars($result['nama']) : '-';
    } catch(Exception $e) {
        return '-';
    }
}

function getKabupatenOptions() {
    try {
        $rows = getMultipleRows("SELECT id, nama FROM kabupaten ORDER BY nama");
        $output = '';
        foreach ($rows as $row) {
            $output .= '<option value="' . $row['id'] . '">' . htmlspecialchars($row['nama']) . '</option>';
        }
        return $output;
    } catch(Exception $e) {
        return '';
    }
}

function getPenyuluhOptions() {
    try {
        $rows = getMultipleRows("SELECT id, nama FROM penyuluh ORDER BY nama");
        $output = '';
        foreach ($rows as $row) {
            $output .= '<option value="' . $row['id'] . '">' . htmlspecialchars($row['nama']) . '</option>';
        }
        return $output;
    } catch(Exception $e) {
        return '';
    }
}

function getKomoditasOptions() {
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
            
            $output .= '<option value="' . $row['id'] . '">' . htmlspecialchars($row['nama']) . '</option>';
        }
        
        if ($current_kategori != '') {
            $output .= '</optgroup>';
        }
        
        return $output;
    } catch(Exception $e) {
        return '';
    }
}

function getSatuanOptions() {
    try {
        $rows = getMultipleRows("SELECT id, nama FROM satuan ORDER BY nama");
        $output = '';
        foreach ($rows as $row) {
            $output .= '<option value="' . $row['id'] . '">' . htmlspecialchars($row['nama']) . '</option>';
        }
        return $output;
    } catch(Exception $e) {
        return '';
    }
}

function getDataTableDataWithFilter($where_conditions = [], $where_params = []) {
    try {
        $db = getDatabaseConnection();
        
        // Build WHERE clause
        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        }
        
        // Get total count for pagination
        $count_query = "SELECT COUNT(*) as total FROM produksi p " . $where_clause;
        $count_stmt = $db->prepare($count_query);
        $count_stmt->execute($where_params);
        $total_records = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Get paginated data
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        // Build ORDER BY clause
        $order_by = 'ORDER BY p.created_at DESC';
        if (isset($_GET['sort']) && isset($_GET['order'])) {
            $sort_column = $_GET['sort'];
            $sort_order = $_GET['order'];
            $allowed_columns = ['periode', 'qty', 'created_at'];
            
            if (in_array($sort_column, $allowed_columns)) {
                $order_by = "ORDER BY p.$sort_column $sort_order";
            }
        }
        
        // Main query
        $query = "SELECT p.* FROM produksi p " . $where_clause . " " . $order_by . " LIMIT $limit OFFSET $offset";
        $stmt = $db->prepare($query);
        $stmt->execute($where_params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'data' => $data,
            'total' => $total_records,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total_records / $limit)
        ];
    } catch(Exception $e) {
        return [
            'data' => [],
            'total' => 0,
            'page' => 1,
            'limit' => 10,
            'total_pages' => 0
        ];
    }
}

function renderCustomPagination($pagination_data, $base_url) {
    $current_page = $pagination_data['page'];
    $total_pages = $pagination_data['total_pages'];
    $total_records = $pagination_data['total'];
    $limit = $pagination_data['limit'];
    
    if ($total_pages <= 1) {
        return '';
    }
    
    $output = '<div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">';
    $output .= '<div class="flex-1 flex justify-between sm:hidden">';
    
    // Previous button for mobile
    if ($current_page > 1) {
        $output .= '<a href="' . $base_url . '&page=' . ($current_page - 1) . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</a>';
    } else {
        $output .= '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">Previous</span>';
    }
    
    // Next button for mobile
    if ($current_page < $total_pages) {
        $output .= '<a href="' . $base_url . '&page=' . ($current_page + 1) . '" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</a>';
    } else {
        $output .= '<span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">Next</span>';
    }
    
    $output .= '</div>';
    
    // Desktop pagination
    $output .= '<div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">';
    $output .= '<div>';
    $output .= '<p class="text-sm text-gray-700">Showing <span class="font-medium">' . (($current_page - 1) * $limit + 1) . '</span> to <span class="font-medium">' . min($current_page * $limit, $total_records) . '</span> of <span class="font-medium">' . $total_records . '</span> results</p>';
    $output .= '</div>';
    
    $output .= '<div>';
    $output .= '<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">';
    
    // Previous button
    if ($current_page > 1) {
        $output .= '<a href="' . $base_url . '&page=' . ($current_page - 1) . '" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">';
        $output .= '<span class="sr-only">Previous</span>';
        $output .= '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
        $output .= '</a>';
    } else {
        $output .= '<span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed">';
        $output .= '<span class="sr-only">Previous</span>';
        $output .= '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
        $output .= '</span>';
    }
    
    // Page numbers
    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $current_page + 2);
    
    for ($i = $start_page; $i <= $end_page; $i++) {
        if ($i == $current_page) {
            $output .= '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-green-50 text-sm font-medium text-green-600">' . $i . '</span>';
        } else {
            $output .= '<a href="' . $base_url . '&page=' . $i . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">' . $i . '</a>';
        }
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $output .= '<a href="' . $base_url . '&page=' . ($current_page + 1) . '" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">';
        $output .= '<span class="sr-only">Next</span>';
        $output .= '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>';
        $output .= '</a>';
    } else {
        $output .= '<span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed">';
        $output .= '<span class="sr-only">Next</span>';
        $output .= '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>';
        $output .= '</span>';
    }
    
    $output .= '</nav>';
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>';
    
    return $output;
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