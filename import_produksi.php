<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
require_once 'components/DataTable.php';

$page_title = 'Import Data Produksi';
$page_description = 'Import data produksi dari file Excel';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
    // Debug information
    error_log("File upload received: " . print_r($_FILES['excel_file'], true));
    
    if ($_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize)',
            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE)',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
            UPLOAD_ERR_NO_TMP_DIR => 'Tidak ada temporary directory',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh extension'
        ];
        $error = $uploadErrors[$_FILES['excel_file']['error']] ?? 'Error upload tidak diketahui';
    } else {
        $result = importExcelData($_FILES['excel_file']);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

function importExcelData($file) {
    error_log("Starting importExcelData function");
    
    // Check file type - allow multiple possible MIME types for CSV files
    $allowedTypes = [
        'application/vnd.ms-excel', 
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
        'text/csv',
        'text/plain',  // CSV sometimes detected as text/plain
        'application/csv',  // Another possible CSV MIME type
        'text/comma-separated-values'  // Alternative CSV MIME type
    ];
    
    // Get file extension
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    error_log("File extension: $fileExtension");
    
    // Check if it's a valid extension first
    $allowedExtensions = ['csv', 'xls', 'xlsx'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        error_log("File extension not allowed: $fileExtension");
        return ['success' => false, 'message' => 'File harus berformat Excel (.xls, .xlsx) atau CSV'];
    }
    
    // Check MIME type if function exists
    if (function_exists('mime_content_type')) {
        $fileType = mime_content_type($file['tmp_name']);
        error_log("File type detected (mime_content_type): $fileType");
        
        // For CSV files, be more lenient with MIME type detection
        if ($fileExtension === 'csv') {
            if (!in_array($fileType, $allowedTypes)) {
                error_log("CSV file MIME type not recognized: $fileType, but accepting based on extension");
            }
        } else {
            // For Excel files, check MIME type strictly
            if (!in_array($fileType, $allowedTypes)) {
                error_log("File type not allowed: $fileType");
                return ['success' => false, 'message' => 'File harus berformat Excel (.xls, .xlsx) atau CSV'];
            }
        }
    } else {
        error_log("mime_content_type function not available, using extension only");
    }
    
    // Read file content
    $fileContent = file_get_contents($file['tmp_name']);
    error_log("File content length: " . strlen($fileContent));
    
    $lines = explode("\n", $fileContent);
    error_log("Number of lines before filtering: " . count($lines));
    
    // Remove empty lines
    $lines = array_filter($lines, function($line) {
        return trim($line) !== '';
    });
    
    error_log("Number of lines after filtering: " . count($lines));
    
    if (count($lines) < 2) {
        error_log("File has insufficient lines");
        return ['success' => false, 'message' => 'File harus memiliki header dan minimal 1 baris data'];
    }
    
    // Parse header
    $header = str_getcsv(array_shift($lines), ',');
    error_log("Parsed headers: " . print_r($header, true));
    
    $expectedHeaders = ['periode', 'kabupaten', 'kecamatan', 'desa', 'kth', 'penyuluh', 'komoditas', 'kategori', 'qty', 'satuan'];
    
    // Validate header
    foreach ($expectedHeaders as $expected) {
        if (!in_array($expected, $header)) {
            error_log("Missing header: $expected");
            return ['success' => false, 'message' => "Header '$expected' tidak ditemukan dalam file Excel"];
        }
    }
    
    error_log("Headers validation passed");
    
    $db = getDatabaseConnection();
    
    $successCount = 0;
    $errorCount = 0;
    $errors = [];
    
    // Begin transaction
    $db->beginTransaction();
    
    try {
        foreach ($lines as $lineNumber => $line) {
            $data = str_getcsv($line, ',');
            
            // Skip if data count doesn't match header count
            if (count($data) !== count($header)) {
                $errorCount++;
                $errors[] = "Baris " . ($lineNumber + 2) . ": Jumlah kolom tidak sesuai";
                continue;
            }
            
            // Create associative array
            $rowData = array_combine($header, $data);
            
            // Validate and import row
            $result = validateAndImportRow($db, $rowData, $lineNumber + 2);
            if ($result['success']) {
                $successCount++;
            } else {
                $errorCount++;
                $errors[] = "Baris " . ($lineNumber + 2) . ": " . $result['message'];
            }
        }
        
        // Commit transaction if all successful
        $db->commit();
        
        $message = "Import selesai. Berhasil: $successCount, Gagal: $errorCount";
        if (!empty($errors)) {
            $message .= ". Detail error: " . implode('; ', array_slice($errors, 0, 5));
        }
        
        return ['success' => true, 'message' => $message];
        
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function validateAndImportRow($db, $rowData, $lineNumber) {
    try {
        // Validate required fields
        $requiredFields = ['periode', 'kabupaten', 'kecamatan', 'desa', 'komoditas', 'kategori', 'qty', 'satuan'];
        foreach ($requiredFields as $field) {
            if (empty(trim($rowData[$field]))) {
                return ['success' => false, 'message' => "Field $field tidak boleh kosong"];
            }
        }
        
        // Validate date format
        $periode = trim($rowData['periode']);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $periode)) {
            return ['success' => false, 'message' => 'Format periode harus YYYY-MM-DD'];
        }
        
        // Validate quantity
        $qty = floatval($rowData['qty']);
        if ($qty <= 0) {
            return ['success' => false, 'message' => 'Quantity harus lebih dari 0'];
        }
        
        // Validate kategori
        $kategori = strtoupper(trim($rowData['kategori']));
        if (!in_array($kategori, ['HHK', 'HHBK'])) {
            return ['success' => false, 'message' => 'Kategori harus HHK atau HHBK'];
        }
        
        // Get IDs from names
        $kabupatenId = getKabupatenIdByName($db, trim($rowData['kabupaten']));
        if (!$kabupatenId) {
            return ['success' => false, 'message' => "Kabupaten '{$rowData['kabupaten']}' tidak ditemukan dalam database"];
        }
        
        $kecamatanId = getKecamatanIdByName($db, $kabupatenId, trim($rowData['kecamatan']));
        if (!$kecamatanId) {
            return ['success' => false, 'message' => "Kecamatan '{$rowData['kecamatan']}' tidak ditemukan dalam kabupaten '{$rowData['kabupaten']}'"];
        }
        
        $desaId = getDesaIdByName($db, $kecamatanId, trim($rowData['desa']));
        if (!$desaId) {
            return ['success' => false, 'message' => "Desa '{$rowData['desa']}' tidak ditemukan dalam kecamatan '{$rowData['kecamatan']}'"];
        }
        
        $kthId = null;
        if (!empty(trim($rowData['kth']))) {
            $kthId = getKTHIdByName($db, $desaId, trim($rowData['kth']));
            if (!$kthId) {
                return ['success' => false, 'message' => "KTH '{$rowData['kth']}' tidak ditemukan dalam desa '{$rowData['desa']}'"];
            }
        }
        
        $penyuluhId = null;
        if (!empty(trim($rowData['penyuluh']))) {
            $penyuluhId = getPenyuluhIdByName($db, trim($rowData['penyuluh']));
            if (!$penyuluhId) {
                return ['success' => false, 'message' => "Penyuluh '{$rowData['penyuluh']}' tidak ditemukan dalam database"];
            }
        }
        
        $komoditasId = getKomoditasIdByName($db, trim($rowData['komoditas']));
        if (!$komoditasId) {
            return ['success' => false, 'message' => "Komoditas '{$rowData['komoditas']}' tidak ditemukan dalam database"];
        }
        
        // Validate komoditas kategori
        $komoditasKategori = getKomoditasKategori($db, $komoditasId);
        if ($komoditasKategori !== $kategori) {
            return ['success' => false, 'message' => "Kategori komoditas '{$rowData['komoditas']}' tidak sesuai (harus {$komoditasKategori}, bukan {$kategori})"];
        }
        
        $satuanId = getSatuanIdByName($db, trim($rowData['satuan']));
        if (!$satuanId) {
            return ['success' => false, 'message' => "Satuan '{$rowData['satuan']}' tidak ditemukan dalam database"];
        }
        
        // Check for duplicates
        if (isDuplicateProduksi($db, $periode, $kabupatenId, $kecamatanId, $desaId, $kthId, $komoditasId)) {
            return ['success' => false, 'message' => 'Data duplikat ditemukan'];
        }
        
        // Insert data
        $query = "INSERT INTO produksi (periode, kabupaten_id, kecamatan_id, desa_id, kth_id, 
                                      penyuluh_id, komoditas_id, qty, satuan_id, sumber, tanggal_input, created_by) 
                  VALUES (:periode, :kabupaten_id, :kecamatan_id, :desa_id, :kth_id, 
                          :penyuluh_id, :komoditas_id, :qty, :satuan_id, :sumber, :tanggal_input, :created_by)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':periode', $periode);
        $stmt->bindParam(':kabupaten_id', $kabupatenId);
        $stmt->bindParam(':kecamatan_id', $kecamatanId);
        $stmt->bindParam(':desa_id', $desaId);
        $stmt->bindParam(':kth_id', $kthId);
        $stmt->bindParam(':penyuluh_id', $penyuluhId);
        $stmt->bindParam(':komoditas_id', $komoditasId);
        $stmt->bindParam(':qty', $qty);
        $stmt->bindParam(':satuan_id', $satuanId);
        $sumber = 'import';
        $stmt->bindParam(':sumber', $sumber);
        $tanggalInput = date('Y-m-d');
        $stmt->bindParam(':tanggal_input', $tanggalInput);
        $createdBy = $_SESSION['user_id'] ?? 1;
        $stmt->bindParam(':created_by', $createdBy);
        
        $stmt->execute();
        
        return ['success' => true, 'message' => 'Data berhasil diimport'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Helper functions for getting IDs
function getKabupatenIdByName($db, $nama) {
    $query = "SELECT id FROM kabupaten WHERE nama = :nama";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nama', $nama);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : null;
}

function getKecamatanIdByName($db, $kabupatenId, $nama) {
    $query = "SELECT id FROM kecamatan WHERE kabupaten_id = :kabupaten_id AND nama = :nama";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':kabupaten_id', $kabupatenId);
    $stmt->bindParam(':nama', $nama);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : null;
}

function getDesaIdByName($db, $kecamatanId, $nama) {
    $query = "SELECT id FROM desa WHERE kecamatan_id = :kecamatan_id AND nama = :nama";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':kecamatan_id', $kecamatanId);
    $stmt->bindParam(':nama', $nama);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : null;
}

function getKTHIdByName($db, $desaId, $nama) {
    $query = "SELECT id FROM kth WHERE desa_id = :desa_id AND nama = :nama";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':desa_id', $desaId);
    $stmt->bindParam(':nama', $nama);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : null;
}

function getPenyuluhIdByName($db, $nama) {
    $query = "SELECT id FROM penyuluh WHERE nama = :nama";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nama', $nama);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : null;
}

function getKomoditasIdByName($db, $nama) {
    $query = "SELECT id FROM komoditas WHERE nama = :nama AND aktif = 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nama', $nama);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : null;
}

function getKomoditasKategori($db, $komoditasId) {
    $query = "SELECT kategori FROM komoditas WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $komoditasId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['kategori'] : null;
}

function getSatuanIdByName($db, $nama) {
    $query = "SELECT id FROM satuan WHERE nama = :nama";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nama', $nama);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : null;
}

function isDuplicateProduksi($db, $periode, $kabupatenId, $kecamatanId, $desaId, $kthId, $komoditasId) {
    $query = "SELECT COUNT(*) FROM produksi 
              WHERE periode = :periode 
              AND kabupaten_id = :kabupaten_id 
              AND kecamatan_id = :kecamatan_id 
              AND desa_id = :desa_id 
              AND COALESCE(kth_id, 0) = COALESCE(:kth_id, 0)
              AND komoditas_id = :komoditas_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':periode', $periode);
    $stmt->bindParam(':kabupaten_id', $kabupatenId);
    $stmt->bindParam(':kecamatan_id', $kecamatanId);
    $stmt->bindParam(':desa_id', $desaId);
    $stmt->bindParam(':kth_id', $kthId);
    $stmt->bindParam(':komoditas_id', $komoditasId);
    $stmt->execute();
    
    return $stmt->fetchColumn() > 0;
}

include 'includes/header.php';
?>

<!-- Page Content -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="text-lg font-medium text-gray-900">Import Data Produksi</h3>
        <p class="text-sm text-gray-600">Import data produksi dari file Excel</p>
    </div>
    <div class="flex space-x-3">
        <a href="produksi.php" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Produksi
        </a>
        <a href="download_template.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Download Template
        </a>
    </div>
</div>

<!-- Alert Messages -->
<?php if (isset($success)): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <div class="flex">
            <svg class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <?php echo $success; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <div class="flex">
            <svg class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <?php echo $error; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Info Card -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <div class="flex items-start">
        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h4 class="text-sm font-medium text-blue-800 mb-1">Panduan Import Data Excel</h4>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• <strong>Format File:</strong> Excel (.xls, .xlsx) atau CSV</li>
                <li>• <strong>Template:</strong> Download template untuk format yang benar</li>
                <li>• <strong>Validasi:</strong> Sistem akan memvalidasi data secara otomatis</li>
                <li>• <strong>Sumber Data:</strong> Otomatis terisi "Import Data"</li>
                <li>• <strong>Tanggal Input:</strong> Otomatis terisi tanggal import</li>
            </ul>
        </div>
    </div>
</div>

<!-- Import Form -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
        </svg>
        Upload File Excel
    </h3>
    
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel <span class="text-red-500">*</span></label>
            <input type="file" name="excel_file" accept=".xls,.xlsx,.csv" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
            <p class="text-sm text-gray-500 mt-1">Format yang didukung: .xls, .xlsx, .csv</p>
        </div>
        
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
            </svg>
            Import Data
        </button>
    </form>
</div>

<!-- Format Template -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Format Template Excel
    </h3>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kolom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contoh</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wajib</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">periode</td>
                    <td class="px-6 py-4 text-sm text-gray-900">Tanggal produksi</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2024-01-15</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="text-red-500">Ya</span></td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">kabupaten</td>
                    <td class="px-6 py-4 text-sm text-gray-900">Nama kabupaten</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Kabupaten A</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="text-red-500">Ya</span></td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">kecamatan</td>
                    <td class="px-6 py-4 text-sm text-gray-900">Nama kecamatan</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Kecamatan B</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="text-red-500">Ya</span></td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">desa</td>
                    <td class="px-6 py-4 text-sm text-gray-900">Nama desa</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Desa C</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="text-red-500">Ya</span></td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">kth</td>
                    <td class="px-6 py-4 text-sm text-gray-900">Nama KTH (opsional)</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">KTH Maju</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Tidak</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">penyuluh</td>
                    <td class="px-6 py-4 text-sm text-gray-900">Nama penyuluh (opsional)</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Budi Santoso</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Tidak</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">komoditas</td>
                    <td class="px-6 py-4 text-sm text-gray-900">Nama komoditas</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Bambu</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="text-red-500">Ya</span></td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">kategori</td>
                    <td class="px-6 py-4 text-sm text-gray-900">Kategori komoditas</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">HHBK</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="text-red-500">Ya</span></td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">qty</td>
                    <td class="px-6 py-4 text-sm text-gray-900">Jumlah produksi</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">100.500</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="text-red-500">Ya</span></td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">satuan</td>
                    <td class="px-6 py-4 text-sm text-gray-900">Satuan ukuran</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Kilogram</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="text-red-500">Ya</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Important Notes -->
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
    <h4 class="text-sm font-medium text-yellow-900 mb-2 flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        Penting!
    </h4>
    <ul class="text-sm text-yellow-700 space-y-1">
        <li>• Field <strong>sumber</strong> akan otomatis terisi "import" untuk data dari Excel</li>
        <li>• Field <strong>tanggal_input</strong> akan otomatis terisi tanggal import</li>
        <li>• Data duplikat (periode, wilayah, komoditas yang sama) tidak akan diimport</li>
        <li>• <strong>Validasi Data Master:</strong> Semua nama harus sesuai dengan data master:</li>
        <li class="ml-4">- Kabupaten, Kecamatan, Desa harus sesuai hierarki wilayah</li>
        <li class="ml-4">- KTH harus ada di desa yang dipilih</li>
        <li class="ml-4">- Penyuluh harus terdaftar dalam database</li>
        <li class="ml-4">- Komoditas dan kategori harus sesuai</li>
        <li class="ml-4">- Satuan harus terdaftar dalam database</li>
        <li>• Format tanggal harus YYYY-MM-DD (contoh: 2024-01-15)</li>
        <li>• Quantity harus berupa angka positif</li>
        <li>• Kategori harus HHK atau HHBK</li>
        <li>• <strong>Error Handling:</strong> Jika ada data tidak valid, baris tersebut akan dilewati dan ditampilkan pesan error spesifik</li>
    </ul>
</div>

<?php include 'includes/footer.php'; ?>
