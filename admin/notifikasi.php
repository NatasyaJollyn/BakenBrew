<?php
// ========================================================
// BAKE'N BREW - Admin Notifications Archive
// ========================================================

session_start();
require_once '../config/koneksi.php';

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
            'lang' => 'id'
        ];
    }
}
require_once 'lang.php';


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

$success = '';
$error = '';

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'read_all') {
    if ($is_db_online) {
        try {
            $pdo->exec("UPDATE `notifications` SET `is_read` = 1");
            $_SESSION['success_msg'] = "Semua notifikasi ditandai telah dibaca.";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Gagal memperbarui database: " . $e->getMessage();
        }
    } else {
        $_SESSION['success_msg'] = "Semua notifikasi ditandai telah dibaca (Mode Offline).";
    }
    header('Location: notifikasi.php');
    exit;
}

if ($action === 'clear_all') {
    if ($is_db_online) {
        try {
            $pdo->exec("DELETE FROM `notifications`");
            $_SESSION['success_msg'] = "Arsip notifikasi berhasil dibersihkan.";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Gagal membersihkan database: " . $e->getMessage();
        }
    } else {
        $_SESSION['success_msg'] = "Arsip notifikasi dibersihkan (Mode Offline).";
    }
    header('Location: notifikasi.php');
    exit;
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

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

if ($is_db_online) {
    // Total count
    $total_records = (int)$pdo->query("SELECT COUNT(*) FROM `notifications`")->fetchColumn();
    $total_pages = ceil($total_records / $limit);
    
    // Fetch notifications
    $stmt = $pdo->prepare("SELECT * FROM `notifications` ORDER BY `created_at` DESC LIMIT ? OFFSET ?");
    $stmt->execute([$limit, $offset]);
    $notifications = $stmt->fetchAll();
} else {
    // Mock notifications fallback
    $mock_notifs = isset($mock_data['notifications']) ? $mock_data['notifications'] : [];
    usort($mock_notifs, function($a, $b) {
        return $b['id'] - $a['id'];
    });
    
    $total_records = count($mock_notifs);
    $total_pages = ceil($total_records / $limit);
    $notifications = array_slice($mock_notifs, $offset, $limit);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= __('notif_archive_title') ?> – Bake'n Brew</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'></text></svg>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="admin-style.css?v=5.2" />
    <style>
        .notif-item {
            transition: all 0.3s ease;
            border-bottom: 1px solid var(--cream-dark);
        }
        .notif-item:hover {
            background-color: #FAF8F5;
        }
        .notif-item.unread {
            background-color: #FFFDF4;
        }
        .notif-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .notif-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #2f80ed;
            display: inline-block;
            margin-left: 8px;
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
        <h2><?= __('notif_archive_title') ?></h2>
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

        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <h4 class="m-0"><?= __('notif_all') ?></h4>
                <div class="d-flex gap-2">
                    <a href="notifikasi.php?action=read_all" class="btn btn-sm btn-admin-outline" style="font-size: 0.82rem;"><i class="bi bi-check2-all me-1"></i> <?= __('mark_all_read') ?></a>
                    <a href="notifikasi.php?action=clear_all" class="btn btn-sm btn-admin-danger" style="font-size: 0.82rem;" onclick="return confirm('<?= __('confirm_clear_all') ?>')"><i class="bi bi-trash me-1"></i> <?= __('btn_clear_all') ?></a>
                </div>
            </div>

            <div class="notif-list-container border rounded-3 overflow-hidden">
                <?php if (count($notifications) > 0): ?>
                    <?php foreach ($notifications as $n): 
                        $icon_class = 'bi-bell-fill bg-light text-secondary';
                        if ($n['type'] === 'new_order') {
                            $icon_class = 'bi-cart-plus-fill text-success';
                        } elseif ($n['type'] === 'low_stock') {
                            $icon_class = 'bi-exclamation-triangle-fill text-warning';
                        } elseif ($n['type'] === 'cancelled_order') {
                            $icon_class = 'bi-x-circle-fill text-danger';
                        }
                        
                        $is_unread = (isset($n['is_read']) && $n['is_read'] == 0);
                        $bg_style = $is_unread ? 'background-color: #FFFDF4;' : 'background-color: #ffffff;';
                        $text_style = $is_unread ? 'font-weight: 600; color: var(--text-dark);' : 'font-weight: normal; color: var(--text-mid);';
                        $link_url = !empty($n['link']) ? $n['link'] : '#';
                    ?>
                        <div class="notif-item p-3 d-flex align-items-center gap-3 <?= $is_unread ? 'unread' : '' ?>" id="notif-row-<?= $n['id'] ?>" style="<?= $bg_style ?>">
                             <div class="notif-icon-box shadow-sm" style="background-color: #ffffff; border: 1px solid var(--cream-dark);">
                                <i class="bi <?= $icon_class ?>"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start flex-wrap">
                                    <h6 class="m-0 mb-1" style="<?= $text_style ?>">
                                        <?= htmlspecialchars($n['title']) ?>
                                        <?php if ($is_unread): ?>
                                            <span class="notif-dot"></span>
                                        <?php endif; ?>
                                    </h6>
                                    <span style="font-size: 0.76rem; color: var(--text-mid);"><?= getRelativeTime($n['created_at']) ?></span>
                                </div>
                                <p class="mb-0" style="font-size: 0.85rem; color: var(--text-mid);"><?= htmlspecialchars($n['message']) ?></p>
                                <?php if (!empty($n['link'])): ?>
                                    <a href="<?= $link_url ?>" onclick="markNotificationRead(<?= $n['id'] ?>)" class="text-decoration-none d-inline-flex align-items-center gap-1 mt-1 fw-medium" style="font-size: 0.8rem; color: var(--accent-gold);">
                                        <?= __('lbl_view_detail') ?> <i class="bi bi-chevron-right" style="font-size: 0.7rem;"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-bell-slash" style="font-size: 2.5rem; display: block; margin-bottom: 1rem;"></i>
                        <?= __('empty_notif_archive') ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- PAGINATION -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination-admin mt-4">
                    <?php if ($page > 1): ?>
                        <a href="notifikasi.php?page=<?= $page - 1 ?>"><i class="bi bi-chevron-left"></i></a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="notifikasi.php?page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="notifikasi.php?page=<?= $page + 1 ?>"><i class="bi bi-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        method: 'GET',
        success: function() {
            // Read success, row background transition will happen before redirection
        }
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
                const badgeHeader = $('#notifBadgeHeader');
                
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
                    // If on the archive page, reload list to show read status
                    if (window.location.pathname.includes('notifikasi.php')) {
                        setTimeout(() => { window.location.reload(); }, 150);
                    }
                }
            }
        });
    });
});
</script>

</body>
</html>
