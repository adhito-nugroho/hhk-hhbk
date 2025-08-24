<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
require_once 'components/DataTable.php';

$page_title = 'Manajemen Komoditas';
$page_description = 'Kelola data komoditas HHK dan HHBK';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                addKomoditas($_POST);
                break;
            case 'edit':
                editKomoditas($_POST);
                break;
            case 'delete':
                deleteKomoditas($_POST['id']);
                break;
            case 'toggle_status':
                toggleKomoditasStatus($_POST['id']);
                break;
        }
    }
}

function addKomoditas($data) {
    global $success, $error;
    try {
        $result = insertData("INSERT INTO komoditas (nama, kategori, aktif) VALUES (?, ?, 1)", [$data['nama'], $data['kategori']]);
        if ($result) {
            $success = "Komoditas berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan komoditas!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

function editKomoditas($data) {
    global $success, $error;
    try {
        $result = updateData("UPDATE komoditas SET nama = ?, kategori = ? WHERE id = ?", [$data['nama'], $data['kategori'], $data['id']]);
        if ($result) {
            $success = "Komoditas berhasil diupdate!";
        } else {
            $error = "Gagal mengupdate komoditas!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

function deleteKomoditas($id) {
    global $success, $error;
    try {
        $result = deleteData("DELETE FROM komoditas WHERE id = ?", [$id]);
        if ($result) {
            $success = "Komoditas berhasil dihapus!";
        } else {
            $error = "Gagal menghapus komoditas!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

function toggleKomoditasStatus($id) {
    global $success, $error;
    try {
        $result = updateData("UPDATE komoditas SET aktif = NOT aktif WHERE id = ?", [$id]);
        if ($result) {
            $success = "Status komoditas berhasil diubah!";
        } else {
            $error = "Gagal mengubah status komoditas!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Initialize DataTable
$db = getDatabaseConnection();
initDataTable(
    $db, 
    'komoditas', 
    ['id', 'nama', 'kategori', 'aktif', 'created_at'],
    ['nama', 'kategori'], // searchable columns
    ['nama', 'kategori', 'created_at'] // orderable columns
);

include 'includes/header.php';
?>

<!-- Page Content -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="text-lg font-medium text-gray-900">Data Komoditas</h3>
        <p class="text-sm text-gray-600">Kelola data komoditas HHK dan HHBK</p>
    </div>
    <button onclick="openModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Tambah Komoditas
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
                    <?php echo renderDataTableSortableHeader('nama', 'Nama Komoditas', 'komoditas.php'); ?>
                    <?php echo renderDataTableSortableHeader('kategori', 'Kategori', 'komoditas.php'); ?>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php echo getKomoditasData(); ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php echo renderDataTablePagination('komoditas.php'); ?>
</div>

<!-- Modal Form -->
<div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Tambah Komoditas</h3>
            <form id="komoditasForm" method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="formId">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Komoditas</label>
                    <input type="text" name="nama" id="nama" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="kategori" id="kategori" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Pilih Kategori</option>
                        <option value="HHK">HHK - Hasil Hutan Kayu</option>
                        <option value="HHBK">HHBK - Hasil Hutan Bukan Kayu</option>
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
        document.getElementById('modalTitle').textContent = 'Tambah Komoditas';
        document.getElementById('formAction').value = 'add';
        document.getElementById('komoditasForm').reset();
    }
    
    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }
    
    function editKomoditas(id, nama, kategori) {
        document.getElementById('modalTitle').textContent = 'Edit Komoditas';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('formId').value = id;
        document.getElementById('nama').value = nama;
        document.getElementById('kategori').value = kategori;
        document.getElementById('modal').classList.remove('hidden');
    }
    
    function deleteKomoditas(id) {
        if (confirm('Apakah Anda yakin ingin menghapus komoditas ini?')) {
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
    
    function toggleStatus(id) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
</script>

<?php
function getKomoditasData() {
    try {
        $result = getDataTableData();
        $data = $result['data'];
        
        $output = '';
        if (empty($data)) {
            $output .= '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data yang ditemukan</td></tr>';
        } else {
            foreach ($data as $row) {
                $statusClass = $row['aktif'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                $statusText = $row['aktif'] ? 'Aktif' : 'Nonaktif';
                
                $output .= '<tr>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . $row['id'] . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['nama']) . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . ($row['kategori'] ?: '-') . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap">';
                $output .= '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $statusClass . '">' . $statusText . '</span>';
                $output .= '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">';
                $output .= '<button onclick="editKomoditas(' . $row['id'] . ', \'' . addslashes($row['nama']) . '\', \'' . addslashes($row['kategori'] ?: '') . '\')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>';
                $output .= '<button onclick="toggleStatus(' . $row['id'] . ')" class="text-yellow-600 hover:text-yellow-900 mr-3">' . ($row['aktif'] ? 'Nonaktifkan' : 'Aktifkan') . '</button>';
                $output .= '<button onclick="deleteKomoditas(' . $row['id'] . ')" class="text-red-600 hover:text-red-900">Hapus</button>';
                $output .= '</td>';
                $output .= '</tr>';
            }
        }
        
        return $output;
    } catch(Exception $e) {
        return '<tr><td colspan="5" class="px-6 py-4 text-center text-red-600">Error: ' . $e->getMessage() . '</td></tr>';
    }
}

include 'includes/footer.php';
?> 