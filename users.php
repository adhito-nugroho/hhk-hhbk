<?php
session_start();
require_once 'config/database.php';
require_once 'components/DataTable.php';

// Check if user is admin
if (!in_array($_SESSION['role'], ['admin-prov', 'admin-kab'])) {
    header('Location: index.php');
    exit();
}

$page_title = 'Manajemen Pengguna';
$page_description = 'Kelola data pengguna sistem';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                addUser($_POST);
                break;
            case 'edit':
                editUser($_POST);
                break;
            case 'delete':
                deleteUser($_POST['id']);
                break;
        }
    }
}

function addUser($data) {
    global $success, $error;
    try {
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $result = insertData("INSERT INTO pengguna (username, password_hash, nama, role) VALUES (?, ?, ?, ?)", [
            $data['username'],
            $password_hash,
            $data['nama'],
            $data['role']
        ]);
        if ($result) {
            $success = "Pengguna berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan pengguna!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

function editUser($data) {
    global $success, $error;
    try {
        if (!empty($data['password'])) {
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $result = updateData("UPDATE pengguna SET username = ?, password_hash = ?, nama = ?, role = ? WHERE id = ?", [
                $data['username'],
                $password_hash,
                $data['nama'],
                $data['role'],
                $data['id']
            ]);
        } else {
            $result = updateData("UPDATE pengguna SET username = ?, nama = ?, role = ? WHERE id = ?", [
                $data['username'],
                $data['nama'],
                $data['role'],
                $data['id']
            ]);
        }
        
        if ($result) {
            $success = "Pengguna berhasil diupdate!";
        } else {
            $error = "Gagal mengupdate pengguna!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

function deleteUser($id) {
    global $success, $error;
    try {
        // Prevent deleting own account
        if ($id == $_SESSION['user_id']) {
            $error = "Tidak dapat menghapus akun sendiri!";
            return;
        }
        
        $result = deleteData("DELETE FROM pengguna WHERE id = ?", [$id]);
        if ($result) {
            $success = "Pengguna berhasil dihapus!";
        } else {
            $error = "Gagal menghapus pengguna!";
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<!-- Page Content -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="text-lg font-medium text-gray-900">Data Pengguna</h3>
        <p class="text-sm text-gray-600">Kelola data pengguna sistem</p>
    </div>
    <button onclick="openModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Tambah Pengguna
    </button>
</div>

<!-- Data Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php echo getUsersData(); ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Tambah Pengguna</h3>
            <form id="userForm" method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="formId">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" name="username" id="username" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" id="password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    <p class="text-sm text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" id="role" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Pilih Role</option>
                        <option value="admin-prov">Admin Provinsi</option>
                        <option value="admin-kab">Admin Kabupaten</option>
                        <option value="penyuluh">Penyuluh</option>
                        <option value="viewer">Viewer</option>
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
        document.getElementById('modalTitle').textContent = 'Tambah Pengguna';
        document.getElementById('formAction').value = 'add';
        document.getElementById('userForm').reset();
        document.getElementById('password').required = true;
    }
    
    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }
    
    function editUser(id, username, nama, role) {
        document.getElementById('modalTitle').textContent = 'Edit Pengguna';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('formId').value = id;
        document.getElementById('username').value = username;
        document.getElementById('nama').value = nama;
        document.getElementById('role').value = role;
        document.getElementById('password').required = false;
        document.getElementById('modal').classList.remove('hidden');
    }
    
    function deleteUser(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
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
function getUsersData() {
    try {
        $rows = getMultipleRows("SELECT id, username, nama, role FROM pengguna ORDER BY id");
        
        $output = '';
        if (empty($rows)) {
            $output .= '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data yang ditemukan</td></tr>';
        } else {
            foreach ($rows as $row) {
                $roleLabels = [
                    'admin-prov' => 'Admin Provinsi',
                    'admin-kab' => 'Admin Kabupaten',
                    'penyuluh' => 'Penyuluh',
                    'viewer' => 'Viewer'
                ];
                
                $roleClass = [
                    'admin-prov' => 'bg-red-100 text-red-800',
                    'admin-kab' => 'bg-blue-100 text-blue-800',
                    'penyuluh' => 'bg-green-100 text-green-800',
                    'viewer' => 'bg-gray-100 text-gray-800'
                ];
                
                $output .= '<tr>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . $row['id'] . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['username']) . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['nama']) . '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap">';
                $output .= '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $roleClass[$row['role']] . '">' . $roleLabels[$row['role']] . '</span>';
                $output .= '</td>';
                $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">';
                $output .= '<button onclick="editUser(' . $row['id'] . ', \'' . addslashes($row['username']) . '\', \'' . addslashes($row['nama']) . '\', \'' . $row['role'] . '\')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>';
                if ($row['id'] != $_SESSION['user_id']) {
                    $output .= '<button onclick="deleteUser(' . $row['id'] . ')" class="text-red-600 hover:text-red-900">Hapus</button>';
                } else {
                    $output .= '<span class="text-gray-400">Akun Aktif</span>';
                }
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
