import re
import os

cwd = r"d:\KULIAH\TUGAS\SEMESTER 4\PEMROGRAMAN WEB\EAS PEMWEB"

# 1. Update admin/dashboard.php
dashboard_path = os.path.join(cwd, "admin", "dashboard.php")
if os.path.exists(dashboard_path):
    with open(dashboard_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # A. Remove Popular Menu query
    # From: // Most Popular Menu ... $popular_menu = $fallback_popular ? $fallback_popular : 'Belum Ada';
    # To: empty
    query_pattern = r'// Most Popular Menu.*?\n\s*// Recent Orders'
    content = re.sub(query_pattern, '// Recent Orders', content, flags=re.DOTALL)
    
    # B. Update Sidebar logout link to use the premium .logout class
    sidebar_logout_old = r'<div class="sidebar-footer">\s*<a href="logout.php" class="nav-item-admin" style="color: #ff8a80;">\s*<i class="bi bi-box-arrow-left"></i> Logout\s*</a>\s*</div>'
    sidebar_logout_new = """<div class="sidebar-footer">
        <a href="logout.php" class="nav-item-admin logout">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </div>"""
    content = re.sub(sidebar_logout_old, sidebar_logout_new, content)
    
    # C. Update Top Header to include a premium Logout button next to profile
    header_old = r'<header class="top-header">\s*<h2>Dashboard</h2>\s*<div class="admin-profile">\s*<span>Halo, <strong>Admin</strong></span>\s*<div class="admin-avatar">A</div>\s*</div>\s*</header>'
    header_new = """<header class="top-header">
        <h2>Dashboard</h2>
        <div class="d-flex align-items-center gap-3">
            <div class="admin-profile">
                <span>Halo, <strong>Admin</strong></span>
                <div class="admin-avatar">A</div>
            </div>
            <a href="logout.php" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: var(--radius-sm);" title="Logout dari panel admin">
                <i class="bi bi-box-arrow-left"></i> Keluar
            </a>
        </div>
    </header>"""
    content = re.sub(header_old, header_new, content)

    # D. Remove Card 3 (Popular Menu) and change others to col-md-4
    cards_section_old = r'<!-- STATISTICS CARDS -->.*?<!-- PIE CHART \(Chart\.js\) -->'
    # Let's extract and replace the statistics cards block cleanly
    # We want 3 cards instead of 4, each styled with col-md-4
    cards_section_new = """<!-- STATISTICS CARDS -->
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
                                    <input type="checkbox" id="storeSwitch" <?= $store_status === 'open' ? 'checked' : '' ?> onchange="submitToggle()">
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
            <!-- PIE CHART (Chart.js) -->"""
            
    content = re.sub(cards_section_old, cards_section_new, content, flags=re.DOTALL)
    
    with open(dashboard_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Successfully updated admin/dashboard.php")


# 2. Update admin/produk.php
produk_path = os.path.join(cwd, "admin", "produk.php")
if os.path.exists(produk_path):
    with open(produk_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    # Update Sidebar logout link
    sidebar_logout_old = r'<div class="sidebar-footer">\s*<a href="logout.php" class="nav-item-admin" style="color: #ff8a80;">\s*<i class="bi bi-box-arrow-left"></i> Logout\s*</a>\s*</div>'
    sidebar_logout_new = """<div class="sidebar-footer">
        <a href="logout.php" class="nav-item-admin logout">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </div>"""
    content = re.sub(sidebar_logout_old, sidebar_logout_new, content)
    
    # Update Top Header to include a premium Logout button next to profile
    header_old = r'<header class="top-header">\s*<h2>Kelola Menu</h2>\s*<div class="admin-profile">\s*<span>Halo, <strong>Admin</strong></span>\s*<div class="admin-avatar">A</div>\s*</div>\s*</header>'
    header_new = """<header class="top-header">
        <h2>Kelola Menu</h2>
        <div class="d-flex align-items-center gap-3">
            <div class="admin-profile">
                <span>Halo, <strong>Admin</strong></span>
                <div class="admin-avatar">A</div>
            </div>
            <a href="logout.php" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: var(--radius-sm);" title="Logout dari panel admin">
                <i class="bi bi-box-arrow-left"></i> Keluar
            </a>
        </div>
    </header>"""
    content = re.sub(header_old, header_new, content)
    
    with open(produk_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Successfully updated admin/produk.php")


# 3. Update admin/pesanan.php
pesanan_path = os.path.join(cwd, "admin", "pesanan.php")
if os.path.exists(pesanan_path):
    with open(pesanan_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    # Update Sidebar logout link
    sidebar_logout_old = r'<div class="sidebar-footer">\s*<a href="logout.php" class="nav-item-admin" style="color: #ff8a80;">\s*<i class="bi bi-box-arrow-left"></i> Logout\s*</a>\s*</div>'
    sidebar_logout_new = """<div class="sidebar-footer">
        <a href="logout.php" class="nav-item-admin logout">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </div>"""
    content = re.sub(sidebar_logout_old, sidebar_logout_new, content)
    
    # Update Top Header to include a premium Logout button next to profile
    header_old = r'<header class="top-header">\s*<h2>Kelola Pesanan</h2>\s*<div class="admin-profile">\s*<span>Halo, <strong>Admin</strong></span>\s*<div class="admin-avatar">A</div>\s*</div>\s*</header>'
    header_new = """<header class="top-header">
        <h2>Kelola Pesanan</h2>
        <div class="d-flex align-items-center gap-3">
            <div class="admin-profile">
                <span>Halo, <strong>Admin</strong></span>
                <div class="admin-avatar">A</div>
            </div>
            <a href="logout.php" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: var(--radius-sm);" title="Logout dari panel admin">
                <i class="bi bi-box-arrow-left"></i> Keluar
            </a>
        </div>
    </header>"""
    content = re.sub(header_old, header_new, content)
    
    with open(pesanan_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Successfully updated admin/pesanan.php")
