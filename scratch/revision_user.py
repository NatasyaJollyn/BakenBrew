import re
import os

cwd = r"d:\KULIAH\TUGAS\SEMESTER 4\PEMROGRAMAN WEB\EAS PEMWEB"

files_to_update = [
    'index.php',
    'about.php',
    'product.php',
    'gallery.php',
    'contact.php',
    'form.php'
]

# 1. Update each file
for filename in files_to_update:
    filepath = os.path.join(cwd, filename)
    if not os.path.exists(filepath):
        print(f"File not found: {filepath}")
        continue
        
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
        
    # A. Inject PHP connection and store status at the very top (if not already there)
    # Note: product.php and form.php already have <?php require_once 'config/koneksi.php'; ... ?>
    # We should make sure they fetch $store_status.
    php_koneksi_str = """<?php
require_once 'config/koneksi.php';
// Get store status
$status_stmt = $pdo->query("SELECT `setting_value` FROM `settings` WHERE `setting_key` = 'store_status'");
$store_status = $status_stmt->fetchColumn() ?: 'open';
?>
"""
    if filename in ['product.php', 'form.php']:
        # If it already has connection loading, we just ensure $store_status is fetched
        if '$store_status' not in content:
            # Inject store_status retrieval after connecting to database
            # We can find $pdo and insert it right after
            content = content.replace("require_once 'config/koneksi.php';", "require_once 'config/koneksi.php';\n// Get store status\n$status_stmt = $pdo->query(\"SELECT `setting_value` FROM `settings` WHERE `setting_key` = 'store_status'\");\n$store_status = $status_stmt->fetchColumn() ?: 'open';")
    else:
        # For other files, inject the connection and status logic at the very top
        if not content.startswith("<?php"):
            content = php_koneksi_str + content
            
    # B. Replace Navbar Brand to include the dynamic Buka/Tutup badge
    navbar_brand_regex = r'<a class="navbar-brand" href="index.php">.*?Bake\'n <span>Brew</span>\s*</a>'
    dynamic_navbar_brand = """<a class="navbar-brand" href="index.php">
     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="me-2" style="width: 28px; height: 28px; vertical-align: middle; color: var(--accent-gold);">
       <path d="M17 8h1a4 4 0 1 1 0 8h-1" />
       <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8z" />
       <line x1="6" y1="2" x2="6" y2="4" />
       <line x1="10" y1="2" x2="10" y2="4" />
       <line x1="14" y1="2" x2="14" y2="4" />
     </svg>Bake'n <span>Brew</span>
     <?php if ($store_status === 'open'): ?>
      <span class="badge bg-success ms-2" style="font-size: 0.65rem; vertical-align: middle; font-family: 'Poppins', sans-serif;">Buka</span>
     <?php else: ?>
      <span class="badge bg-danger ms-2" style="font-size: 0.65rem; vertical-align: middle; font-family: 'Poppins', sans-serif;">Tutup</span>
     <?php endif; ?>
    </a>"""
    content = re.sub(navbar_brand_regex, dynamic_navbar_brand, content, flags=re.DOTALL)
    
    # C. Specific to index.php: Add closure banner inside Hero section
    if filename == 'index.php':
        hero_badge_str = '<div class="hero-badge"> Café & Bakery Surabaya</div>'
        hero_closure_banner = """<?php if ($store_status === 'closed'): ?>
   <div class="alert alert-danger d-inline-flex align-items-center gap-2 mb-3" style="border-radius: var(--radius-sm); font-size: 0.85rem; background-color: rgba(220, 53, 69, 0.15); border: 1px solid rgba(220, 53, 69, 0.25); color: #FAF6F0; padding: 0.5rem 1rem; font-family: 'Poppins', sans-serif;">
    <i class="bi bi-shop"></i> Kami sedang Tutup. Pemesanan dinonaktifkan sementara.
   </div>
   <?php endif; ?>
   <div class="hero-badge"> Café & Bakery Surabaya</div>"""
        if 'Kami sedang Tutup' not in content:
            content = content.replace(hero_badge_str, hero_closure_banner)
            
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Successfully updated user-facing page: {filename}")
