<?php
// ========================================================
// BAKE'N BREW - Admin Dashboard
// ========================================================

session_start();
require_once '../config/koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 1. Handle Store Status Toggle Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_store') {
    if (!$is_db_online) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Aksi tidak diizinkan dalam Mode Offline.']);
            exit;
        }
        header('Location: dashboard.php');
        exit;
    }
    $new_status = $_POST['store_status'] === 'open' ? 'open' : 'closed';
    $stmt = $pdo->prepare("UPDATE `settings` SET `setting_value` = ? WHERE `setting_key` = 'store_status'");
    $stmt->execute([$new_status]);
    
    // Return JSON if AJAX request, otherwise page redirect
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'status' => $new_status]);
        exit;
    }
    header('Location: dashboard.php');
    exit;
}

// 2. Fetch Statistics
if ($is_db_online) {
    // Total Products
    $total_products = $pdo->query("SELECT COUNT(*) FROM `products`")->fetchColumn();

    // Total Orders
    $total_orders = $pdo->query("SELECT COUNT(*) FROM `orders`")->fetchColumn();

    // Store Status
    $store_status_stmt = $pdo->query("SELECT `setting_value` FROM `settings` WHERE `setting_key` = 'store_status'");
    $store_status = $store_status_stmt->fetchColumn();
    if (!$store_status) {
        $store_status = 'open'; // default fallback
    }

    // Recent Orders
    $recent_orders = $pdo->query("SELECT * FROM `orders` ORDER BY `created_at` DESC LIMIT 5")->fetchAll();

    // Category Counts
    $bakery_count = $pdo->query("SELECT COUNT(*) FROM `products` WHERE `category` = 'bakery'")->fetchColumn();
    $coffee_count = $pdo->query("SELECT COUNT(*) FROM `products` WHERE `category` = 'coffee'")->fetchColumn();
    $noncoffee_count = $pdo->query("SELECT COUNT(*) FROM `products` WHERE `category` = 'non-coffee'")->fetchColumn();
} else {
    // Fallback Mock Data
    $total_products = count($mock_data['products']);
    $total_orders = count($mock_data['orders']);
    $store_status = $mock_data['settings']['store_status'];
    $recent_orders = array_slice($mock_data['orders'], 0, 5);
    
    $bakery_count = count(array_filter($mock_data['products'], fn($p) => $p['category'] === 'bakery'));
    $coffee_count = count(array_filter($mock_data['products'], fn($p) => $p['category'] === 'coffee'));
    $noncoffee_count = count(array_filter($mock_data['products'], fn($p) => $p['category'] === 'non-coffee'));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin – Bake'n Brew</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'></text></svg>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="admin-style.css?v=5.2" />
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <a href="dashboard.php" class="nav-item-admin active">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="produk.php" class="nav-item-admin">
            <i class="bi bi-egg-fried"></i> Kelola Menu
        </a>
        <a href="pesanan.php" class="nav-item-admin">
            <i class="bi bi-cart3"></i> Kelola Pesanan
        </a>
    </div>

    
</div>

<!-- MAIN CONTENT -->
<div class="main-wrapper">
    <!-- TOP HEADER -->
    <header class="top-header">
        <h2>Dashboard</h2>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--brown-dark);">
                <span class="d-none d-sm-inline font-weight-medium me-1" style="font-size: 0.9rem;">Halo, <strong>Admin</strong></span>
                <div class="admin-avatar">A</div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="background-color: #ffffff; border-radius: var(--radius-md); min-width: 180px;">
                <li><h6 class="dropdown-header" style="color: var(--text-mid); font-family: 'Poppins', sans-serif;">Administrator</h6></li>
                <li><hr class="dropdown-divider" style="border-top: 1px solid var(--cream-dark);"></li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="#" style="color: var(--text-mid); opacity: 0.65; cursor: not-allowed;" onclick="event.preventDefault(); alert('Fitur Lihat Profil akan tersedia pada Fase 2.');">
                        <i class="bi bi-person" style="font-size: 1rem;"></i> Lihat Profil (Fase 2)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="logout.php" onclick="confirmLogout(event)" style="color: #d32f2f; font-weight: 500;">
                        <i class="bi bi-box-arrow-left" style="font-size: 1rem;"></i> Log Out
                    </a>
                </li>
            </ul>
        </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="page-content">
        <?php if (!$is_db_online): ?>
            <div class="alert d-flex align-items-center gap-3 mb-4 shadow-sm" role="alert" style="background: linear-gradient(135deg, #FFF3CD, #FFEBAA); border: 1px solid #FFE082; color: #856404; border-radius: var(--radius-md); padding: 1.2rem 1.5rem; font-family: 'Poppins', sans-serif;">
                <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.6rem; color: #E65100;"></i>
                <div>
                    <h5 class="fw-bold mb-1" style="font-size: 1.05rem; margin: 0; color: #E65100;">Koneksi Database Offline</h5>
                    <p class="mb-0" style="font-size: 0.88rem; margin: 0; font-weight: 500;">Peringatan: Koneksi server database terputus. Anda saat ini melihat data statis (Mode Offline).</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- STATISTICS CARDS -->
        <div class="row g-4 mb-4">
            <!-- Card 1: Total Menu -->
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-egg-fried"></i></div>
                    <div class="stat-details">
                        <h3><?= $total_products ?></h3>
                        <p>Total Menu</p>
                    </div>
                </div>
            </div>
            
            <!-- Card 2: Total Orders -->
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-cart3"></i></div>
                    <div class="stat-details">
                        <h3><?= $total_orders ?></h3>
                        <p>Total Pesanan</p>
                    </div>
                </div>
            </div>

            <!-- Card 3: Store Status -->
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-shop"></i></div>
                    <div class="stat-details">
                        <div class="d-flex align-items-center gap-2 mb-1" style="min-height: 43px;">
                            <span class="store-status-text <?= $store_status === 'open' ? 'text-success' : 'text-danger' ?>">
                                <?= $store_status === 'open' ? 'BUKA' : 'TUTUP' ?>
                            </span>
                            <form id="storeToggleForm" method="POST" action="">
                                <input type="hidden" name="action" value="toggle_store">
                                <input type="hidden" id="statusVal" name="store_status" value="<?= $store_status ?>">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="storeSwitch" <?= $store_status === 'open' ? 'checked' : '' ?> onchange="submitToggle()" <?= !$is_db_online ? 'disabled' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </form>
                        </div>
                        <p>Status Operasional</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- PIE CHART (Chart.js) -->
            <div class="col-lg-5">
                <div class="admin-card h-100">
                    <h4>Komposisi Menu</h4>
                    <div class="chart-container" style="position: relative; height:250px; margin: auto;">
                        <canvas id="menuCompositionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- RECENT ORDERS -->
            <div class="col-lg-7">
                <div class="admin-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="m-0">Pesanan Terbaru</h4>
                        <a href="pesanan.php" class="btn btn-admin-outline" style="font-size: 0.78rem; padding: 0.35rem 0.75rem;">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-admin">
                            <thead>
                                <tr>
                                    <th>Pelanggan</th>
                                    <th>Menu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($recent_orders) > 0): ?>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold"><?= htmlspecialchars($order['customer_name']) ?></div>
                                                <span style="font-size: 0.75rem;" class="text-muted"><?= htmlspecialchars($order['customer_email']) ?></span>
                                            </td>
                                            <td>
                                                <div><?= htmlspecialchars(preg_replace('/ \(.+\)/', '', $order['product_name'])) ?></div>
                                                <span class="badge bg-secondary" style="font-size: 0.7rem;"><?= $order['quantity'] ?> pcs</span>
                                            </td>
                                            <td>
                                                <span class="<?= $order['status'] === 'completed' ? 'badge-status-completed' : 'badge-status-pending' ?>">
                                                    <?= $order['status'] === 'completed' ? 'Selesai' : 'Pending' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted" style="padding: 2rem;">Belum ada pesanan masuk.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // Submit store status toggle via AJAX
    function submitToggle() {
        const checkbox = document.getElementById('storeSwitch');
        const statusText = document.querySelector('.store-status-text');
        const newStatus = checkbox.checked ? 'open' : 'closed';
        
        // Immediately update visual indicator
        if (newStatus === 'open') {
            statusText.textContent = 'BUKA';
            statusText.className = 'store-status-text text-success';
        } else {
            statusText.textContent = 'TUTUP';
            statusText.className = 'store-status-text text-danger';
        }
        
        const formData = new URLSearchParams();
        formData.append('action', 'toggle_store');
        formData.append('store_status', newStatus);
        
        fetch('dashboard.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData.toString()
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                // Rollback if failed
                checkbox.checked = !checkbox.checked;
                submitToggle();
                alert('Gagal memperbarui status toko.');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            checkbox.checked = !checkbox.checked;
            submitToggle();
            alert('Koneksi server gagal.');
        });
    }

    // Chart.js Configuration
    const ctx = document.getElementById('menuCompositionChart').getContext('2d');
    const menuCompositionChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Bakery', 'Coffee', 'Non-Coffee'],
            datasets: [{
                data: [<?= $bakery_count ?>, <?= $coffee_count ?>, <?= $noncoffee_count ?>],
                backgroundColor: [
                    '#4B2E2B', // Dark Brown for Bakery
                    '#C5A880', // Gold/Beige for Coffee
                    '#D6BFAF'  // Soft Beige for Non-Coffee
                ],
                borderWidth: 1,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: {
                            family: 'Poppins',
                            size: 11
                        }
                    }
                }
            }
        }
    });

    function confirmLogout(event) {
        event.preventDefault();
        if (confirm('Apakah Anda yakin ingin keluar?')) {
            sessionStorage.clear();
            localStorage.clear();
            window.location.href = 'logout.php';
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
