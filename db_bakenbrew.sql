-- ========================================================
-- BAKE'N BREW - Database Dump
-- Target Server Type: MySQL / MariaDB
-- Generated automatically for project submission
-- ========================================================

CREATE DATABASE IF NOT EXISTS `db_bakenbrew` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_bakenbrew`;

-- --------------------------------------------------------
-- Table structure for table `admin`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(100) DEFAULT 'Bake n Brew Admin',
  `email` VARCHAR(100) DEFAULT 'admin@bakenbrew.com',
  `phone` VARCHAR(20) DEFAULT '081234567890',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `role` VARCHAR(50) DEFAULT 'Administrator',
  `notif_sound` TINYINT(1) DEFAULT 1,
  `lang` VARCHAR(10) DEFAULT 'en',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `admin`
INSERT INTO `admin` (`username`, `password`, `fullname`, `email`, `phone`, `role`, `notif_sound`, `lang`) VALUES
('admin', '$2y$10$UYtVGWP3ocNUO95RWzJQTu2AM2N8fFw9lcqaSEZPYa5WxXiFumqPq', 'Bake n Brew Admin', 'admin@bakenbrew.com', '081234567890', 'Administrator', 1, 'en');

-- --------------------------------------------------------
-- Table structure for table `products`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `price` INT NOT NULL,
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `category` ENUM('bakery', 'coffee', 'non-coffee') NOT NULL,
  `is_bestseller` TINYINT(1) DEFAULT 0,
  `is_new` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_products_category` (`category`),
  INDEX `idx_products_bestseller` (`is_bestseller`),
  INDEX `idx_products_new` (`is_new`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `products`
INSERT INTO `products` (`id`, `name`, `price`, `description`, `image`, `category`, `is_bestseller`, `is_new`) VALUES
(1, 'Croissant Butter', 22000, 'Croissant berlapis mentega premium, renyah di luar, lembut di dalam. Dipanggang fresh setiap pagi.', 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&q=80&fm=webp', 'bakery', 1, 0),
(2, 'Almond Croissant', 25000, 'Croissant berlapis dengan tambahan almond yang renyah, memberikan tekstur dan rasa yang khas. Dipanggang fresh setiap pagi.', 'https://images.unsplash.com/photo-1625425404751-19b16c027511?q=80&w=735&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp', 'bakery', 0, 0),
(3, 'Strawberry Croissant', 28000, 'Croissant berlapis dengan tambahan strawberry yang lezat, memberikan tekstur dan rasa yang khas. Dipanggang fresh setiap pagi.', 'https://images.unsplash.com/photo-1721324412655-63d4885d9e67?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp', 'bakery', 0, 1),
(4, 'Salt Bread', 25000, 'Roti yang lembut dan gurih, cocok untuk sarapan atau camilan. Dipanggang fresh setiap pagi.', 'https://images.unsplash.com/photo-1700284923285-90d6fe468920?q=80&w=721&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp', 'bakery', 0, 0),
(5, 'Strawberry Danish', 28000, 'Danish berlapis dengan tambahan strawberry yang lezat, memberikan tekstur dan rasa yang khas. Dipanggang fresh setiap pagi.', 'https://images.unsplash.com/photo-1720091382934-fc9fdff94857?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp', 'bakery', 0, 0),
(6, 'Donut Glazed', 15000, 'Donut empuk dengan glazing gula mengkilap. Tersedia rasa: original, coklat, stroberi, dan matcha.', 'https://images.unsplash.com/photo-1585459441171-70a603cd5e46?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp', 'bakery', 1, 0),
(7, 'Roti Coklat', 18000, 'Roti lembut isi coklat premium. Lumer di dalam saat dimakan hangat, cocok untuk sarapan atau camilan sore.', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80&fm=webp', 'bakery', 0, 1),
(8, 'Cinnamon Roll', 25000, 'Gulungan roti hangat dengan isian kayu manis dan krim keju yang manis. Aroma harumnya mengisi seluruh kafe.', 'https://plus.unsplash.com/premium_photo-1722002219049-1c41e1a034c8?q=80&w=688&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'bakery', 0, 0),
(9, 'Banana Bread', 20000, 'Roti pisang lembut yang dipanggang sempurna. Dibuat dari pisang kepok matang pilihan, tanpa pengawet.', 'https://images.unsplash.com/photo-1596241913027-34358037e159?q=80&w=1025&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp', 'bakery', 0, 0),
(10, 'Red Velvet Cake', 35000, 'Kue red velvet lembut dengan rasa khas dan tampilan yang menarik. Dibuat dengan bahan berkualitas tinggi.', 'https://images.unsplash.com/photo-1578937014788-b8318dc042a1?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp', 'bakery', 0, 0),
(11, 'Cheesecake', 32000, 'Kue keju lembut dengan rasa khas dan tampilan yang menarik. Dibuat dengan bahan berkualitas tinggi.', 'https://images.unsplash.com/photo-1695088957420-c3b97d1f1138?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp', 'bakery', 1, 0),
(12, 'Cheese Bun', 17000, 'Roti fluffy dengan topping keju cheddar meleleh dan taburan wijen. Favorit pelanggan semua usia.', 'https://plus.unsplash.com/premium_photo-1693086421089-847b0a2724f8?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'bakery', 1, 0),
(13, 'Signature Latte', 28000, 'Espresso double shot dengan susu full cream yang di-steam sempurna. Creamy, smooth, dan selalu memuaskan.', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400&q=80&fm=webp', 'coffee', 1, 0),
(14, 'Cappuccino', 26000, 'Cappuccino klasik dengan busa susu tebal dan rasa espresso yang kaya. Disajikan dengan taburan bubuk coklat.', 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400&q=80&fm=webp', 'coffee', 0, 0),
(15, 'Cold Brew', 32000, 'Kopi diseduh dingin selama 18 jam untuk menghasilkan rasa yang halus, kaya, dan rendah asam. Segar dan bold.', 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&q=80&fm=webp', 'coffee', 0, 1),
(16, 'Espresso', 22000, 'Satu shot espresso pekat dari biji kopi arabika Flores single origin. Untuk yang suka rasa kopi yang pure dan autentik.', 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=400&q=80&fm=webp', 'coffee', 0, 0),
(17, 'Americano', 24000, 'Americano klasik dengan rasa espresso yang kaya dan aroma yang kuat. Disajikan dengan es.', 'https://images.unsplash.com/photo-1531835207745-506a1bc035d8?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp', 'coffee', 0, 0),
(18, 'Iced Caramel Latte', 35000, 'Latte karamel dingin dengan rasa manis dan karamel yang kaya. Disajikan dengan es dan taburan bubuk coklat.', 'https://images.unsplash.com/photo-1527678357412-ef45dfbd9ecc?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp', 'coffee', 0, 1),
(19, 'Iced Hazelnut Coffee', 32000, 'Kopi dingin dengan rasa hazelnut yang kaya dan lembut. Disajikan dengan es dan taburan bubuk coklat.', 'https://images.unsplash.com/photo-1584286595398-a59f21d313f5?q=80&w=735&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp', 'coffee', 0, 0),
(20, 'Matcha Latte', 30000, 'Matcha premium grade ceremonial dari Jepang dipadukan dengan susu segar. Creamy, earthy, dan menyegarkan.', 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=400&q=80&fm=webp', 'non-coffee', 1, 0),
(21, 'Dark Chocolate', 25000, 'Minuman coklat panas atau dingin yang kaya rasa, dibuat dari coklat dark 70% premium. Indulge yourself!', 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=400&q=80&fm=webp', 'non-coffee', 0, 0),
(22, 'Ice Tea', 15000, 'Minuman teh dingin yang segar dan menyegarkan, dibuat dari daun teh premium. Ideal untuk menemani hari yang panas!', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=764&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp', 'non-coffee', 0, 0),
(23, 'Lemon Tea', 20000, 'Teh hitam premium dengan perasan lemon segar dan sedikit madu. Segar, ringan, dan menyehatkan.', 'https://i.pinimg.com/736x/64/bb/bc/64bbbc45a302b646abee022c00ca0c41.jpg', 'non-coffee', 0, 0);

-- --------------------------------------------------------
-- Table structure for table `orders`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(100) NOT NULL,
  `product_name` VARCHAR(100) NOT NULL,
  `quantity` INT NOT NULL,
  `note` TEXT DEFAULT NULL,
  `status` ENUM('pending', 'completed') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_orders_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `notifications`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `link` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `notifications`
INSERT INTO `notifications` (`title`, `message`, `type`, `is_read`, `link`) VALUES
('Pesanan Baru Masuk', 'Pesanan baru dari Ahmad Dani (2 Item) menunggu konfirmasi.', 'new_order', 0, 'pesanan.php'),
('Perubahan Status Stok', 'Peringatan: Stok bahan Croissant Butter hampir habis (Tersisa 5 pcs).', 'low_stock', 0, 'produk.php'),
('Pembatalan Pesanan', 'Pesanan #104 telah dibatalkan oleh sistem/pelanggan.', 'cancelled_order', 1, 'pesanan.php');

-- --------------------------------------------------------
-- Table structure for table `settings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `setting_key` VARCHAR(50) PRIMARY KEY,
  `setting_value` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `settings`
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('store_status', 'open');
