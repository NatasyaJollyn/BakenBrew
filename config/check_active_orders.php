<?php
require_once 'koneksi.php';
header('Content-Type: application/json');

$ids_str = isset($_GET['ids']) ? $_GET['ids'] : '';
if (empty($ids_str)) {
    echo json_encode(['active_ids' => []]);
    exit;
}

$ids = array_map('intval', explode(',', $ids_str));
if (count($ids) === 0) {
    echo json_encode(['active_ids' => []]);
    exit;
}

$active_ids = [];
if ($is_db_online && $pdo) {
    try {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT `id` FROM `orders` WHERE `id` IN ($placeholders) AND `status` = 'pending'");
        $stmt->execute($ids);
        $active_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        $active_ids = [];
    }
} else {
    if ($mock_data && isset($mock_data['orders'])) {
        foreach ($mock_data['orders'] as $mo) {
            if (in_array((int)$mo['id'], $ids) && $mo['status'] === 'pending') {
                $active_ids[] = (int)$mo['id'];
            }
        }
    }
}

echo json_encode(['active_ids' => $active_ids]);
exit;
?>