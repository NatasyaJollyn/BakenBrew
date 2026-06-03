<?php
require_once 'config/koneksi.php';
// Get store status safely
$store_status = 'open';
if ($is_db_online && $pdo) {
    try {
        $status_stmt = $pdo->query("SELECT `setting_value` FROM `settings` WHERE `setting_key` = 'store_status'");
        $store_status = $status_stmt->fetchColumn() ?: 'open';
    } catch (PDOException $e) {
        $store_status = 'open';
    }
} else {
    $store_status = $mock_data['settings']['store_status'] ?? 'open';
}

$all_products = [];
if ($is_db_online && $pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM `products` ORDER BY `id` ASC");
        $all_products = $stmt->fetchAll();
    } catch (PDOException $e) {
        $all_products = $mock_data['products'] ?? [];
    }
} else {
    $all_products = $mock_data['products'] ?? [];
}

$bakery_products = [];
$coffee_products = [];
$non_coffee_products = [];

foreach ($all_products as $p) {
    if ($p['category'] === 'bakery') $bakery_products[] = $p;
    elseif ($p['category'] === 'coffee') $coffee_products[] = $p;
    elseif ($p['category'] === 'non-coffee') $non_coffee_products[] = $p;
}

function renderProductCard($p, $delayIndex) {
    $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $p['name'])));
    
    $img_src = htmlspecialchars($p['image']);
    if (!empty($p['image']) && !str_starts_with($p['image'], 'http')) {
        $img_src = 'public/images/products/' . htmlspecialchars($p['image']);
    }
    
    $price_formatted = 'Rp ' . number_format($p['price'], 0, ',', '.');
    
    $badge_html = '';
    if ($p['is_bestseller']) {
        $badge_html .= '<span class="badge-bestseller">Best Seller</span>';
    }
    if ($p['is_new']) {
        $badge_html .= '<span class="badge-new">New</span>';
    }
    
    $badge_container = '';
    if (!empty($badge_html)) {
        $badge_container = '<div style="position:absolute;top:10px;left:10px;display:flex;gap:5px;flex-wrap:wrap;">' . $badge_html . '</div>';
    }
    
    $delay_class = 'delay-' . (($delayIndex % 4) + 1);
    $category_attr = ($p['category'] === 'non-coffee') ? 'noncoffee' : htmlspecialchars($p['category']);

    echo '
    <div class="col-sm-6 col-lg-3 fade-in-up ' . $delay_class . ' product-item" data-category="' . $category_attr . '" id="' . $slug . '">
     <div class="product-card card h-100">
      <div style="overflow:hidden;position:relative;">
       <img src="' . $img_src . '" class="card-img-top" alt="' . htmlspecialchars($p['name']) . '" loading="lazy" />
       ' . $badge_container . '
      </div>
      <div class="card-body d-flex flex-column">
       <h5 class="card-title">' . htmlspecialchars($p['name']) . '</h5>
       <p class="card-text flex-grow-1">' . htmlspecialchars($p['description']) . '</p>
       <div class="d-flex justify-content-between align-items-center mt-3">
        <span class="price">' . $price_formatted . '</span>
        <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
       </div>
      </div>
     </div>
    </div>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8" />
 <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 <title>Produk – Bake'n Brew</title>
 <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'></text></svg>" />
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
 <link rel="stylesheet" href="css/style.css?v=5.2" />
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
 <div class="container">
   <a class="navbar-brand" href="index.php">
     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="me-2" style="width: 28px; height: 28px; vertical-align: middle; color: var(--accent-gold);">
       <path d="M17 8h1a4 4 0 1 1 0 8h-1" />
       <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8z" />
       <line x1="6" y1="2" x2="6" y2="4" />
       <line x1="10" y1="2" x2="10" y2="4" />
       <line x1="14" y1="2" x2="14" y2="4" />
     </svg>Bake'n <span>Brew</span>
     <?php if ($store_status === 'open'): ?>
      <span class="badge bg-success ms-2 badge-flip-active" style="font-size: 0.65rem; vertical-align: middle; font-family: 'Poppins', sans-serif;">Buka</span>
     <?php else: ?>
      <span class="badge bg-danger ms-2 badge-flip-active" style="font-size: 0.65rem; vertical-align: middle; font-family: 'Poppins', sans-serif;">Tutup</span>
     <?php endif; ?>
    </a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
   <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarMain">
   <ul class="navbar-nav ms-auto gap-1">
    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
    <li class="nav-item"><a class="nav-link" href="about.php">Tentang</a></li>
    <li class="nav-item"><a class="nav-link" href="product.php">Produk</a></li>
    <li class="nav-item"><a class="nav-link" href="gallery.php">Galeri</a></li>
    <li class="nav-item"><a class="nav-link" href="contact.php">Kontak</a></li>
    <li class="nav-item"><a class="nav-link" href="form.php">Order</a></li>
   </ul>
  </div>
 </div>
</nav>

<!-- PAGE HERO -->
<section class="page-hero">
 <div class="container position-relative" style="z-index:2">
  <nav aria-label="breadcrumb">
   <ol class="breadcrumb justify-content-center mb-3" style="font-size:.82rem">
    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
    <li class="breadcrumb-item active">Produk</li>
   </ol>
  </nav>
  <h1>Menu Kami</h1>
  <p>Pilihan roti segar dan kopi premium yang kami hadirkan setiap hari dengan penuh cinta.</p>
 </div>
</section>

<!-- PRODUCT SECTION -->
<section class="product-section">
 <div class="container">

  <?php if (!$is_db_online): ?>
   <div class="alert d-flex align-items-center gap-3 mb-4 shadow-sm" role="alert" style="background: linear-gradient(135deg, #FFF3CD, #FFEBAA); border: 1px solid #FFE082; color: #856404; border-radius: var(--radius-md); padding: 1rem 1.5rem; font-family: 'Poppins', sans-serif;">
    <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.4rem; color: #E65100;"></i>
    <div>
     <h6 class="fw-bold mb-1" style="margin: 0; color: #E65100;">Koneksi Database Offline...</h6>
     <p class="mb-0" style="font-size: 0.82rem; margin: 0; opacity: 0.9;">Sistem saat ini menggunakan cadangan data produk statis lokal.</p>
    </div>
   </div>
  <?php endif; ?>

  <!-- Filter Buttons -->
  <div class="text-center mb-5 fade-in-up">
   <div class="d-flex flex-wrap gap-2 justify-content-center">
    <button class="filter-btn active" data-filter="all"> Semua</button>
    <button class="filter-btn" data-filter="bakery"> Bakery</button>
    <button class="filter-btn" data-filter="coffee"> Coffee</button>
    <button class="filter-btn" data-filter="noncoffee"> Non-Coffee</button>
   </div>
  </div>

  <!-- === BAKERY === -->
  <div class="mb-4 fade-in-up" id="bakery">
   <h3 style="font-family:'Playfair Display',serif;color:var(--brown-dark);border-bottom:2px solid var(--beige);padding-bottom:.6rem;margin-bottom:1.5rem;">
     Bakery
   </h3>
  </div>
  <div class="row g-4 mb-5">
   <?php 
   if (count($bakery_products) > 0) {
       foreach ($bakery_products as $idx => $p) {
           renderProductCard($p, $idx);
       }
   } else {
       echo '<div class="col-12 text-center text-muted py-4">Tidak ada menu bakery tersedia.</div>';
   }
   ?>
  </div>

    <!-- === COFFEE === -->
  <div class="mb-4 fade-in-up" id="coffee">
   <h3 style="font-family:'Playfair Display',serif;color:var(--brown-dark);border-bottom:2px solid var(--beige);padding-bottom:.6rem;margin-bottom:1.5rem;">
     Coffee
   </h3>
  </div>
  <div class="row g-4 mb-5">
   <?php 
   if (count($coffee_products) > 0) {
       foreach ($coffee_products as $idx => $p) {
           renderProductCard($p, $idx);
       }
   } else {
       echo '<div class="col-12 text-center text-muted py-4">Tidak ada menu kopi tersedia.</div>';
   }
   ?>
  </div>

    <!-- === NON-COFFEE === -->
  <div class="mb-4 fade-in-up" id="non-coffee">
   <h3 style="font-family:'Playfair Display',serif;color:var(--brown-dark);border-bottom:2px solid var(--beige);padding-bottom:.6rem;margin-bottom:1.5rem;">
     Non-Coffee
   </h3>
  </div>
  <div class="row g-4">
   <?php 
   if (count($non_coffee_products) > 0) {
       foreach ($non_coffee_products as $idx => $p) {
           renderProductCard($p, $idx);
       }
   } else {
       echo '<div class="col-12 text-center text-muted py-4">Tidak ada menu non-kopi tersedia.</div>';
   }
   ?>
  </div>

  <!-- CTA -->
  <div class="text-center mt-5 fade-in-up" style="padding:3rem;background:linear-gradient(135deg,var(--cream),var(--beige-light));border-radius:var(--radius-lg);">
   <h3 style="color:var(--brown-dark);margin-bottom:.8rem;">Sudah menemukan menu favoritmu?</h3>
   <p style="color:var(--text-mid);margin-bottom:1.5rem;font-size:.95rem;">Pesan sekarang dan nikmati langsung di kafe kami atau melalui layanan take away.</p>
   <a href="form.php" class="btn-primary-brown">Pesan Sekarang →</a>
  </div>

 </div>
</section>

<!-- FOOTER -->
<footer class="footer">
 <div class="container">
  <div class="row g-4">
   <div class="col-lg-4 col-md-6">
    <div class="footer-brand">
     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="me-2" style="width: 26px; height: 26px; vertical-align: middle; color: var(--cream);">
       <path d="M17 8h1a4 4 0 1 1 0 8h-1" />
       <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8z" />
       <line x1="6" y1="2" x2="6" y2="4" />
       <line x1="10" y1="2" x2="10" y2="4" />
       <line x1="14" y1="2" x2="14" y2="4" />
     </svg>Bake'n <span>Brew</span>
    </div>
    <p>Café & Bakery di Surabaya yang menghadirkan roti segar dan kopi berkualitas dalam suasana hangat dan cozy sejak 2019.</p>
    <div class="footer-social">
     <a href="https://www.instagram.com/natasyajollyn/" target="_blank" rel="noopener noreferrer"><i class="bi bi-instagram"></i></a>
     <a href="https://wa.me/6282335871770" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp"></i></a>
     <a href="https://www.tiktok.com/@lynnatzz?_r=1&_t=ZS-96sLinjnNw1" target="_blank" rel="noopener noreferrer"><i class="bi bi-tiktok"></i></a>
    </div>
   </div>
   <div class="col-lg-2 col-md-6">
    <h6>Navigasi</h6>
    <ul class="footer-links">
     <li><a href="index.php">Home</a></li>
     <li><a href="about.php">Tentang Kami</a></li>
     <li><a href="product.php">Produk</a></li>
     <li><a href="gallery.php">Galeri</a></li>
     <li><a href="contact.php">Kontak</a></li>
    </ul>
   </div>
   <div class="col-lg-3 col-md-6">
    <h6>Kategori Menu</h6>
    <ul class="footer-links">
     <li><a href="#bakery"> Bakery</a></li>
     <li><a href="#coffee"> Coffee</a></li>
     <li><a href="#non-coffee"> Non-Coffee</a></li>
    </ul>
   </div>
   <div class="col-lg-3 col-md-6">
    <h6>Jam Operasional</h6>
    <p style="font-size:.85rem;">
     <span style="color:var(--cream)">Senin – Jumat</span><br>07.00 – 21.00 WIB<br><br>
     <span style="color:var(--cream)">Sabtu – Minggu</span><br>08.00 – 22.00 WIB
    </p>
   </div>
  </div>
 </div>
 <div class="footer-bottom">
  <div class="container"><p>© 2024 Bake'n Brew. Dibuat dengan cinta di Surabaya. All rights reserved.</p></div>
 </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js?v=5.2"></script>
</body>
</html>
