<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$term = isset($_GET['q']) ? trim($_GET['q']) : '';
$kecamatanId = isset($_GET['kecamatan_id']) ? trim($_GET['kecamatan_id']) : '';

if ($kecamatanId === '') {
    echo json_encode([]);
    exit;
}

try {
    if ($term === '') {
        $result = getMultipleRows("SELECT id, nama AS text FROM desa WHERE kecamatan_id = ? ORDER BY nama LIMIT 50", [$kecamatanId]);
    } else {
        $like = "%" . $term . "%";
        $result = getMultipleRows("SELECT id, nama AS text FROM desa WHERE kecamatan_id = ? AND nama LIKE ? ORDER BY nama LIMIT 50", [$kecamatanId, $like]);
    }
    echo json_encode($result ?: []);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>


