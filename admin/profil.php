<?php
// ========================================================
// BAKE'N BREW - Admin Profile & Settings
// ========================================================

session_start();
require_once '../config/koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

// Folder path for avatars
$avatar_dir = '../public/images/avatars/';
if (!file_exists($avatar_dir)) {
    @mkdir($avatar_dir, 0777, true);
}

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

// Block write operations on offline mode
if (!$is_db_online && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['error_msg'] = __('err_offline_post');
    header('Location: profil.php?tab=' . $active_tab);
    exit;
}

// Process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. UPDATE PROFILE DATA
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $fullname = trim($_POST['fullname']);
        $phone = trim($_POST['phone']);
        
        if (empty($fullname)) {
            $error = __('err_fullname_empty');
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE `admin` SET `fullname` = ?, `phone` = ? WHERE `username` = ?");
                $stmt->execute([$fullname, $phone, $_SESSION['admin_username']]);
                $_SESSION['success_msg'] = __('msg_profile_success');
                header('Location: profil.php?tab=profile');
                exit;
            } catch (PDOException $e) {
                $error = __('err_profile_fail') . $e->getMessage();
            }
        }
    }
    
    // 2. UPLOAD OR REMOVE AVATAR
    if (isset($_POST['action']) && $_POST['action'] === 'update_avatar') {
        if (isset($_POST['remove_avatar']) && $_POST['remove_avatar'] == 1) {
            // Remove avatar
            if ($admin_data && !empty($admin_data['avatar'])) {
                $old_avatar = $avatar_dir . $admin_data['avatar'];
                if (file_exists($old_avatar)) {
                    @unlink($old_avatar);
                }
            }
            try {
                $stmt = $pdo->prepare("UPDATE `admin` SET `avatar` = NULL WHERE `username` = ?");
                $stmt->execute([$_SESSION['admin_username']]);
                $_SESSION['success_msg'] = __('msg_avatar_removed');
                header('Location: profil.php?tab=profile');
                exit;
            } catch (PDOException $e) {
                $error = __('err_avatar_remove_fail') . $e->getMessage();
            }
        } elseif (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['avatar']['tmp_name'];
            $file_name = $_FILES['avatar']['name'];
            $file_size = $_FILES['avatar']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($file_ext, $allowed_exts)) {
                $error = __('err_avatar_invalid_ext');
            } elseif ($file_size > 2 * 1024 * 1024) {
                $error = __('err_avatar_too_large');
            } else {
                $new_filename = 'avatar_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
                $new_filename_webp = pathinfo($new_filename, PATHINFO_FILENAME) . '.webp';
                
                if (compressAndSaveToWebp($file_tmp, $avatar_dir . $new_filename_webp)) {
                    // Delete old avatar
                    if ($admin_data && !empty($admin_data['avatar'])) {
                        $old_avatar = $avatar_dir . $admin_data['avatar'];
                        if (file_exists($old_avatar)) {
                            @unlink($old_avatar);
                        }
                    }
                    try {
                        $stmt = $pdo->prepare("UPDATE `admin` SET `avatar` = ? WHERE `username` = ?");
                        $stmt->execute([$new_filename_webp, $_SESSION['admin_username']]);
                        $_SESSION['success_msg'] = __('msg_avatar_uploaded');
                        header('Location: profil.php?tab=profile');
                        exit;
                    } catch (PDOException $e) {
                        $error = __('err_avatar_db_fail') . $e->getMessage();
                    }
                } else {
                    $error = __('err_avatar_upload_fail');
                }
            }
        } else {
            $error = __('err_avatar_select');
        }
    }

    // 3. CHANGE PASSWORD
    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $curr_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $conf_pass = $_POST['confirm_password'];
        
        if (empty($curr_pass) || empty($new_pass) || empty($conf_pass)) {
            $error = __('err_pass_required');
        } elseif ($new_pass !== $conf_pass) {
            $error = __('err_pass_mismatch');
        } elseif (strlen($new_pass) < 6) {
            $error = __('err_pass_length');
        } else {
            // Verify current password
            if (password_verify($curr_pass, $admin_data['password'])) {
                try {
                    $new_hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE `admin` SET `password` = ? WHERE `username` = ?");
                    $stmt->execute([$new_hashed_pass, $_SESSION['admin_username']]);
                    $_SESSION['success_msg'] = __('msg_pass_success');
                    header('Location: profil.php?tab=security');
                    exit;
                } catch (PDOException $e) {
                    $error = __('err_pass_fail') . $e->getMessage();
                }
            } else {
                $error = __('err_pass_invalid');
            }
        }
    }

    // 4. UPDATE SYSTEM PREFERENCES
    if (isset($_POST['action']) && $_POST['action'] === 'update_preferences') {
        $notif_sound = isset($_POST['notif_sound']) ? 1 : 0;
        $lang = $_POST['lang'] === 'en' ? 'en' : 'id';
        
        try {
            $stmt = $pdo->prepare("UPDATE `admin` SET `notif_sound` = ?, `lang` = ? WHERE `username` = ?");
            $stmt->execute([$notif_sound, $lang, $_SESSION['admin_username']]);
            $_SESSION['success_msg'] = __('msg_prefs_success');
            header('Location: profil.php?tab=security');
            exit;
        } catch (PDOException $e) {
            $error = __('err_prefs_fail') . $e->getMessage();
        }
    }
}

// Retrieve success/error messages
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
    <title><?= __('profile_title') ?> – Bake'n Brew</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'></text></svg>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="admin-style.css?v=5.2" />
    <style>
        .nav-tabs-profile .nav-link {
            color: var(--text-mid);
            font-weight: 500;
            border: none;
            border-bottom: 2px solid transparent;
            padding: 0.8rem 1.5rem;
            transition: all 0.3s ease;
        }
        .nav-tabs-profile .nav-link:hover {
            color: var(--brown-dark);
            border-bottom: 2px solid var(--cream-dark);
        }
        .nav-tabs-profile .nav-link.active {
            color: var(--brown-dark);
            font-weight: 600;
            background: none;
            border-bottom: 2px solid var(--accent-gold);
        }
        .profile-avatar-large {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background-color: var(--cream-dark);
            color: var(--brown-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            border: 2.5px solid var(--accent-gold);
            margin: 0 auto 1.5rem auto;
            position: relative;
            overflow: hidden;
        }
        .profile-avatar-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .password-field-wrapper {
            position: relative;
        }
        .password-toggle-eye {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-mid);
            font-size: 1.1rem;
            transition: color 0.2s ease;
        }
        .password-toggle-eye:hover {
            color: var(--brown-dark);
        }
    </style>
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
        <a href="produk.php" class="nav-item-admin">
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
        <h2><?= __('profile_title') ?></h2>
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

        <div class="row">
            <!-- Avatar Card -->
            <div class="col-lg-4 mb-4">
                <div class="admin-card text-center py-5">
                    <div class="profile-avatar-large shadow-sm">
                        <?php if ($avatar_img && file_exists($avatar_img)): ?>
                            <img src="<?= $avatar_img ?>" alt="Avatar" id="avatarPreviewLarge" />
                        <?php else: ?>
                            <span id="avatarInitialsLarge"><?= $initial ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <h4 class="mb-1" style="color: var(--text-dark);"><?= htmlspecialchars($admin_data['fullname'] ?? 'Bake n Brew Admin') ?></h4>
                    <p class="text-muted mb-4" style="font-size: 0.85rem;">@<?= htmlspecialchars($admin_data['username']) ?> &bull; <span class="badge" style="background-color: var(--brown-dark); font-weight: 500; font-size: 0.75rem;"><?= htmlspecialchars($admin_data['role'] ?? __('administrator')) ?></span></p>

                    <form method="POST" action="profil.php?tab=profile" enctype="multipart/form-data" class="d-flex flex-column gap-2 px-3">
                        <input type="hidden" name="action" value="update_avatar" />
                        
                        <div class="d-grid">
                            <label for="avatarInput" class="btn btn-admin-outline btn-sm <?= !$is_db_online ? 'disabled' : '' ?>" style="<?= !$is_db_online ? 'pointer-events: none; opacity: 0.6;' : '' ?>">
                                <i class="bi bi-upload me-1"></i> <?= __('btn_select_photo') ?>
                            </label>
                            <input type="file" name="avatar" id="avatarInput" accept=".jpg, .jpeg, .png" class="d-none" onchange="this.form.submit()" <?= !$is_db_online ? 'disabled' : '' ?> />
                        </div>
                        
                        <?php if ($avatar_img): ?>
                            <div class="d-grid">
                                <button type="submit" name="remove_avatar" value="1" class="btn btn-sm btn-outline-danger" onclick="return confirm('<?= __('confirm_delete_avatar') ?>')" <?= !$is_db_online ? 'disabled' : '' ?>>
                                    <i class="bi bi-trash me-1"></i> <?= __('btn_remove_photo') ?>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <span class="text-muted" style="font-size: 0.75rem;"><?= __('avatar_hint') ?></span>
                    </form>
                </div>
            </div>

            <!-- Settings Fields Card -->
            <div class="col-lg-8 mb-4">
                <div class="admin-card p-0" style="overflow: hidden;">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs nav-tabs-profile border-bottom px-4 pt-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link <?= $active_tab === 'profile' ? 'active' : '' ?>" href="profil.php?tab=profile" role="tab">
                                <i class="bi bi-person-gear me-1"></i> <?= __('tab_profile') ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $active_tab === 'security' ? 'active' : '' ?>" href="profil.php?tab=security" role="tab">
                                <i class="bi bi-shield-lock me-1"></i> <?= __('tab_security') ?>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content p-4">
                        
                        <!-- TAB 1: PROFILE ACCOUNT -->
                        <div class="tab-pane fade show active" id="profileTab" role="tabpanel" style="<?= $active_tab !== 'profile' ? 'display: none;' : '' ?>">
                            <h5 class="mb-4" style="color: var(--brown-dark); font-weight: 600;"><?= __('personal_data') ?></h5>
                            
                            <form method="POST" action="profil.php?tab=profile">
                                <input type="hidden" name="action" value="update_profile" />
                                
                                <div class="mb-3">
                                    <label class="form-label"><?= __('lbl_fullname') ?></label>
                                    <input type="text" class="form-control" name="fullname" value="<?= htmlspecialchars($admin_data['fullname'] ?? '') ?>" required <?= !$is_db_online ? 'disabled' : '' ?> />
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><?= __('lbl_email') ?> <span class="text-muted" style="font-size: 0.75rem;"><?= __('lbl_email_hint') ?></span></label>
                                    <input type="email" class="form-control text-muted" name="email" value="<?= htmlspecialchars($admin_data['email'] ?? 'admin@bakenbrew.com') ?>" disabled />
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label"><?= __('lbl_phone') ?></label>
                                    <input type="tel" class="form-control" name="phone" placeholder="Contoh: 081234567890" value="<?= htmlspecialchars($admin_data['phone'] ?? '') ?>" <?= !$is_db_online ? 'disabled' : '' ?> />
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label d-block"><?= __('lbl_role') ?></label>
                                    <span class="badge" style="background-color: var(--accent-gold); padding: 0.5rem 1rem; border-radius: 20px; font-weight: 500;"><?= htmlspecialchars($admin_data['role'] ?? __('administrator')) ?></span>
                                </div>
                                
                                <hr class="my-4" style="border-top: 1px solid var(--cream-dark);">
                                
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-admin-primary px-4" <?= !$is_db_online ? 'disabled' : '' ?>><?= __('btn_save_changes') ?></button>
                                </div>
                            </form>
                        </div>

                        <!-- TAB 2: SECURITY & PREFERENCES -->
                        <div class="tab-pane fade show active" id="securityTab" role="tabpanel" style="<?= $active_tab !== 'security' ? 'display: none;' : '' ?>">
                            <!-- Ubah Password -->
                            <h5 class="mb-4" style="color: var(--brown-dark); font-weight: 600;"><?= __('change_password') ?></h5>
                            <form method="POST" action="profil.php?tab=security" class="mb-5">
                                <input type="hidden" name="action" value="change_password" />
                                
                                <div class="mb-3">
                                    <label class="form-label"><?= __('lbl_current_pass') ?></label>
                                    <div class="password-field-wrapper">
                                        <input type="password" class="form-control password-input" name="current_password" required <?= !$is_db_online ? 'disabled' : '' ?> />
                                        <i class="bi bi-eye password-toggle-eye" onclick="togglePasswordVisibility(this)"></i>
                                    </div>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label"><?= __('lbl_new_pass') ?></label>
                                        <div class="password-field-wrapper">
                                            <input type="password" class="form-control password-input" name="new_password" required <?= !$is_db_online ? 'disabled' : '' ?> />
                                            <i class="bi bi-eye password-toggle-eye" onclick="togglePasswordVisibility(this)"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?= __('lbl_confirm_pass') ?></label>
                                        <div class="password-field-wrapper">
                                            <input type="password" class="form-control password-input" name="confirm_password" required <?= !$is_db_online ? 'disabled' : '' ?> />
                                            <i class="bi bi-eye password-toggle-eye" onclick="togglePasswordVisibility(this)"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-admin-primary px-4" <?= !$is_db_online ? 'disabled' : '' ?>><?= __('btn_update_pass') ?></button>
                                </div>
                            </form>
                            
                            <hr class="my-4" style="border-top: 1px solid var(--cream-dark);">
                            
                            <!-- Preferensi Sistem -->
                            <h5 class="mb-4" style="color: var(--brown-dark); font-weight: 600;"><?= __('lbl_sys_prefs') ?></h5>
                            <form method="POST" action="profil.php?tab=security">
                                <input type="hidden" name="action" value="update_preferences" />
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" id="soundToggle" name="notif_sound" value="1" <?= (isset($admin_data['notif_sound']) && $admin_data['notif_sound'] == 1) ? 'checked' : '' ?> <?= !$is_db_online ? 'disabled' : '' ?> />
                                    <label class="form-check-label" for="soundToggle"><?= __('lbl_enable_sound') ?></label>
                                </div>
                                
                                <div class="mb-4 col-md-6">
                                    <label class="form-label"><?= __('lbl_default_lang') ?></label>
                                    <select class="form-select" name="lang" <?= !$is_db_online ? 'disabled' : '' ?>>
                                        <option value="id" <?= (isset($admin_data['lang']) && $admin_data['lang'] === 'id') ? 'selected' : '' ?>><?= (isset($admin_data['lang']) && $admin_data['lang'] === 'en') ? 'Indonesian' : 'Bahasa Indonesia' ?></option>
                                        <option value="en" <?= (isset($admin_data['lang']) && $admin_data['lang'] === 'en') ? 'selected' : '' ?>><?= (isset($admin_data['lang']) && $admin_data['lang'] === 'en') ? 'English' : 'English (Bahasa Inggris)' ?></option>
                                    </select>
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-admin-primary px-4" <?= !$is_db_online ? 'disabled' : '' ?>><?= __('btn_save_prefs') ?></button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function confirmLogout(event) {
    event.preventDefault();
    if (confirm('Apakah Anda yakin ingin keluar?')) {
        sessionStorage.clear();
        localStorage.clear();
        window.location.href = 'logout.php';
    }
}

// Function to toggle password inputs visibility
function togglePasswordVisibility(icon) {
    const input = $(icon).siblings('.password-input');
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        $(icon).removeClass('bi-eye').addClass('bi-eye-slash');
    } else {
        input.attr('type', 'password');
        $(icon).removeClass('bi-eye-slash').addClass('bi-eye');
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
                    list.append('<div class="p-3 text-center text-muted" style="font-size: 0.85rem;"><i class="bi bi-bell-slash me-1"></i> Tidak ada notifikasi.</div>');
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
