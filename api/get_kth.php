<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if (isset($_GET['desa_id'])) {
    $desa_id = $_GET['desa_id'];
    
    // Database connection handled by procedural functions
    
    try {
        $result = getMultipleRows("SELECT id, nama FROM kth WHERE desa_id = ? ORDER BY nama", [$desa_id]);
        echo json_encode($result ?: []);
    } catch(Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Desa ID tidak ditemukan']);
}
?> 