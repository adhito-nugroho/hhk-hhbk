<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$term = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    if ($term === '') {
        $result = getMultipleRows("SELECT id, nama AS text FROM kabupaten ORDER BY nama LIMIT 20");
    } else {
        $like = "%" . $term . "%";
        $result = getMultipleRows("SELECT id, nama AS text FROM kabupaten WHERE nama LIKE ? ORDER BY nama LIMIT 20", [$like]);
    }
    echo json_encode($result ?: []);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>


