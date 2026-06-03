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

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$error = '';
$success = '';

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
                    $_SESSION['success_msg'] = "Menu '$name' berhasil ditambahkan!";
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
                        $_SESSION['success_msg'] = "Menu '$name' berhasil diperbarui!";
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
        
        $_SESSION['success_msg'] = "Menu '" . $prod['name'] . "' berhasil dihapus beserta gambarnya.";
    }
    header('Location: produk.php');
    exit;
}

// Fetch success msg from session if set
if (isset($_SESSION['success_msg'])) {
    $success = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Menu – Bake'n Brew</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'></text></svg>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="admin-style.css" />
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
        <a href="produk.php" class="nav-item-admin active">
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
        <h2>Kelola Menu</h2>
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

        <!-- ========================================================
             1. FORM TAMBAH / EDIT
             ======================================================== -->
        <?php if ($action === 'add' || $action === 'edit'): 
            $form_title = 'Tambah Menu Baru';
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
                    $form_title = 'Edit Menu: ' . htmlspecialchars($prod['name']);
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
                            <label class="form-label" for="name">Nama Menu *</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name_val) ?>" required placeholder="Contoh: Matcha Cheese Croissant" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="price">Harga (Rupiah) *</label>
                            <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($price_val) ?>" required placeholder="Contoh: 28000" min="1000" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="category">Kategori Menu *</label>
                            <select class="form-select form-control" id="category" name="category" required>
                                <option value="bakery" <?= $cat_val === 'bakery' ? 'selected' : '' ?>>Bakery</option>
                                <option value="coffee" <?= $cat_val === 'coffee' ? 'selected' : '' ?>>Coffee</option>
                                <option value="non-coffee" <?= $cat_val === 'non-coffee' ? 'selected' : '' ?>>Non-Coffee</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="image">Foto Produk <?= $action === 'add' ? '*' : '(Kosongkan jika tidak ingin diubah)' ?></label>
                            <input type="file" class="form-control" id="image" name="image" <?= $action === 'add' ? 'required' : '' ?> accept="image/*" />
                            <?php if ($img_preview): ?>
                                <div class="mt-2">
                                    <span style="font-size: 0.78rem;" class="text-muted d-block mb-1">Preview Foto Saat Ini:</span>
                                    <img src="<?= $img_preview ?>" alt="Preview" style="height: 80px; border-radius: var(--radius-sm); object-fit: cover;" />
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="description">Deskripsi Menu *</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Jelaskan detail menu, rasa, dan keunggulan produk..." style="resize: none;"><?= htmlspecialchars($desc_val) ?></textarea>
                        </div>
                        <div class="col-md-6 d-flex gap-4 align-items-center mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_bestseller" name="is_bestseller" value="1" <?= $best_checked ?> style="border-color: var(--accent-gold);" />
                                <label class="form-check-label" for="is_bestseller">Lencana <strong>Best Seller</strong></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_new" name="is_new" value="1" <?= $new_checked ?> style="border-color: var(--accent-gold);" />
                                <label class="form-check-label" for="is_new">Lencana <strong>New</strong></label>
                            </div>
                        </div>
                        <div class="col-12 mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-admin-primary px-4 py-2">
                                <i class="bi bi-floppy me-2"></i>Simpan Perubahan
                            </button>
                            <a href="produk.php" class="btn btn-admin-outline px-4 py-2">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
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

            // Total Count query
            $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM `products` $where_str");
            $count_stmt->execute($params);
            $total_records = $count_stmt->fetchColumn();
            $total_pages = ceil($total_records / $limit);

            // Fetch Records query
            $fetch_stmt = $pdo->prepare("SELECT * FROM `products` $where_str ORDER BY `created_at` DESC LIMIT $limit OFFSET $offset");
            $fetch_stmt->execute($params);
            $products = $fetch_stmt->fetchAll();
        ?>
            <div class="admin-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <h4 class="m-0">Daftar Menu</h4>
                    <a href="produk.php?action=add" class="btn btn-admin-primary">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Menu
                    </a>
                </div>

                <!-- Filter & Search Form -->
                <form method="GET" action="" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-1" name="search" placeholder="Cari nama menu..." value="<?= htmlspecialchars($search) ?>" />
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <select class="form-select form-select-sm" name="filter_cat">
                            <option value="">— Semua Kategori —</option>
                            <option value="bakery" <?= $filter_cat === 'bakery' ? 'selected' : '' ?>>Bakery</option>
                            <option value="coffee" <?= $filter_cat === 'coffee' ? 'selected' : '' ?>>Coffee</option>
                            <option value="non-coffee" <?= $filter_cat === 'non-coffee' ? 'selected' : '' ?>>Non-Coffee</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 d-flex gap-2">
                        <button type="submit" class="btn btn-admin-outline btn-sm w-100">Filter</button>
                        <?php if (!empty($search) || !empty($filter_cat)): ?>
                            <a href="produk.php" class="btn btn-outline-secondary btn-sm" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                        <?php endif; ?>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-admin">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Foto</th>
                                <th>Nama Menu</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Lencana</th>
                                <th style="width: 140px; text-align: center;">Aksi</th>
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
                                            <?php if ($img_src): ?>
                                                <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($prod['name']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--cream-dark);" />
                                            <?php else: ?>
                                                <div style="width: 50px; height: 50px; background-color: var(--cream-dark); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 0.8rem;" class="text-muted"><i class="bi bi-image"></i></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-semibold" style="color: var(--text-dark);"><?= htmlspecialchars($prod['name']) ?></div>
                                            <div style="font-size: 0.76rem; color: var(--text-mid); max-width: 320px;" class="text-truncate"><?= htmlspecialchars($prod['description']) ?></div>
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
                                                <a href="produk.php?action=edit&id=<?= $prod['id'] ?>" class="btn btn-sm btn-admin-outline" style="padding: 0.25rem 0.5rem; font-size: 0.78rem;">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                                <a href="produk.php?action=delete&id=<?= $prod['id'] ?>" class="btn btn-sm btn-admin-danger" style="padding: 0.25rem 0.5rem; font-size: 0.78rem;" onclick="return confirm('Apakah Anda yakin ingin menghapus menu \'<?= htmlspecialchars(addslashes($prod['name'])) ?>\'?')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted" style="padding: 2rem;">Belum ada menu terdaftar.</td>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
