<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if (isset($_GET['kecamatan_id'])) {
    $kecamatan_id = $_GET['kecamatan_id'];
    
    // Database connection handled by procedural functions
    
    try {
        $result = getMultipleRows("SELECT id, nama FROM desa WHERE kecamatan_id = ? ORDER BY nama", [$kecamatan_id]);
        echo json_encode($result ?: []);
    } catch(Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Kecamatan ID tidak ditemukan']);
}
?> 