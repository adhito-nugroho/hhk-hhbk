<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if (isset($_GET['kabupaten_id'])) {
    $kabupaten_id = $_GET['kabupaten_id'];
    
    // Database connection handled by procedural functions
    
    try {
        $result = getMultipleRows("SELECT id, nama FROM kecamatan WHERE kabupaten_id = ? ORDER BY nama", [$kabupaten_id]);
        echo json_encode($result ?: []);
    } catch(Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Kabupaten ID tidak ditemukan']);
}
?> 