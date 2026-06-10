<?php
// ========================================================
// BAKE'N BREW - Automatic Database Setup & Seeding
// ========================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check environment: Local Laragon vs Clever Cloud vs InfinityFree Live Server
$is_local = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', '[::1]']) 
            || (strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1:') === 0) 
            || (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost:') === 0);

$port = null;
if (getenv('MYSQL_ADDON_HOST') !== false) {
    // Clever Cloud Auto-detect
    $host = getenv('MYSQL_ADDON_HOST');
    $user = getenv('MYSQL_ADDON_USER');
    $pass = getenv('MYSQL_ADDON_PASSWORD');
    $db   = getenv('MYSQL_ADDON_DB');
    $port = getenv('MYSQL_ADDON_PORT');
} else if ($is_local) {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'db_bakenbrew';
} else {
    // Production (InfinityFree)
    $host = 'sql313.infinityfree.com';
    $user = 'if0_42086787';
    $pass = 'bUI5qx7AgE0Nc';
    $db   = 'if0_42086787_db_bakenbrew';
}
$charset = 'utf8mb4';

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>Setup Database – Bake'n Brew</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background-color: #FAF6F0; font-family: 'Poppins', sans-serif; padding-top: 50px; }
        .setup-card { background: white; border-radius: 15px; border: 1px solid #D6BFAF; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .btn-custom { background: #4B2E2B; color: #FAF6F0; border: none; }
        .btn-custom:hover { background: #5c3a37; color: white; }
    </style>
</head>
<body>
<div class='container col-lg-6'>
    <div class='setup-card p-5'>
        <h2 class='text-center mb-4' style='font-family: \"Playfair Display\", serif; color: #4B2E2B;'>Database Setup BakenBrew</h2>
        <div class='mb-3'>";

try {
    // 1. Connect and initialize DB (only run CREATE DATABASE on local environment)
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    if ($is_local) {
        $pdo_init = new PDO("mysql:host=$host;charset=$charset", $user, $pass, $options);
        // Create DB
        $pdo_init->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<div class='alert alert-success'>✓ Database <strong>$db</strong> berhasil dibuat/dipastikan ada.</div>";
    } else {
        echo "<div class='alert alert-success'>✓ Menggunakan database hosting yang sudah ada: <strong>$db</strong>.</div>";
    }
    
    // Connect to specific DB
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset" . ($port ? ";port=$port" : ""), $user, $pass, $options);

    // 2. Create tables
    // Admin Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `admin` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "<div class='alert alert-success'>✓ Tabel <strong>admin</strong> berhasil dibuat/dipastikan ada.</div>";

    // Products Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `products` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `price` INT NOT NULL,
        `description` TEXT NOT NULL,
        `image` VARCHAR(255) NOT NULL,
        `category` ENUM('bakery', 'coffee', 'non-coffee') NOT NULL,
        `is_bestseller` TINYINT(1) DEFAULT 0,
        `is_new` TINYINT(1) DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "<div class='alert alert-success'>✓ Tabel <strong>products</strong> berhasil dibuat/dipastikan ada.</div>";

    // Orders Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `orders` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `customer_name` VARCHAR(100) NOT NULL,
        `customer_email` VARCHAR(100) NOT NULL,
        `product_name` VARCHAR(100) NOT NULL,
        `quantity` INT NOT NULL,
        `note` TEXT DEFAULT NULL,
        `status` ENUM('pending', 'completed') DEFAULT 'pending',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "<div class='alert alert-success'>✓ Tabel <strong>orders</strong> berhasil dibuat/dipastikan ada.</div>";

    // Notifications Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `notifications` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `type` VARCHAR(50) NOT NULL,
        `is_read` TINYINT(1) DEFAULT 0,
        `link` VARCHAR(255) DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "<div class='alert alert-success'>✓ Tabel <strong>notifications</strong> berhasil dibuat/dipastikan ada.</div>";

    // Settings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
        `setting_key` VARCHAR(50) PRIMARY KEY,
        `setting_value` VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "<div class='alert alert-success'>✓ Tabel <strong>settings</strong> berhasil dibuat/dipastikan ada.</div>";

    // Add indexes for performance optimization (Non-Functional Requirement)
    try {
        $pdo->exec("ALTER TABLE `products` ADD INDEX `idx_products_category` (`category`)");
    } catch (PDOException $e) { /* index might already exist */ }
    try {
        $pdo->exec("ALTER TABLE `products` ADD INDEX `idx_products_bestseller` (`is_bestseller`)");
    } catch (PDOException $e) { /* index might already exist */ }
    try {
        $pdo->exec("ALTER TABLE `products` ADD INDEX `idx_products_new` (`is_new`)");
    } catch (PDOException $e) { /* index might already exist */ }
    try {
        $pdo->exec("ALTER TABLE `orders` ADD INDEX `idx_orders_status` (`status`)");
    } catch (PDOException $e) { /* index might already exist */ }

    // 3. Seed Default Settings
    $stmt_set = $pdo->prepare("INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES ('store_status', 'open') 
        ON DUPLICATE KEY UPDATE `setting_value`=`setting_value` ");
    $stmt_set->execute();
    echo "<div class='alert alert-success'>✓ Pengaturan default (Status Toko: Buka) berhasil dimasukkan.</div>";

    // 4. Seed Default Admin
    $check_admin = $pdo->query("SELECT COUNT(*) FROM `admin`")->fetchColumn();
    if ($check_admin == 0) {
        $admin_user = 'admin';
        $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt_admin = $pdo->prepare("INSERT INTO `admin` (`username`, `password`) VALUES (?, ?)");
        $stmt_admin->execute([$admin_user, $admin_pass]);
        echo "<div class='alert alert-success'>✓ Akun Admin default berhasil dibuat:<br><strong>Username:</strong> admin<br><strong>Password:</strong> admin123</div>";
    } else {
        echo "<div class='alert alert-info'>i Akun Admin sudah ada di database, lewati seeding.</div>";
    }

    // 5. Seed Default 23 Products
    $check_products = $pdo->query("SELECT COUNT(*) FROM `products`")->fetchColumn();
    if ($check_products == 0) {
        $default_products = [
            // Bakery
            [
                'name' => 'Croissant Butter',
                'price' => 22000,
                'description' => 'Croissant berlapis mentega premium, renyah di luar, lembut di dalam. Dipanggang fresh setiap pagi.',
                'image' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&q=80&fm=webp',
                'category' => 'bakery',
                'is_bestseller' => 1,
                'is_new' => 0
            ],
            [
                'name' => 'Almond Croissant',
                'price' => 25000,
                'description' => 'Croissant berlapis dengan tambahan almond yang renyah, memberikan tekstur dan rasa yang khas. Dipanggang fresh setiap pagi.',
                'image' => 'https://images.unsplash.com/photo-1625425404751-19b16c027511?q=80&w=735&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp',
                'category' => 'bakery',
                'is_bestseller' => 0,
                'is_new' => 0
            ],
            [
                'name' => 'Strawberry Croissant',
                'price' => 28000,
                'description' => 'Croissant berlapis dengan tambahan strawberry yang lezat, memberikan tekstur dan rasa yang khas. Dipanggang fresh setiap pagi.',
                'image' => 'https://images.unsplash.com/photo-1721324412655-63d4885d9e67?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp',
                'category' => 'bakery',
                'is_bestseller' => 0,
                'is_new' => 1
            ],
            [
                'name' => 'Salt Bread',
                'price' => 25000,
                'description' => 'Roti yang lembut dan gurih, cocok untuk sarapan atau camilan. Dipanggang fresh setiap pagi.',
                'image' => 'https://images.unsplash.com/photo-1700284923285-90d6fe468920?q=80&w=721&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp',
                'category' => 'bakery',
                'is_bestseller' => 0,
                'is_new' => 0
            ],
            [
                'name' => 'Strawberry Danish',
                'price' => 28000,
                'description' => 'Danish berlapis dengan tambahan strawberry yang lezat, memberikan tekstur dan rasa yang khas. Dipanggang fresh setiap pagi.',
                'image' => 'https://images.unsplash.com/photo-1720091382934-fc9fdff94857?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp',
                'category' => 'bakery',
                'is_bestseller' => 0,
                'is_new' => 0
            ],
            [
                'name' => 'Donut Glazed',
                'price' => 15000,
                'description' => 'Donut empuk dengan glazing gula mengkilap. Tersedia rasa: original, coklat, stroberi, dan matcha.',
                'image' => 'https://images.unsplash.com/photo-1585459441171-70a603cd5e46?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp',
                'category' => 'bakery',
                'is_bestseller' => 1,
                'is_new' => 0
            ],
            [
                'name' => 'Roti Coklat',
                'price' => 18000,
                'description' => 'Roti lembut isi coklat premium. Lumer di dalam saat dimakan hangat, cocok untuk sarapan atau camilan sore.',
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80&fm=webp',
                'category' => 'bakery',
                'is_bestseller' => 0,
                'is_new' => 1
            ],
            [
                'name' => 'Cinnamon Roll',
                'price' => 25000,
                'description' => 'Gulungan roti hangat dengan isian kayu manis dan krim keju yang manis. Aroma harumnya mengisi seluruh kafe.',
                'image' => 'https://plus.unsplash.com/premium_photo-1722002219049-1c41e1a034c8?q=80&w=688&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'category' => 'bakery',
                'is_bestseller' => 0,
                'is_new' => 0
            ],
            [
                'name' => 'Banana Bread',
                'price' => 20000,
                'description' => 'Roti pisang lembut yang dipanggang sempurna. Dibuat dari pisang kepok matang pilihan, tanpa pengawet.',
                'image' => 'https://images.unsplash.com/photo-1596241913027-34358037e159?q=80&w=1025&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp',
                'category' => 'bakery',
                'is_bestseller' => 0,
                'is_new' => 0
            ],
            [
                'name' => 'Red Velvet Cake',
                'price' => 35000,
                'description' => 'Kue red velvet lembut dengan rasa khas dan tampilan yang menarik. Dibuat dengan bahan berkualitas tinggi.',
                'image' => 'https://images.unsplash.com/photo-1578937014788-b8318dc042a1?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp',
                'category' => 'bakery',
                'is_bestseller' => 0,
                'is_new' => 0
            ],
            [
                'name' => 'Cheesecake',
                'price' => 32000,
                'description' => 'Kue keju lembut dengan rasa khas dan tampilan yang menarik. Dibuat dengan bahan berkualitas tinggi.',
                'image' => 'https://images.unsplash.com/photo-1695088957420-c3b97d1f1138?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp',
                'category' => 'bakery',
                'is_bestseller' => 1,
                'is_new' => 0
            ],
            [
                'name' => 'Cheese Bun',
                'price' => 17000,
                'description' => 'Roti fluffy dengan topping keju cheddar meleleh dan taburan wijen. Favorit pelanggan semua usia.',
                'image' => 'https://plus.unsplash.com/premium_photo-1693086421089-847b0a2724f8?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'category' => 'bakery',
                'is_bestseller' => 1,
                'is_new' => 0
            ],
            // Coffee
            [
                'name' => 'Signature Latte',
                'price' => 28000,
                'description' => 'Espresso double shot dengan susu full cream yang di-steam sempurna. Creamy, smooth, dan selalu memuaskan.',
                'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400&q=80&fm=webp',
                'category' => 'coffee',
                'is_bestseller' => 1,
                'is_new' => 0
            ],
            [
                'name' => 'Cappuccino',
                'price' => 26000,
                'description' => 'Cappuccino klasik dengan busa susu tebal dan rasa espresso yang kaya. Disajikan dengan taburan bubuk coklat.',
                'image' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400&q=80&fm=webp',
                'category' => 'coffee',
                'is_bestseller' => 0,
                'is_new' => 0
            ],
            [
                'name' => 'Cold Brew',
                'price' => 32000,
                'description' => 'Kopi diseduh dingin selama 18 jam untuk menghasilkan rasa yang halus, kaya, dan rendah asam. Segar dan bold.',
                'image' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&q=80&fm=webp',
                'category' => 'coffee',
                'is_bestseller' => 0,
                'is_new' => 1
            ],
            [
                'name' => 'Espresso',
                'price' => 22000,
                'description' => 'Satu shot espresso pekat dari biji kopi arabika Flores single origin. Untuk yang suka rasa kopi yang pure dan autentik.',
                'image' => 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=400&q=80&fm=webp',
                'category' => 'coffee',
                'is_bestseller' => 0,
                'is_new' => 0
            ],
            [
                'name' => 'Americano',
                'price' => 24000,
                'description' => 'Americano klasik dengan rasa espresso yang kaya dan aroma yang kuat. Disajikan dengan es.',
                'image' => 'https://images.unsplash.com/photo-1531835207745-506a1bc035d8?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp',
                'category' => 'coffee',
                'is_bestseller' => 0,
                'is_new' => 0
            ],
            [
                'name' => 'Iced Caramel Latte',
                'price' => 35000,
                'description' => 'Latte karamel dingin dengan rasa manis dan karamel yang kaya. Disajikan dengan es dan taburan bubuk coklat.',
                'image' => 'https://images.unsplash.com/photo-1527678357412-ef45dfbd9ecc?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp',
                'category' => 'coffee',
                'is_bestseller' => 0,
                'is_new' => 1
            ],
            [
                'name' => 'Iced Hazelnut Coffee',
                'price' => 32000,
                'description' => 'Kopi dingin dengan rasa hazelnut yang kaya dan lembut. Disajikan dengan es dan taburan bubuk coklat.',
                'image' => 'https://images.unsplash.com/photo-1584286595398-a59f21d313f5?q=80&w=735&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp',
                'category' => 'coffee',
                'is_bestseller' => 0,
                'is_new' => 0
            ],
            // Non-Coffee
            [
                'name' => 'Matcha Latte',
                'price' => 30000,
                'description' => 'Matcha premium grade ceremonial dari Jepang dipadukan dengan susu segar. Creamy, earthy, dan menyegarkan.',
                'image' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=400&q=80&fm=webp',
                'category' => 'non-coffee',
                'is_bestseller' => 1,
                'is_new' => 0
            ],
            [
                'name' => 'Dark Chocolate',
                'price' => 25000,
                'description' => 'Minuman coklat panas atau dingin yang kaya rasa, dibuat dari coklat dark 70% premium. Indulge yourself!',
                'image' => 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=400&q=80&fm=webp',
                'category' => 'non-coffee',
                'is_bestseller' => 0,
                'is_new' => 0
            ],
            [
                'name' => 'Ice Tea',
                'price' => 15000,
                'description' => 'Minuman teh dingin yang segar dan menyegarkan, dibuat dari daun teh premium. Ideal untuk menemani hari yang panas!',
                'image' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=764&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp',
                'category' => 'non-coffee',
                'is_bestseller' => 0,
                'is_new' => 0
            ],
            [
                'name' => 'Lemon Tea',
                'price' => 20000,
                'description' => 'Teh hitam premium dengan perasan lemon segar dan sedikit madu. Segar, ringan, dan menyehatkan.',
                'image' => 'https://i.pinimg.com/736x/64/bb/bc/64bbbc45a302b646abee022c00ca0c41.jpg',
                'category' => 'non-coffee',
                'is_bestseller' => 0,
                'is_new' => 0
            ]
        ];

        $stmt = $pdo->prepare("INSERT INTO `products` (`name`, `price`, `description`, `image`, `category`, `is_bestseller`, `is_new`) VALUES (:name, :price, :description, :image, :category, :is_bestseller, :is_new)");
        foreach ($default_products as $p) {
            $stmt->execute($p);
        }
        echo "<div class='alert alert-success'>✓ Berhasil memasukkan 23 produk default ke dalam database.</div>";
    } else {
        echo "<div class='alert alert-info'>i Tabel products sudah terisi data, lewati seeding.</div>";
    }
    
    // 6. Seed Default Notifications
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
        echo "<div class='alert alert-success'>✓ Berhasil memasukkan notifikasi default ke dalam database.</div>";
    }
    
    echo "<div class='alert alert-success text-center mt-4 fw-bold'>PROSES SETUP DATABASE SELESAI DENGAN SUKSES!</div>";
    echo "<div class='text-center mt-3'><a href='../index.php' class='btn btn-custom px-4'>Kunjungi Website BakenBrew</a></div>";

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>❌ Terjadi kesalahan: " . $e->getMessage() . "</div>";
}

echo "</div>
    </div>
</div>
</body>
</html>";
?>
