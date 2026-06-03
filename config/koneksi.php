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

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // If the database doesn't exist yet, we allow connecting to host to create it (used in setup_db.php)
    if ($e->getCode() == 1049) { // Database not found
        try {
            $pdo_temp = new PDO("mysql:host=$host;charset=$charset", $user, $pass, $options);
            $pdo_temp->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $ex) {
            die("Koneksi database gagal: " . $ex->getMessage());
        }
    } else {
        die("Koneksi database gagal: " . $e->getMessage());
    }
}
?>
