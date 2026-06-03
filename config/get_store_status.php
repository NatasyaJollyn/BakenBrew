<?php
require_once 'koneksi.php';
header('Content-Type: application/json');

$status = 'open';
if ($is_db_online && $pdo) {
    try {
        $stmt = $pdo->query("SELECT `setting_value` FROM `settings` WHERE `setting_key` = 'store_status'");
        $status = $stmt->fetchColumn() ?: 'open';
    } catch (PDOException $e) {
        $status = 'open';
    }
} else {
    $status = $mock_data['settings']['store_status'] ?? 'open';
}

echo json_encode(['status' => $status]);
exit;
?>