<?php
// ========================================================
// BAKE'N BREW - Admin Manage Orders
// ========================================================

session_start();
require_once '../config/koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$success = '';
$error = '';

// 1. UPDATE ORDER STATUS TO COMPLETED
if ($action === 'complete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("UPDATE `orders` SET `status` = 'completed' WHERE `id` = ?");
        $stmt->execute([$id]);
        $_SESSION['success_msg'] = "Pesanan #$id berhasil ditandai selesai.";
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Database error: " . $e->getMessage();
    }
    header('Location: pesanan.php');
    exit;
}

// 2. DELETE ORDER LOG
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM `orders` WHERE `id` = ?");
        $stmt->execute([$id]);
        $_SESSION['success_msg'] = "Log pesanan #$id berhasil dihapus.";
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Database error: " . $e->getMessage();
    }
    header('Location: pesanan.php');
    exit;
}

// Fetch messages from session
if (isset($_SESSION['success_msg'])) {
    $success = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}
if (isset($_SESSION['error_msg'])) {
    $error = $_SESSION['error_msg'];
    unset($_SESSION['error_msg']);
}

// Filter and pagination config
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$limit = 12;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Build dynamic WHERE clause
$where_clauses = [];
$params = [];

if (!empty($filter_status)) {
    $where_clauses[] = "`status` = ?";
    $params[] = $filter_status;
}
if (!empty($search)) {
    $where_clauses[] = "(`customer_name` LIKE ? OR `customer_email` LIKE ? OR `product_name` LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_str = '';
if (count($where_clauses) > 0) {
    $where_str = "WHERE " . implode(" AND ", $where_clauses);
}

// Count total records
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM `orders` $where_str");
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Fetch records
$fetch_stmt = $pdo->prepare("SELECT * FROM `orders` $where_str ORDER BY `created_at` DESC LIMIT $limit OFFSET $offset");
$fetch_stmt->execute($params);
$orders = $fetch_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Pesanan – Bake'n Brew</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'></text></svg>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="admin-style.css?v=5.0" />
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="dashboard.php" class="sidebar-brand">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px; color: var(--accent-gold);">
            <path d="M17 8h1a4 4 0 1 1 0 8h-1" />
            <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8z" />
            <line x1="6" y1="2" x2="6" y2="4" />
            <line x1="10" y1="2" x2="10" y2="4" />
            <line x1="14" y1="2" x2="14" y2="4" />
        </svg>
        <h4>Bake'n <span>Brew</span></h4>
    </a>
    
    <div class="sidebar-nav">
        <a href="dashboard.php" class="nav-item-admin">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="produk.php" class="nav-item-admin">
            <i class="bi bi-egg-fried"></i> Kelola Menu
        </a>
        <a href="pesanan.php" class="nav-item-admin active">
            <i class="bi bi-cart3"></i> Kelola Pesanan
        </a>
    </div>

    
</div>

<!-- MAIN CONTENT -->
<div class="main-wrapper">
    <!-- TOP HEADER -->
    <header class="top-header">
        <h2>Kelola Pesanan</h2>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--brown-dark);">
                <span class="d-none d-sm-inline font-weight-medium me-1" style="font-size: 0.9rem;">Halo, <strong>Admin</strong></span>
                <div class="admin-avatar">A</div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="background-color: #ffffff; border-radius: var(--radius-md); min-width: 160px;">
                <li><h6 class="dropdown-header" style="color: var(--text-mid); font-family: 'Poppins', sans-serif;">Administrator</h6></li>
                <li><hr class="dropdown-divider" style="border-top: 1px solid var(--cream-dark);"></li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="logout.php" style="color: #d32f2f; font-weight: 500;">
                        <i class="bi bi-box-arrow-left" style="font-size: 1rem;"></i> Keluar
                    </a>
                </li>
            </ul>
        </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="page-content">
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: var(--radius-md);">
                <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: var(--radius-md);">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- FILTER & SEARCH PANEL -->
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <h4 class="m-0">Log Pesanan Pelanggan</h4>
                <div style="font-size: 0.85rem;" class="text-muted">Total: <strong><?= $total_records ?></strong> pesanan</div>
            </div>

            <form method="GET" action="" class="row g-2 mb-4 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-1" name="search" placeholder="Cari nama, email, atau menu..." value="<?= htmlspecialchars($search) ?>" />
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <select class="form-select form-select-sm" name="status">
                        <option value="">— Semua Status —</option>
                        <option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="completed" <?= $filter_status === 'completed' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn btn-admin-outline btn-sm w-100">Filter</button>
                    <?php if (!empty($search) || !empty($filter_status)): ?>
                        <a href="pesanan.php" class="btn btn-outline-secondary btn-sm" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-admin">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Pelanggan</th>
                            <th>Menu Yang Dipesan</th>
                            <th>Jumlah</th>
                            <th>Catatan</th>
                            <th>Waktu Pemesanan</th>
                            <th>Status</th>
                            <th style="width: 180px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders) > 0): ?>
                            <?php foreach ($orders as $order): 
                                // Strip price formatting from product name if exists
                                $clean_product = htmlspecialchars($order['product_name']);
                            ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold" style="color: var(--brown-dark);">#<?= $order['id'] ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold" style="color: var(--text-dark);"><?= htmlspecialchars($order['customer_name']) ?></div>
                                        <div style="font-size: 0.76rem; color: var(--text-mid);"><?= htmlspecialchars($order['customer_email']) ?></div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold" style="color: var(--brown-dark);"><?= $clean_product ?></span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: var(--brown-dark); color: var(--cream); padding: 0.35rem 0.75rem; border-radius: 20px;">
                                            <?= $order['quantity'] ?> pcs
                                        </span>
                                    </td>
                                    <td style="font-size: 0.82rem; max-width: 200px;" class="text-truncate" title="<?= htmlspecialchars($order['note']) ?>">
                                        <?= !empty($order['note']) ? htmlspecialchars($order['note']) : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td style="font-size: 0.78rem; color: var(--text-mid);">
                                        <?= date('d-m-Y H:i', strtotime($order['created_at'])) ?>
                                    </td>
                                    <td>
                                        <span class="<?= $order['status'] === 'completed' ? 'badge-status-completed' : 'badge-status-pending' ?>">
                                            <?= $order['status'] === 'completed' ? 'Selesai' : 'Pending' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <?php if ($order['status'] === 'pending'): ?>
                                                <a href="pesanan.php?action=complete&id=<?= $order['id'] ?>" class="btn btn-sm btn-admin-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background-color: #2e7d32;">
                                                    <i class="bi bi-check-lg"></i> Selesai
                                                </a>
                                            <?php endif; ?>
                                            <a href="pesanan.php?action=delete&id=<?= $order['id'] ?>" class="btn btn-sm btn-admin-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="return confirm('Apakah Anda yakin ingin menghapus log pesanan #<?= $order['id'] ?>?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted" style="padding: 2.5rem;">Tidak ada data pesanan yang cocok.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination-admin">
                    <?php if ($page > 1): ?>
                        <a href="pesanan.php?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($filter_status) ?>"><i class="bi bi-chevron-left"></i></a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="pesanan.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($filter_status) ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="pesanan.php?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($filter_status) ?>"><i class="bi bi-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    </main>
</div>

</body>
</html>
