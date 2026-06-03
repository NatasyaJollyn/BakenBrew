<?php
require_once 'koneksi.php';
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT `setting_value` FROM `settings` WHERE `setting_key` = 'store_status'");
$status = $stmt->fetchColumn() ?: 'open';

echo json_encode(['status' => $status]);
exit;
?>