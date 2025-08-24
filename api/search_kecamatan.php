<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$term = isset($_GET['q']) ? trim($_GET['q']) : '';
$kabupatenId = isset($_GET['kabupaten_id']) ? trim($_GET['kabupaten_id']) : '';

if ($kabupatenId === '') {
    echo json_encode([]);
    exit;
}

try {
    if ($term === '') {
        $result = getMultipleRows("SELECT id, nama AS text FROM kecamatan WHERE kabupaten_id = ? ORDER BY nama LIMIT 50", [$kabupatenId]);
    } else {
        $like = "%" . $term . "%";
        $result = getMultipleRows("SELECT id, nama AS text FROM kecamatan WHERE kabupaten_id = ? AND nama LIKE ? ORDER BY nama LIMIT 50", [$kabupatenId, $like]);
    }
    echo json_encode($result ?: []);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>


