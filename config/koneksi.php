<?php
// ========================================================
// BAKE'N BREW - Database Connection Configuration (Laragon)
// ========================================================

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_bakenbrew';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$is_db_online = true;
$mock_data = null;

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Ensure admin table has all required columns for profile management
    $stmt = $pdo->query("SHOW COLUMNS FROM `admin` LIKE 'fullname'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE `admin` ADD COLUMN `fullname` VARCHAR(100) DEFAULT 'Bake n Brew Admin'");
        $pdo->exec("ALTER TABLE `admin` ADD COLUMN `email` VARCHAR(100) DEFAULT 'admin@bakenbrew.com'");
        $pdo->exec("ALTER TABLE `admin` ADD COLUMN `phone` VARCHAR(20) DEFAULT '081234567890'");
        $pdo->exec("ALTER TABLE `admin` ADD COLUMN `avatar` VARCHAR(255) DEFAULT NULL");
        $pdo->exec("ALTER TABLE `admin` ADD COLUMN `role` VARCHAR(50) DEFAULT 'Administrator'");
        $pdo->exec("ALTER TABLE `admin` ADD COLUMN `notif_sound` TINYINT(1) DEFAULT 1");
        $pdo->exec("ALTER TABLE `admin` ADD COLUMN `lang` VARCHAR(10) DEFAULT 'id'");
    }

    // Ensure notifications table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS `notifications` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `type` VARCHAR(50) NOT NULL,
        `is_read` TINYINT(1) DEFAULT 0,
        `link` VARCHAR(255) DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Seed default notifications if empty
    $check_notifs = $pdo->query("SELECT COUNT(*) FROM `notifications`")->fetchColumn();
    if ($check_notifs == 0) {
        $default_notifs = [
            [
                'title' => 'Pesanan Baru Masuk',
                'message' => 'Pesanan baru dari Ahmad Dani (2 Item) menunggu konfirmasi.',
                'type' => 'new_order',
                'is_read' => 0,
                'link' => 'pesanan.php',
                'created_at' => date('Y-m-d H:i:s', time() - 180) // 3 minutes ago
            ],
            [
                'title' => 'Perubahan Status Stok',
                'message' => 'Peringatan: Stok bahan Croissant Butter hampir habis (Tersisa 5 pcs).',
                'type' => 'low_stock',
                'is_read' => 0,
                'link' => 'produk.php',
                'created_at' => date('Y-m-d H:i:s', time() - 3600) // 1 hour ago
            ],
            [
                'title' => 'Pembatalan Pesanan',
                'message' => 'Pesanan #104 telah dibatalkan oleh sistem/pelanggan.',
                'type' => 'cancelled_order',
                'is_read' => 1,
                'link' => 'pesanan.php',
                'created_at' => date('Y-m-d H:i:s', time() - 86400) // 1 day ago
            ]
        ];
        $stmt_n = $pdo->prepare("INSERT INTO `notifications` (`title`, `message`, `type`, `is_read`, `link`, `created_at`) VALUES (:title, :message, :type, :is_read, :link, :created_at)");
        foreach ($default_notifs as $n) {
            $stmt_n->execute($n);
        }
    }
} catch (PDOException $e) {
    // If the database doesn't exist yet, we allow connecting to host to create it (used in setup_db.php)
    if ($e->getCode() == 1049) { // Database not found
        try {
            $pdo_temp = new PDO("mysql:host=$host;charset=$charset", $user, $pass, $options);
            $pdo_temp->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo = new PDO($dsn, $user, $pass, $options);
            
            // Ensure admin table has all required columns for profile management
            $stmt = $pdo->query("SHOW COLUMNS FROM `admin` LIKE 'fullname'");
            if (!$stmt->fetch()) {
                $pdo->exec("ALTER TABLE `admin` ADD COLUMN `fullname` VARCHAR(100) DEFAULT 'Bake n Brew Admin'");
                $pdo->exec("ALTER TABLE `admin` ADD COLUMN `email` VARCHAR(100) DEFAULT 'admin@bakenbrew.com'");
                $pdo->exec("ALTER TABLE `admin` ADD COLUMN `phone` VARCHAR(20) DEFAULT '081234567890'");
                $pdo->exec("ALTER TABLE `admin` ADD COLUMN `avatar` VARCHAR(255) DEFAULT NULL");
                $pdo->exec("ALTER TABLE `admin` ADD COLUMN `role` VARCHAR(50) DEFAULT 'Administrator'");
                $pdo->exec("ALTER TABLE `admin` ADD COLUMN `notif_sound` TINYINT(1) DEFAULT 1");
                $pdo->exec("ALTER TABLE `admin` ADD COLUMN `lang` VARCHAR(10) DEFAULT 'id'");
            }

            // Ensure notifications table exists
            $pdo->exec("CREATE TABLE IF NOT EXISTS `notifications` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `message` TEXT NOT NULL,
                `type` VARCHAR(50) NOT NULL,
                `is_read` TINYINT(1) DEFAULT 0,
                `link` VARCHAR(255) DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        } catch (PDOException $ex) {
            $is_db_online = false;
            $pdo = null;
        }
    } else {
        $is_db_online = false;
        $pdo = null;
    }
}

// Load mock data if database is offline
if (!$is_db_online) {
    $mock_file = __DIR__ . '/mock_data.json';
    if (file_exists($mock_file)) {
        $mock_data = json_decode(file_get_contents($mock_file), true);
    }
}
?>
