<?php
// ========================================================
// BAKE'N BREW - Notification API
// ========================================================

session_start();
require_once '../config/koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$admin_data = null;
if (isset($_SESSION['admin_username'])) {
    if ($is_db_online) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM `admin` WHERE `username` = ?");
            $stmt->execute([$_SESSION['admin_username']]);
            $admin_data = $stmt->fetch();
        } catch (PDOException $e) {}
    }
}
$lang_code = (isset($admin_data['lang']) && $admin_data['lang'] === 'id') ? 'id' : 'en';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Function to format relative timestamp
function getRelativeTime($timestamp) {
    global $lang_code;
    $time = strtotime($timestamp);
    $diff = time() - $time;
    if ($diff < 60) {
        return $lang_code === 'en' ? 'Just now' : 'Baru saja';
    } elseif ($diff < 3600) {
        $mins = round($diff / 60);
        return $lang_code === 'en' 
            ? $mins . ' ' . ($mins == 1 ? 'minute' : 'minutes') . ' ago' 
            : $mins . ' menit yang lalu';
    } elseif ($diff < 86400) {
        $hours = round($diff / 3600);
        return $lang_code === 'en' 
            ? $hours . ' ' . ($hours == 1 ? 'hour' : 'hours') . ' ago' 
            : $hours . ' jam yang lalu';
    } else {
        $days = round($diff / 86400);
        return $lang_code === 'en' 
            ? $days . ' ' . ($days == 1 ? 'day' : 'days') . ' ago' 
            : $days . ' hari yang lalu';
    }
}

// 1. Handle Mark as Read
if ($action === 'read' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($is_db_online) {
        try {
            $stmt = $pdo->prepare("UPDATE `notifications` SET `is_read` = 1 WHERE `id` = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        // Mock success for offline mode
        echo json_encode(['success' => true, 'offline' => true]);
    }
    exit;
}

// 2. Handle Mark All as Read
if ($action === 'read_all') {
    if ($is_db_online) {
        try {
            $pdo->exec("UPDATE `notifications` SET `is_read` = 1");
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        // Mock success for offline mode
        echo json_encode(['success' => true, 'offline' => true]);
    }
    exit;
}

// 3. Default: Fetch 5 Latest Notifications
$notifications = [];
$unread_count = 0;

if ($is_db_online) {
    try {
        // Get unread count
        $unread_count = (int)$pdo->query("SELECT COUNT(*) FROM `notifications` WHERE `is_read` = 0")->fetchColumn();
        
        // Get latest 5
        $stmt = $pdo->query("SELECT * FROM `notifications` ORDER BY `created_at` DESC LIMIT 5");
        $raw_notifs = $stmt->fetchAll();
        
        foreach ($raw_notifs as $n) {
            $notifications[] = [
                'id' => (int)$n['id'],
                'title' => $n['title'],
                'message' => $n['message'],
                'type' => $n['type'],
                'is_read' => (int)$n['is_read'],
                'link' => $n['link'],
                'relative_time' => getRelativeTime($n['created_at']),
                'created_at' => $n['created_at']
            ];
        }
    } catch (PDOException $e) {
        // Fallback silently or error message
    }
} else {
    // Read from mock data
    $mock_notifs = isset($mock_data['notifications']) ? $mock_data['notifications'] : [];
    
    // Sort mock notifications by ID desc (simulating DESC order)
    usort($mock_notifs, function($a, $b) {
        return $b['id'] - $a['id'];
    });
    
    // Calculate unread count
    foreach ($mock_notifs as $n) {
        if (isset($n['is_read']) && $n['is_read'] == 0) {
            $unread_count++;
        }
    }
    
    // Slice to 5
    $slice_notifs = array_slice($mock_notifs, 0, 5);
    foreach ($slice_notifs as $n) {
        $notifications[] = [
            'id' => (int)$n['id'],
            'title' => $n['title'],
            'message' => $n['message'],
            'type' => $n['type'],
            'is_read' => (int)$n['is_read'],
            'link' => $n['link'],
            'relative_time' => getRelativeTime($n['created_at']),
            'created_at' => $n['created_at']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'unread_count' => $unread_count,
    'notifications' => $notifications
]);
exit;
