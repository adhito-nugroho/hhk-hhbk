<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
require_once 'components/DataTable.php';

$page_title = 'Manajemen Penyuluh';
$page_description = 'Kelola data Penyuluh Kehutanan';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                addPenyuluh($_POST);
                break;
            case 'edit':
                editPenyuluh($_POST);
                break;
            case 'delete':
                deletePenyuluh($_POST['id']);
                break;
        }
    }
}

function addPenyuluh($data) {
    global $success, $error;
    try {
        $result = insertData("INSERT INTO penyuluh (nama, nip, jabatan, wilayah_kerja) VALUES (?, ?, ?, ?)", [
            $data['nama'], 
            $data['nip'], 
            $data['jabatan'], 
            $data['wilayah_kerja']
        ]);
        if ($result) {
            $success = "Penyuluh berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan penyuluh!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

function editPenyuluh($data) {
    global $success, $error;
    try {
        $result = updateData("UPDATE penyuluh SET nama = ?, nip = ?, jabatan = ?, wilayah_kerja = ? WHERE id = ?", [
            $data['nama'], 
            $data['nip'], 
            $data['jabatan'], 
            $data['wilayah_kerja'], 
            $data['id']
        ]);
        if ($result) {
            $success = "Penyuluh berhasil diupdate!";
        } else {
            $error = "Gagal mengupdate penyuluh!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

function deletePenyuluh($id) {
    global $success, $error;
    try {
        $result = deleteData("DELETE FROM penyuluh WHERE id = ?", [$id]);
        if ($result) {
            $success = "Penyuluh berhasil dihapus!";
        } else {
            $error = "Gagal menghapus penyuluh!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Initialize DataTable
$db = getDatabaseConnection();
initDataTable(
    $db, 
    'penyuluh', 
    ['id', 'nama', 'nip', 'jabatan', 'wilayah_kerja', 'created_at'],
    ['nama', 'nip', 'jabatan', 'wilayah_kerja'], // searchable columns
    ['nama', 'nip', 'jabatan', 'wilayah_kerja', 'created_at'] // orderable columns
);

include 'includes/header.php';
?>

<!-- Page Content -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="text-lg font-medium text-gray-900">Data Penyuluh</h3>
        <p class="text-sm text-gray-600">Kelola data Penyuluh Kehutanan</p>
    </div>
    <button onclick="openModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Tambah Penyuluh
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
                    <?php echo renderDataTableSortableHeader('nama', 'Nama Penyuluh', 'penyuluh.php'); ?>
                    <?php echo renderDataTableSortableHeader('nip', 'NIP', 'penyuluh.php'); ?>
                    <?php echo renderDataTableSortableHeader('jabatan', 'Jabatan', 'penyuluh.php'); ?>
                    <?php echo renderDataTableSortableHeader('wilayah_kerja', 'Wilayah Kerja', 'penyuluh.php'); ?>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php echo getPenyuluhData(); ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php echo renderDataTablePagination('penyuluh.php'); ?>
</div>

<!-- Modal Form -->
<div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Tambah Penyuluh</h3>
            <form id="penyuluhForm" method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="formId">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Penyuluh</label>
                    <input type="text" name="nama" id="nama" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                    <input type="text" name="nip" id="nip" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="Opsional">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                    <input type="text" name="jabatan" id="jabatan" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="Contoh: Penyuluh Kehutanan">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Wilayah Kerja</label>
                    <textarea name="wilayah_kerja" id="wilayah_kerja" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Deskripsi wilayah kerja penyuluh"></textarea>
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
        document.getElementById('modalTitle').textContent = 'Tambah Penyuluh';
        document.getElementById('formAction').value = 'add';
        document.getElementById('penyuluhForm').reset();
    }
    
    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }
    
    function editPenyuluh(id, nama, nip, jabatan, wilayah_kerja) {
        document.getElementById('modalTitle').textContent = 'Edit Penyuluh';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('formId').value = id;
        document.getElementById('nama').value = nama;
        document.getElementById('nip').value = nip;
        document.getElementById('jabatan').value = jabatan;
        document.getElementById('wilayah_kerja').value = wilayah_kerja;
        document.getElementById('modal').classList.remove('hidden');
    }
    
    function deletePenyuluh(id) {
        if (confirm('Apakah Anda yakin ingin menghapus penyuluh ini?')) {
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
function getPenyuluhData() {
    try {
        $result = getDataTableData();
        $data = $result['data'];
        
        $output = '';
        if (empty($data)) {
            $output .= '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data yang ditemukan</td></tr>';
        } else {
            foreach ($data as $row) {
                $output .= '<tr>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . $row['id'] . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['nama']) . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ($row['nip'] ? htmlspecialchars($row['nip']) : '-') . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ($row['jabatan'] ? htmlspecialchars($row['jabatan']) : '-') . '</td>';
                $output .= '<td class="px-6 py-4 text-sm text-gray-900">' . ($row['wilayah_kerja'] ? htmlspecialchars($row['wilayah_kerja']) : '-') . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">';
                $output .= '<button onclick="editPenyuluh(' . $row['id'] . ', \'' . addslashes($row['nama']) . '\', \'' . addslashes($row['nip'] ?: '') . '\', \'' . addslashes($row['jabatan'] ?: '') . '\', \'' . addslashes($row['wilayah_kerja'] ?: '') . '\')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>';
                $output .= '<button onclick="deletePenyuluh(' . $row['id'] . ')" class="text-red-600 hover:text-red-900">Hapus</button>';
                $output .= '</td>';
                $output .= '</tr>';
            }
        }
        
        return $output;
    } catch(Exception $e) {
        return '<tr><td colspan="6" class="px-6 py-4 text-center text-red-600">Error: ' . $e->getMessage() . '</td></tr>';
    }
}

include 'includes/footer.php';
?>
