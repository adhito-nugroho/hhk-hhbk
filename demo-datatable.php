<?php
session_start();
require_once 'config/database.php';
require_once 'components/DataTable.php';

// Initialize DataTable with demo data
$database = new Database();
$db = $database->getConnection();

// Create demo data table if it doesn't exist
try {
    $db->exec("CREATE TABLE IF NOT EXISTS demo_data (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        telepon VARCHAR(20),
        alamat TEXT,
        kota VARCHAR(50),
        status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Insert demo data if table is empty
    $stmt = $db->query("SELECT COUNT(*) FROM demo_data");
    if ($stmt->fetchColumn() == 0) {
        $demoData = [
            ['Ahmad Rizki', 'ahmad@example.com', '08123456789', 'Jl. Merdeka No. 123', 'Jakarta', 'aktif'],
            ['Siti Nurhaliza', 'siti@example.com', '08123456790', 'Jl. Sudirman No. 45', 'Bandung', 'aktif'],
            ['Budi Santoso', 'budi@example.com', '08123456791', 'Jl. Thamrin No. 67', 'Surabaya', 'nonaktif'],
            ['Dewi Sartika', 'dewi@example.com', '08123456792', 'Jl. Gatot Subroto No. 89', 'Medan', 'aktif'],
            ['Joko Widodo', 'joko@example.com', '08123456793', 'Jl. Asia Afrika No. 12', 'Semarang', 'aktif'],
            ['Sri Mulyani', 'sri@example.com', '08123456794', 'Jl. Diponegoro No. 34', 'Yogyakarta', 'aktif'],
            ['Prabowo Subianto', 'prabowo@example.com', '08123456795', 'Jl. Ahmad Yani No. 56', 'Malang', 'nonaktif'],
            ['Megawati Soekarnoputri', 'megawati@example.com', '08123456796', 'Jl. Kartini No. 78', 'Palembang', 'aktif'],
            ['Susilo Bambang Yudhoyono', 'sby@example.com', '08123456797', 'Jl. Pahlawan No. 90', 'Denpasar', 'aktif'],
            ['Gus Dur', 'gusdur@example.com', '08123456798', 'Jl. Veteran No. 23', 'Makassar', 'nonaktif'],
            ['Bacharuddin Jusuf Habibie', 'habibie@example.com', '08123456799', 'Jl. Nasional No. 45', 'Manado', 'aktif'],
            ['Soeharto', 'soeharto@example.com', '08123456800', 'Jl. Kemerdekaan No. 67', 'Balikpapan', 'nonaktif'],
            ['Soekarno', 'soekarno@example.com', '08123456801', 'Jl. Proklamasi No. 89', 'Pontianak', 'aktif'],
            ['Kartini', 'kartini@example.com', '08123456802', 'Jl. Pendidikan No. 12', 'Banjarmasin', 'aktif'],
            ['Cut Nyak Dien', 'cutnyak@example.com', '08123456803', 'Jl. Perjuangan No. 34', 'Padang', 'aktif']
        ];
        
        $stmt = $db->prepare("INSERT INTO demo_data (nama, email, telepon, alamat, kota, status) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($demoData as $data) {
            $stmt->execute($data);
        }
    }
} catch(PDOException $e) {
    // Table creation failed, continue without demo data
}

// Initialize DataTable
$dataTable = new DataTable(
    $db, 
    'demo_data', 
    ['id', 'nama', 'email', 'telepon', 'alamat', 'kota', 'status', 'created_at'],
    ['nama', 'email', 'telepon', 'alamat', 'kota'], // searchable columns
    ['nama', 'email', 'kota', 'status', 'created_at'] // orderable columns
);

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_delete') {
    if (isset($_POST['ids']) && !empty($_POST['ids'])) {
        $ids = explode(',', $_POST['ids']);
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $query = "DELETE FROM demo_data WHERE id IN ($placeholders)";
        $stmt = $db->prepare($query);
        $stmt->execute($ids);
        $success = count($ids) . " data berhasil dihapus!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo DataTable - Sistem Informasi Pengelolaan HHK dan HHBK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-green-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold">Sistem Informasi Pengelolaan HHK dan HHBK</h1>
                    <span class="text-green-200">Demo Fitur DataTable</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="hover:text-green-200">Dashboard</a>
                    <a href="produksi.php" class="hover:text-green-200">Produksi</a>
                    <a href="komoditas.php" class="hover:text-green-200">Komoditas</a>
                    <a href="wilayah.php" class="hover:text-green-200">Wilayah</a>
                    <a href="laporan.php" class="hover:text-green-200">Laporan</a>
                    <a href="demo-datatable.php" class="hover:text-green-200 font-semibold">Demo DataTable</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Demo DataTable Features</h2>
            <p class="text-gray-600">Halaman ini menampilkan semua fitur DataTable yang tersedia dalam sistem Sistem Informasi Pengelolaan HHK dan HHBK</p>
        </div>

        <!-- Feature Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-md">
                <div class="flex items-center">
                    <i class="fas fa-search text-green-600 text-2xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-gray-900">Searching</h3>
                        <p class="text-sm text-gray-600">Pencarian real-time</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-md">
                <div class="flex items-center">
                    <i class="fas fa-sort text-blue-600 text-2xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-gray-900">Sorting</h3>
                        <p class="text-sm text-gray-600">Pengurutan kolom</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-md">
                <div class="flex items-center">
                    <i class="fas fa-list-ol text-purple-600 text-2xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-gray-900">Pagination</h3>
                        <p class="text-sm text-gray-600">Navigasi halaman</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-md">
                <div class="flex items-center">
                    <i class="fas fa-download text-orange-600 text-2xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-gray-900">Export</h3>
                        <p class="text-sm text-gray-600">Ekspor data</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <!-- Search and Filter -->
        <form method="GET" class="mb-4">
            <?php echo $dataTable->renderSearchBox(); ?>
        </form>

        <!-- Bulk Actions -->
        <div id="bulk-actions" class="hidden mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-blue-700">
                        <span id="selected-count">0</span> data dipilih
                    </span>
                    <button type="button" onclick="bulkDelete()" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                        <i class="fas fa-trash mr-2"></i>Hapus Terpilih
                    </button>
                </div>
                <button type="button" onclick="clearSelection()" 
                        class="text-blue-600 hover:text-blue-800 text-sm">
                    Batal Pilihan
                </button>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all" onchange="selectAllRows()" 
                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <?php echo $dataTable->renderSortableHeader('nama', 'Nama', 'demo-datatable.php'); ?>
                            <?php echo $dataTable->renderSortableHeader('email', 'Email', 'demo-datatable.php'); ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                            <?php echo $dataTable->renderSortableHeader('kota', 'Kota', 'demo-datatable.php'); ?>
                            <?php echo $dataTable->renderSortableHeader('status', 'Status', 'demo-datatable.php'); ?>
                            <?php echo $dataTable->renderSortableHeader('created_at', 'Tanggal Dibuat', 'demo-datatable.php'); ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php echo getDemoData(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php echo $dataTable->renderPagination('demo-datatable.php'); ?>
        </div>

        <!-- Export Buttons -->
        <div class="mt-6 flex space-x-4">
            <button onclick="exportTableData('csv')" 
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center">
                <i class="fas fa-file-csv mr-2"></i>Export CSV
            </button>
            <button onclick="exportTableData('excel')" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </button>
        </div>

        <!-- Keyboard Shortcuts Info -->
        <div class="mt-6 bg-white p-4 rounded-lg shadow-md">
            <h3 class="font-semibold text-gray-900 mb-3">Keyboard Shortcuts:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <div>
                    <p><kbd class="bg-gray-100 px-2 py-1 rounded">Ctrl + F</kbd> - Focus search box</p>
                    <p><kbd class="bg-gray-100 px-2 py-1 rounded">Ctrl + A</kbd> - Select all rows</p>
                </div>
                <div>
                    <p><kbd class="bg-gray-100 px-2 py-1 rounded">Enter</kbd> - Submit search</p>
                    <p><kbd class="bg-gray-100 px-2 py-1 rounded">Esc</kbd> - Clear search</p>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/datatable.js"></script>
    <script>
        function clearSelection() {
            document.getElementById('select-all').checked = false;
            const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateBulkActions();
        }
        
        // Add row highlighting on hover
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f9fafb';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
                
                row.addEventListener('click', function() {
                    highlightRow(this);
                });
            });
        });
        
        function editRow(id) {
            // Placeholder for edit functionality
            alert('Edit row ' + id);
        }
        
        function deleteRow(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = '<input type="hidden" name="action" value="delete">' +
                                '<input type="hidden" name="id" value="' + id + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

<?php
function getDemoData() {
    global $dataTable;
    
    try {
        // Get data with pagination and search
        $result = $dataTable->getData();
        $data = $result['data'];
        
        $output = '';
        if (empty($data)) {
            $output .= '<tr><td colspan="10" class="px-6 py-4 text-center text-gray-500">Tidak ada data yang ditemukan</td></tr>';
        } else {
            foreach ($data as $row) {
                $statusClass = $row['status'] === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                $statusText = ucfirst($row['status']);
                
                $output .= '<tr class="hover:bg-gray-50 cursor-pointer">';
                $output .= '<td class="px-6 py-4 whitespace-nowrap">';
                $output .= '<input type="checkbox" value="' . $row['id'] . '" onchange="updateBulkActions()" ';
                $output .= 'class="rounded border-gray-300 text-green-600 focus:ring-green-500">';
                $output .= '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . $row['id'] . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['nama']) . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['email']) . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['telepon']) . '</td>';
                $output .= '<td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate" title="' . htmlspecialchars($row['alamat']) . '">' . htmlspecialchars($row['alamat']) . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['kota']) . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap">';
                $output .= '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $statusClass . '">' . $statusText . '</span>';
                $output .= '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . date('d/m/Y H:i', strtotime($row['created_at'])) . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">';
                $output .= '<button onclick="editRow(' . $row['id'] . ')" class="text-indigo-600 hover:text-indigo-900 mr-3">';
                $output .= '<i class="fas fa-edit"></i>';
                $output .= '</button>';
                $output .= '<button onclick="deleteRow(' . $row['id'] . ')" class="text-red-600 hover:text-red-900">';
                $output .= '<i class="fas fa-trash"></i>';
                $output .= '</button>';
                $output .= '</td>';
                $output .= '</tr>';
            }
        }
        
        return $output;
    } catch(PDOException $e) {
        return '<tr><td colspan="10" class="px-6 py-4 text-center text-red-600">Error: ' . $e->getMessage() . '</td></tr>';
    }
}
?> 