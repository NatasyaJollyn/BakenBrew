<?php
// ========================================================
// BAKE'N BREW - Admin Manage Products (CRUD)
// ========================================================

session_start();
require_once '../config/koneksi.php';

// Helper function to compress and convert images to WebP format
function compressAndSaveToWebp($source_path, $dest_path, $quality = 75) {
    $info = @getimagesize($source_path);
    if (!$info) return false;
    
    switch ($info['mime']) {
        case 'image/jpeg':
            $image = @imagecreatefromjpeg($source_path);
            break;
        case 'image/gif':
            $image = @imagecreatefromgif($source_path);
            break;
        case 'image/png':
            $image = @imagecreatefrompng($source_path);
            if ($image) {
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }
            break;
        case 'image/webp':
            $image = @imagecreatefromwebp($source_path);
            break;
        default:
            return false;
    }
    
    if (!$image) return false;
    
    $result = imagewebp($image, $dest_path, $quality);
    imagedestroy($image);
    return $result;
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Fetch active admin details
$admin_data = null;
if (isset($_SESSION['admin_username'])) {
    if ($is_db_online) {
        $stmt = $pdo->prepare("SELECT * FROM `admin` WHERE `username` = ?");
        $stmt->execute([$_SESSION['admin_username']]);
        $admin_data = $stmt->fetch();
    } else {
        $admin_data = [
            'username' => $_SESSION['admin_username'],
            'fullname' => 'Bake n Brew Admin (Offline)',
            'email' => 'admin@bakenbrew.com',
            'phone' => '081234567890',
            'avatar' => null,
            'role' => 'Administrator',
            'notif_sound' => 1,
            'lang' => 'en'
        ];
    }
}
require_once 'lang.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$error = '';
$success = '';

// Block write operations in offline mode
if (!$is_db_online && ($_SERVER['REQUEST_METHOD'] === 'POST' || $action === 'delete' || $action === 'add' || $action === 'edit')) {
    $_SESSION['error_msg'] = "Aksi tidak diizinkan dalam Mode Offline (Database Terputus).";
    header('Location: produk.php');
    exit;
}

// Folder paths
$upload_dir = '../public/images/products/';

// --------------------------------------------------------
// PROCESS POST ACTIONS (ADD, EDIT, DELETE)
// --------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. ADD PRODUCT
    if ($action === 'add') {
        $name = trim($_POST['name']);
        $category = $_POST['category'];
        $price = intval($_POST['price']);
        $description = trim($_POST['description']);
        $is_bestseller = isset($_POST['is_bestseller']) ? 1 : 0;
        $is_new = isset($_POST['is_new']) ? 1 : 0;
        
        // Handle Image Upload
        $image_filename = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_name = $_FILES['image']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($file_ext, $allowed_exts)) {
                $new_filename = 'prod_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
                $new_filename_webp = pathinfo($new_filename, PATHINFO_FILENAME) . '.webp';
                if (compressAndSaveToWebp($file_tmp, $upload_dir . $new_filename_webp)) {
                    $image_filename = $new_filename_webp;
                } else {
                    $error = 'Gagal melakukan kompresi dan penyimpanan gambar.';
                }
            } else {
                $error = 'Format file gambar tidak didukung (gunakan jpg, jpeg, png, atau webp).';
            }
        } else {
            $error = 'Foto menu wajib diunggah.';
        }

        if (empty($error)) {
            if (!empty($name) && !empty($category) && $price > 0 && !empty($description)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO `products` (`name`, `price`, `description`, `image`, `category`, `is_bestseller`, `is_new`) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $price, $description, $image_filename, $category, $is_bestseller, $is_new]);
                    $_SESSION['success_msg'] = sprintf(__('msg_menu_added'), $name);
                    header('Location: produk.php');
                    exit;
                } catch (PDOException $e) {
                    $error = 'Database error: ' . $e->getMessage();
                }
            } else {
                $error = 'Silakan isi seluruh input form dengan benar.';
            }
        }
    }

    // 2. EDIT PRODUCT
    if ($action === 'edit') {
        $id = intval($_GET['id']);
        $name = trim($_POST['name']);
        $category = $_POST['category'];
        $price = intval($_POST['price']);
        $description = trim($_POST['description']);
        $is_bestseller = isset($_POST['is_bestseller']) ? 1 : 0;
        $is_new = isset($_POST['is_new']) ? 1 : 0;
        
        // Fetch current product to check image
        $stmt_check = $pdo->prepare("SELECT * FROM `products` WHERE `id` = ?");
        $stmt_check->execute([$id]);
        $curr_prod = $stmt_check->fetch();
        
        if ($curr_prod) {
            $image_filename = $curr_prod['image'];
            
            // Check if new image uploaded
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['image']['tmp_name'];
                $file_name = $_FILES['image']['name'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($file_ext, $allowed_exts)) {
                    $new_filename = 'prod_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
                    $new_filename_webp = pathinfo($new_filename, PATHINFO_FILENAME) . '.webp';
                    if (compressAndSaveToWebp($file_tmp, $upload_dir . $new_filename_webp)) {
                        // Delete old local image file if it exists and is not an external URL
                        if (!empty($curr_prod['image']) && !str_starts_with($curr_prod['image'], 'http') && file_exists($upload_dir . $curr_prod['image'])) {
                            @unlink($upload_dir . $curr_prod['image']);
                        }
                        $image_filename = $new_filename_webp;
                    } else {
                        $error = 'Gagal melakukan kompresi dan penyimpanan gambar baru.';
                    }
                } else {
                    $error = 'Format file gambar tidak didukung.';
                }
            }
            
            if (empty($error)) {
                if (!empty($name) && !empty($category) && $price > 0 && !empty($description)) {
                    try {
                        $stmt = $pdo->prepare("UPDATE `products` SET `name` = ?, `price` = ?, `description` = ?, `image` = ?, `category` = ?, `is_bestseller` = ?, `is_new` = ? WHERE `id` = ?");
                        $stmt->execute([$name, $price, $description, $image_filename, $category, $is_bestseller, $is_new, $id]);
                        $_SESSION['success_msg'] = sprintf(__('msg_menu_updated'), $name);
                        header('Location: produk.php');
                        exit;
                    } catch (PDOException $e) {
                        $error = 'Database error: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Silakan isi seluruh input form dengan benar.';
                }
            }
        } else {
            $error = 'Menu tidak ditemukan.';
        }
    }
}

// 3. DELETE PRODUCT (GET REQUEST FOR CONVENIENCE)
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Fetch product to delete local image file
    $stmt_fetch = $pdo->prepare("SELECT * FROM `products` WHERE `id` = ?");
    $stmt_fetch->execute([$id]);
    $prod = $stmt_fetch->fetch();
    
    if ($prod) {
        // Delete database record
        $stmt_del = $pdo->prepare("DELETE FROM `products` WHERE `id` = ?");
        $stmt_del->execute([$id]);
        
        // Delete image file
        if (!empty($prod['image']) && !str_starts_with($prod['image'], 'http') && file_exists($upload_dir . $prod['image'])) {
            @unlink($upload_dir . $prod['image']);
        }
        
        $_SESSION['success_msg'] = sprintf(__('msg_menu_deleted'), $prod['name']);
    }
    header('Location: produk.php');
    exit;
}

// Fetch success msg from session if set
if (isset($_SESSION['success_msg'])) {
    $success = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}
if (isset($_SESSION['error_msg'])) {
    $error = $_SESSION['error_msg'];
    unset($_SESSION['error_msg']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= __('menu_title') ?> – Bake'n Brew</title>
    <link rel="icon" type="image/webp" href="../public/images/logo.webp?v=2" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="admin-style.css?v=5.2" />
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
            <i class="bi bi-speedometer2"></i> <?= __('nav_dashboard') ?>
        </a>
        <a href="produk.php" class="nav-item-admin active">
            <i class="bi bi-egg-fried"></i> <?= __('nav_menu') ?>
        </a>
        <a href="pesanan.php" class="nav-item-admin">
            <i class="bi bi-cart3"></i> <?= __('nav_orders') ?>
        </a>
    </div>

</div>

<!-- MAIN CONTENT -->
<div class="main-wrapper">
    <!-- TOP HEADER -->
    <header class="top-header">
        <h2><?= __('menu_title') ?></h2>
        <div class="d-flex align-items-center gap-3">
            
            <!-- Lonceng Notifikasi Dropdown -->
            <div class="dropdown" id="notificationDropdown">
                <a href="#" class="position-relative text-decoration-none dropdown-toggle-no-caret" id="notifBell" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--brown-dark);">
                    <i class="bi bi-bell" style="font-size: 1.4rem;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; padding: 0.25em 0.5em; display: none;" id="notifBadge">0</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-0" aria-labelledby="notifBell" style="width: 320px; border-radius: var(--radius-md); overflow: hidden; background-color: #ffffff;">
                    <li>
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="background-color: var(--cream-dark); font-family: 'Poppins', sans-serif;">
                            <span class="fw-bold" style="color: var(--brown-dark); font-size: 0.9rem;"><?= __('notif_title') ?></span>
                            <a href="#" class="text-decoration-none" style="font-size: 0.75rem; color: var(--accent-gold); font-weight: 500; display: none;" id="markAllReadHeader"><?= __('mark_all_read') ?></a>
                        </div>
                    </li>
                    <div class="notif-items-list" style="max-height: 280px; overflow-y: auto;">
                        <div class="p-3 text-center text-muted" style="font-size: 0.85rem;"><i class="bi bi-bell-slash me-1"></i> <?= __('no_notif') ?></div>
                    </div>
                    <li>
                        <a href="notifikasi.php" class="dropdown-item text-center py-2 border-top text-decoration-none fw-semibold" style="font-size: 0.8rem; color: var(--brown-dark); background-color: var(--cream);"><?= __('view_all_notif') ?></a>
                    </li>
                </ul>
            </div>

            <!-- Profile Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--brown-dark);">
                    <span class="d-none d-sm-inline font-weight-medium me-1" style="font-size: 0.9rem;"><?= __('halo') ?>, <strong><?= htmlspecialchars($admin_data['fullname'] ?? $admin_data['username'] ?? 'Admin') ?></strong></span>
                    <?php 
                    $avatar_img = '';
                    if (!empty($admin_data['avatar'])) {
                        $avatar_img = '../public/images/avatars/' . $admin_data['avatar'];
                    }
                    $initial = strtoupper(substr($admin_data['fullname'] ?? $admin_data['username'] ?? 'A', 0, 1));
                    if ($avatar_img && file_exists($avatar_img)): 
                    ?>
                        <img src="<?= $avatar_img ?>" alt="Avatar" class="admin-avatar" style="width: 38px; height: 38px; object-fit: cover; border-radius: 50%; border: 1px solid var(--accent-gold);" />
                    <?php else: ?>
                        <div class="admin-avatar"><?= $initial ?></div>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="background-color: #ffffff; border-radius: var(--radius-md); min-width: 180px;">
                    <li><h6 class="dropdown-header" style="color: var(--text-mid); font-family: 'Poppins', sans-serif;"><?= __('administrator') ?></h6></li>
                    <li><hr class="dropdown-divider" style="border-top: 1px solid var(--cream-dark);"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="profil.php" style="color: var(--text-mid);">
                            <i class="bi bi-person" style="font-size: 1rem;"></i> <?= __('profile') ?>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="logout.php" onclick="confirmLogout(event)" style="color: #d32f2f; font-weight: 500;">
                            <i class="bi bi-box-arrow-left" style="font-size: 1rem;"></i> <?= __('logout') ?>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="page-content">
        <?php if (!$is_db_online): ?>
            <div class="alert d-flex align-items-center gap-3 mb-4 shadow-sm" role="alert" style="background: linear-gradient(135deg, #FFF3CD, #FFEBAA); border: 1px solid #FFE082; color: #856404; border-radius: var(--radius-md); padding: 1.2rem 1.5rem; font-family: 'Poppins', sans-serif;">
                <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.6rem; color: #E65100;"></i>
                <div>
                    <h5 class="fw-bold mb-1" style="font-size: 1.05rem; margin: 0; color: #E65100;"><?= __('db_offline_title') ?></h5>
                    <p class="mb-0" style="font-size: 0.88rem; margin: 0; font-weight: 500;"><?= __('db_offline_msg') ?></p>
                </div>
            </div>
        <?php endif; ?>
        
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

        <!-- ========================================================
             1. FORM TAMBAH / EDIT
             ======================================================== -->
        <?php if ($action === 'add' || $action === 'edit'): 
            $form_title = __('btn_add_menu_new');
            $name_val = '';
            $price_val = '';
            $desc_val = '';
            $cat_val = 'bakery';
            $best_checked = '';
            $new_checked = '';
            $img_preview = '';

            if ($action === 'edit' && isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $stmt_fetch = $pdo->prepare("SELECT * FROM `products` WHERE `id` = ?");
                $stmt_fetch->execute([$id]);
                $prod = $stmt_fetch->fetch();
                
                if ($prod) {
                    $form_title = __('btn_edit') . ' ' . __('nav_menu') . ': ' . htmlspecialchars($prod['name']);
                    $name_val = $prod['name'];
                    $price_val = $prod['price'];
                    $desc_val = $prod['description'];
                    $cat_val = $prod['category'];
                    $best_checked = $prod['is_bestseller'] ? 'checked' : '';
                    $new_checked = $prod['is_new'] ? 'checked' : '';
                    
                    if (!empty($prod['image'])) {
                        if (str_starts_with($prod['image'], 'http')) {
                            $img_preview = $prod['image'];
                        } else {
                            $img_preview = $upload_dir . $prod['image'];
                        }
                    }
                } else {
                    echo "<div class='alert alert-danger'>Menu tidak ditemukan.</div>";
                    exit;
                }
            }
        ?>
            <div class="admin-card">
                <h4><?= $form_title ?></h4>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="name"><?= __('lbl_menu_name') ?> *</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name_val) ?>" required placeholder="<?= __('lbl_menu_name_placeholder') ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="price"><?= __('lbl_price') ?> *</label>
                            <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($price_val) ?>" required placeholder="<?= __('lbl_price_placeholder') ?>" min="1000" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="category"><?= __('lbl_category') ?> *</label>
                            <select class="form-select form-control" id="category" name="category" required>
                                <option value="bakery" <?= $cat_val === 'bakery' ? 'selected' : '' ?>>Bakery</option>
                                <option value="coffee" <?= $cat_val === 'coffee' ? 'selected' : '' ?>>Coffee</option>
                                <option value="non-coffee" <?= $cat_val === 'non-coffee' ? 'selected' : '' ?>>Non-Coffee</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="image"><?= $action === 'add' ? __('lbl_photo_hint_add') : __('lbl_photo_hint_edit') ?></label>
                            <input type="file" class="form-control" id="image" name="image" <?= $action === 'add' ? 'required' : '' ?> accept="image/*" />
                            <?php if ($img_preview): ?>
                                <div class="mt-2">
                                    <span style="font-size: 0.78rem;" class="text-muted d-block mb-1"><?= $lang_code === 'en' ? 'Current Photo Preview:' : 'Preview Foto Saat Ini:' ?></span>
                                    <img src="<?= $img_preview ?>" alt="Preview" style="height: 80px; border-radius: var(--radius-sm); object-fit: cover;" />
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="description"><?= __('lbl_description') ?> *</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required placeholder="<?= __('lbl_description_placeholder') ?>" style="resize: none;"><?= htmlspecialchars($desc_val) ?></textarea>
                        </div>
                        <div class="col-md-6 d-flex gap-4 align-items-center mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_bestseller" name="is_bestseller" value="1" <?= $best_checked ?> style="border-color: var(--accent-gold);" />
                                <label class="form-check-label" for="is_bestseller"><?= __('tbl_badge') ?> <strong>Best Seller</strong></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_new" name="is_new" value="1" <?= $new_checked ?> style="border-color: var(--accent-gold);" />
                                <label class="form-check-label" for="is_new"><?= __('tbl_badge') ?> <strong>New</strong></label>
                            </div>
                        </div>
                        <div class="col-12 mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-admin-primary px-4 py-2">
                                <i class="bi bi-floppy me-2"></i><?= __('btn_save_changes') ?>
                            </button>
                            <a href="produk.php" class="btn btn-admin-outline px-4 py-2">
                                <i class="bi bi-x-circle me-2"></i><?= __('btn_cancel') ?>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
                    </div>
                </form>
            </div>

        <!-- ========================================================
             2. TABEL DAFTAR PRODUK (LIST VIEW)
             ======================================================== -->
        <?php else: 
            // Handle Search & Filter parameters
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $filter_cat = isset($_GET['filter_cat']) ? $_GET['filter_cat'] : '';

            // Handle Pagination
            $limit = 10;
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            if ($page < 1) $page = 1;
            $offset = ($page - 1) * $limit;

            // Build query dynamically
            $params = [];
            $where_clauses = [];

            if (!empty($search)) {
                $where_clauses[] = "`name` LIKE ?";
                $params[] = "%$search%";
            }
            if (!empty($filter_cat)) {
                $where_clauses[] = "`category` = ?";
                $params[] = $filter_cat;
            }

            $where_str = '';
            if (count($where_clauses) > 0) {
                $where_str = "WHERE " . implode(" AND ", $where_clauses);
            }

            // Retrieve products
            if ($is_db_online) {
                // Total Count query
                $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM `products` $where_str");
                $count_stmt->execute($params);
                $total_records = $count_stmt->fetchColumn();
                $total_pages = ceil($total_records / $limit);

                // Fetch Records query
                $fetch_stmt = $pdo->prepare("SELECT * FROM `products` $where_str ORDER BY `created_at` DESC LIMIT $limit OFFSET $offset");
                $fetch_stmt->execute($params);
                $products = $fetch_stmt->fetchAll();
            } else {
                // Read from mock data
                $mock_products = $mock_data['products'];
                
                // Apply search & category filters locally
                if (!empty($search)) {
                    $mock_products = array_filter($mock_products, function($p) use ($search) {
                        return stripos($p['name'], $search) !== false || stripos($p['description'], $search) !== false;
                    });
                }
                if (!empty($filter_cat)) {
                    $mock_products = array_filter($mock_products, function($p) use ($filter_cat) {
                        return $p['category'] === $filter_cat;
                    });
                }
                
                $total_records = count($mock_products);
                $total_pages = ceil($total_records / $limit);
                
                // Pagination slice
                $products = array_slice($mock_products, $offset, $limit);
            }
        ?>
            <div class="admin-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <h4 class="m-0"><?= __('menu_list') ?></h4>
                    <a href="<?= $is_db_online ? 'produk.php?action=add' : '#' ?>" class="btn btn-admin-primary <?= !$is_db_online ? 'disabled' : '' ?>" style="<?= !$is_db_online ? 'pointer-events: none; opacity: 0.6; cursor: not-allowed;' : '' ?>">
                        <i class="bi bi-plus-lg me-1"></i> <?= __('btn_add_menu') ?>
                    </a>
                </div>

                <!-- Filter & Search Form -->
                <form method="GET" action="" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-1" name="search" placeholder="<?= __('search_placeholder_menu') ?>" value="<?= htmlspecialchars($search) ?>" />
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <select class="form-select form-select-sm" name="filter_cat">
                            <option value=""><?= __('filter_all_cat') ?></option>
                            <option value="bakery" <?= $filter_cat === 'bakery' ? 'selected' : '' ?>>Bakery</option>
                            <option value="coffee" <?= $filter_cat === 'coffee' ? 'selected' : '' ?>>Coffee</option>
                            <option value="non-coffee" <?= $filter_cat === 'non-coffee' ? 'selected' : '' ?>>Non-Coffee</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 d-flex gap-2">
                        <button type="submit" class="btn btn-admin-outline btn-sm w-100"><?= __('filter_btn') ?></button>
                        <?php if (!empty($search) || !empty($filter_cat)): ?>
                            <a href="produk.php" class="btn btn-outline-secondary btn-sm" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                        <?php endif; ?>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-admin">
                        <thead>
                            <tr>
                                <th><?= __('tbl_menu_name') ?></th>
                                <th><?= __('tbl_category') ?></th>
                                <th><?= __('tbl_price') ?></th>
                                <th><?= __('tbl_badge') ?></th>
                                <th style="width: 140px; text-align: center;"><?= __('tbl_actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($products) > 0): ?>
                                <?php foreach ($products as $prod): 
                                    $img_src = '';
                                    if (!empty($prod['image'])) {
                                        if (str_starts_with($prod['image'], 'http')) {
                                            $img_src = $prod['image'];
                                        } else {
                                            $img_src = $upload_dir . $prod['image'];
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td>
                                            <div class="table-media">
                                                <?php if ($img_src): ?>
                                                    <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="product-thumb" />
                                                <?php else: ?>
                                                    <div class="product-thumb text-muted d-flex align-items-center justify-content-center" style="background-color: var(--cream-dark);"><i class="bi bi-image"></i></div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-semibold" style="color: var(--text-dark);"><?= htmlspecialchars($prod['name']) ?></div>
                                                    <div style="font-size: 0.76rem; color: var(--text-mid); max-width: 320px;" class="text-truncate"><?= htmlspecialchars($prod['description']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-category"><?= htmlspecialchars(ucfirst($prod['category'])) ?></span>
                                        </td>
                                        <td class="fw-semibold" style="color: var(--brown-dark);">
                                            Rp <?= number_format($prod['price'], 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?php if ($prod['is_bestseller']): ?>
                                                    <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Best Seller</span>
                                                <?php endif; ?>
                                                <?php if ($prod['is_new']): ?>
                                                    <span class="badge bg-success" style="font-size: 0.65rem;">New</span>
                                                <?php endif; ?>
                                                <?php if (!$prod['is_bestseller'] && !$prod['is_new']): ?>
                                                    <span class="text-muted" style="font-size: 0.8rem;">-</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 justify-content-center">
                                                <a href="<?= $is_db_online ? 'produk.php?action=edit&id=' . $prod['id'] : '#' ?>" class="btn btn-sm btn-admin-outline <?= !$is_db_online ? 'disabled' : '' ?>" style="padding: 0.25rem 0.5rem; font-size: 0.78rem; <?= !$is_db_online ? 'pointer-events: none; opacity: 0.6; cursor: not-allowed;' : '' ?>">
                                                    <i class="bi bi-pencil-square"></i> <?= __('btn_edit') ?>
                                                </a>
                                                <a href="<?= $is_db_online ? 'produk.php?action=delete&id=' . $prod['id'] : '#' ?>" class="btn btn-sm btn-admin-danger <?= !$is_db_online ? 'disabled' : '' ?>" style="padding: 0.25rem 0.5rem; font-size: 0.78rem; <?= !$is_db_online ? 'pointer-events: none; opacity: 0.6; cursor: not-allowed;' : '' ?>" onclick="return <?= $is_db_online ? "confirm('" . sprintf(__('confirm_delete_menu_item'), htmlspecialchars(addslashes($prod['name']))) . "')" : 'false' ?>">
                                                    <i class="bi bi-trash"></i> <?= __('btn_delete') ?>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted" style="padding: 2rem;"><?= __('empty_menu') ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION VIEW -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination-admin">
                        <?php if ($page > 1): ?>
                            <a href="produk.php?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&filter_cat=<?= urlencode($filter_cat) ?>"><i class="bi bi-chevron-left"></i></a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="active"><?= $i ?></span>
                            <?php else: ?>
                                <a href="produk.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&filter_cat=<?= urlencode($filter_cat) ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="produk.php?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&filter_cat=<?= urlencode($filter_cat) ?>"><i class="bi bi-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        <?php endif; ?>

    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmLogout(event) {
    event.preventDefault();
    if (confirm('<?= __('confirm_logout') ?>')) {
        sessionStorage.clear();
        localStorage.clear();
        window.location.href = 'logout.php';
    }
}

// Function to mark individual notification read
function markNotificationRead(id) {
    $.ajax({
        url: 'get_notifications.php?action=read&id=' + id,
        method: 'GET'
    });
}

// Polling and notification fetching for Bell icon
let lastUnreadCount = 0;
function fetchNotifications() {
    $.ajax({
        url: 'get_notifications.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const count = response.unread_count;
                const badge = $('#notifBadge');
                
                // Show/hide badges
                if (count > 0) {
                    badge.text(count).show();
                    $('#markAllReadHeader').show();
                } else {
                    badge.hide();
                    $('#markAllReadHeader').hide();
                }

                // Shake bell if new notifications arrive
                if (count > lastUnreadCount && lastUnreadCount > 0) {
                    $('#notifBell').addClass('bell-jiggle');
                    // Play notification sound if preferred
                    <?php if (isset($admin_data['notif_sound']) && $admin_data['notif_sound'] == 1): ?>
                    try {
                        const audio = new Audio('../public/sounds/notification.mp3');
                        audio.play();
                    } catch(e) {}
                    <?php endif; ?>
                    setTimeout(() => {
                        $('#notifBell').removeClass('bell-jiggle');
                    }, 1000);
                }
                lastUnreadCount = count;

                // Build dropdown items
                const list = $('.notif-items-list');
                list.empty();
                
                if (response.notifications.length > 0) {
                    response.notifications.forEach(n => {
                        const isUnread = n.is_read == 0;
                        const bgStyle = isUnread ? 'background-color: #FFFDF4;' : 'background-color: #ffffff;';
                        const textWeight = isUnread ? 'font-weight: 600;' : 'font-weight: normal;';
                        const dot = isUnread ? '<span class="notif-dot"></span>' : '';
                        
                        const item = `
                            <li class="notif-dropdown-item border-bottom py-2 px-3" style="${bgStyle} list-style: none;">
                                <a href="${n.link || '#'}" onclick="markNotificationRead(${n.id})" class="text-decoration-none" style="color: var(--text-dark); display: block;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <span style="${textWeight} font-size: 0.85rem;">${n.title}${dot}</span>
                                        <span class="text-muted" style="font-size: 0.7rem;">${n.relative_time}</span>
                                    </div>
                                    <p class="mb-0 text-truncate text-muted" style="font-size: 0.78rem;" title="${n.message}">${n.message}</p>
                                </a>
                            </li>
                        `;
                        list.append(item);
                    });
                } else {
                    list.append('<div class="p-3 text-center text-muted" style="font-size: 0.85rem;"><i class="bi bi-bell-slash me-1"></i> <?= __('no_notif') ?></div>');
                }
            }
        }
    });
}

// Initial fetch and set interval
$(document).ready(function() {
    fetchNotifications();
    setInterval(fetchNotifications, 5000);

    // Mark all read handler
    $('#markAllReadHeader').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'get_notifications.php?action=read_all',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    fetchNotifications();
                }
            }
        });
    });
});
</script>

</body>
</html>
