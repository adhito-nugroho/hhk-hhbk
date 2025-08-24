<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
require_once 'components/DataTable.php';

$page_title = 'Manajemen KTH';
$page_description = 'Kelola data Kelompok Tani Hutan (KTH)';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                addKTH($_POST);
                break;
            case 'edit':
                editKTH($_POST);
                break;
            case 'delete':
                deleteKTH($_POST['id']);
                break;
        }
    }
}

function addKTH($data) {
    global $success, $error;
    try {
        $result = insertData("INSERT INTO kth (nama, kode, desa_id) VALUES (?, ?, ?)", [
            $data['nama'], 
            $data['kode'], 
            $data['desa_id']
        ]);
        if ($result) {
            $success = "KTH berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan KTH!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

function editKTH($data) {
    global $success, $error;
    try {
        $result = updateData("UPDATE kth SET nama = ?, kode = ?, desa_id = ? WHERE id = ?", [
            $data['nama'], 
            $data['kode'], 
            $data['desa_id'], 
            $data['id']
        ]);
        if ($result) {
            $success = "KTH berhasil diupdate!";
        } else {
            $error = "Gagal mengupdate KTH!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

function deleteKTH($id) {
    global $success, $error;
    try {
        $result = deleteData("DELETE FROM kth WHERE id = ?", [$id]);
        if ($result) {
            $success = "KTH berhasil dihapus!";
        } else {
            $error = "Gagal menghapus KTH!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Initialize DataTable
$db = getDatabaseConnection();
initDataTable(
    $db, 
    'kth', 
    ['id', 'nama', 'kode', 'desa_id', 'created_at'],
    ['nama', 'kode'], // searchable columns
    ['nama', 'kode', 'created_at'] // orderable columns
);

include 'includes/header.php';
?>

<!-- Page Content -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="text-lg font-medium text-gray-900">Data KTH</h3>
        <p class="text-sm text-gray-600">Kelola data Kelompok Tani Hutan</p>
    </div>
    <button onclick="openModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Tambah KTH
    </button>
</div>

<!-- Search and Filter -->
<form method="GET" class="mb-4">
    <?php echo renderDataTableSearchBox(); ?>
</form>

<!-- Data Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <?php echo renderDataTableSortableHeader('nama', 'Nama KTH', 'kth.php'); ?>
                    <?php echo renderDataTableSortableHeader('kode', 'Kode', 'kth.php'); ?>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php echo getKTHData(); ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php echo renderDataTablePagination('kth.php'); ?>
</div>

<!-- Modal Form -->
<div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Tambah KTH</h3>
            <form id="kthForm" method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="formId">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama KTH</label>
                    <input type="text" name="nama" id="nama" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode KTH</label>
                    <input type="text" name="kode" id="kode" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="Opsional">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Desa</label>
                    <select name="desa_id" id="desa_id" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Pilih Desa</option>
                        <?php echo getDesaOptions(); ?>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('modal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Tambah KTH';
        document.getElementById('formAction').value = 'add';
        document.getElementById('kthForm').reset();
    }
    
    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }
    
    function editKTH(id, nama, kode, desa_id) {
        document.getElementById('modalTitle').textContent = 'Edit KTH';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('formId').value = id;
        document.getElementById('nama').value = nama;
        document.getElementById('kode').value = kode;
        document.getElementById('desa_id').value = desa_id;
        document.getElementById('modal').classList.remove('hidden');
    }
    
    function deleteKTH(id) {
        if (confirm('Apakah Anda yakin ingin menghapus KTH ini?')) {
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
</script>

<?php
function getKTHData() {
    try {
        $result = getDataTableData();
        $data = $result['data'];
        
        $output = '';
        if (empty($data)) {
            $output .= '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data yang ditemukan</td></tr>';
        } else {
            foreach ($data as $row) {
                // Get wilayah info
                $wilayahInfo = getWilayahInfo($row['desa_id']);
                
                $output .= '<tr>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . $row['id'] . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['nama']) . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ($row['kode'] ? htmlspecialchars($row['kode']) : '-') . '</td>';
                $output .= '<td class="px-6 py-4 text-sm text-gray-900">' . $wilayahInfo . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">';
                $output .= '<button onclick="editKTH(' . $row['id'] . ', \'' . addslashes($row['nama']) . '\', \'' . addslashes($row['kode'] ?: '') . '\', ' . $row['desa_id'] . ')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>';
                $output .= '<button onclick="deleteKTH(' . $row['id'] . ')" class="text-red-600 hover:text-red-900">Hapus</button>';
                $output .= '</td>';
                $output .= '</tr>';
            }
        }
        
        return $output;
    } catch(Exception $e) {
        return '<tr><td colspan="5" class="px-6 py-4 text-center text-red-600">Error: ' . $e->getMessage() . '</td></tr>';
    }
}

function getWilayahInfo($desa_id) {
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

function getDesaOptions() {
    try {
        $rows = getMultipleRows("
            SELECT d.id, d.nama, k.nama as kecamatan, kab.nama as kabupaten 
            FROM desa d 
            JOIN kecamatan k ON d.kecamatan_id = k.id 
            JOIN kabupaten kab ON k.kabupaten_id = kab.id 
            ORDER BY kab.nama, k.nama, d.nama
        ");
        
        $output = '';
        foreach ($rows as $row) {
            $output .= '<option value="' . $row['id'] . '">' . 
                      htmlspecialchars($row['kabupaten']) . ' - ' . 
                      htmlspecialchars($row['kecamatan']) . ' - ' . 
                      htmlspecialchars($row['nama']) . '</option>';
        }
        return $output;
    } catch(Exception $e) {
        return '';
    }
}

include 'includes/footer.php';
?>
