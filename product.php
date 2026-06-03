<?php
require_once 'config/koneksi.php';
// Get store status
$status_stmt = $pdo->query("SELECT `setting_value` FROM `settings` WHERE `setting_key` = 'store_status'");
$store_status = $status_stmt->fetchColumn() ?: 'open';

try {
    $stmt = $pdo->query("SELECT * FROM `products` ORDER BY `id` ASC");
    $all_products = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Gagal memuat produk: " . $e->getMessage());
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
 <link rel="stylesheet" href="css/style.css?v=3.0" />
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
      <span class="badge bg-success ms-2" style="font-size: 0.65rem; vertical-align: middle; font-family: 'Poppins', sans-serif;">Buka</span>
     <?php else: ?>
      <span class="badge bg-danger ms-2" style="font-size: 0.65rem; vertical-align: middle; font-family: 'Poppins', sans-serif;">Tutup</span>
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
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Croissant Butter</h5>
      <p class="card-text flex-grow-1">Croissant berlapis mentega premium, renyah di luar, lembut di dalam. Dipanggang fresh setiap pagi.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 22.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Almond Croissant -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-4 product-item" data-category="bakery">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1625425404751-19b16c027511?q=80&w=735&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp"
         class="card-img-top" alt="Almond Croissant" loading="lazy" />
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Almond Croissant</h5>
      <p class="card-text flex-grow-1">Croissant berlapis dengan tambahan almond yang renyah, memberikan tekstur dan rasa yang khas. Dipanggang fresh setiap pagi.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 25.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Srawberry Croissant -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-4 product-item" data-category="bakery">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1721324412655-63d4885d9e67?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp"
         class="card-img-top" alt="Strawberry Croissant" loading="lazy" />
      <div style="position:absolute;top:10px;left:10px;">
       <span class="badge-new">New</span>
      </div>
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Strawberry Croissant</h5>
      <p class="card-text flex-grow-1">Croissant berlapis dengan tambahan strawberry yang lezat, memberikan tekstur dan rasa yang khas. Dipanggang fresh setiap pagi.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 28.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Salt Bread -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-4 product-item" data-category="bakery">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1700284923285-90d6fe468920?q=80&w=721&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp"
         class="card-img-top" alt="Salt Bread" loading="lazy" />
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Salt Bread</h5>
      <p class="card-text flex-grow-1">Roti yang lembut dan gurih, cocok untuk sarapan atau camilan. Dipanggang fresh setiap pagi.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 25.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Srawberry Danish -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-4 product-item" data-category="bakery">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1720091382934-fc9fdff94857?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp"
         class="card-img-top" alt="Strawberry Danish" loading="lazy" />
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Strawberry Danish</h5>
      <p class="card-text flex-grow-1">Danish berlapis dengan tambahan strawberry yang lezat, memberikan tekstur dan rasa yang khas. Dipanggang fresh setiap pagi.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 28.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Donut -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-2 product-item" data-category="bakery" id="donut-glazed">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1585459441171-70a603cd5e46?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp"
         class="card-img-top" alt="Donut Glazed" loading="lazy" />
      <div style="position:absolute;top:10px;left:10px;">
       <span class="badge-bestseller">Best Seller</span>
      </div>
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Donut Glazed</h5>
      <p class="card-text flex-grow-1">Donut empuk dengan glazing gula mengkilap. Tersedia rasa: original, coklat, stroberi, dan matcha.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 15.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Roti Coklat -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-3 product-item" data-category="bakery" id="roti-coklat">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80&fm=webp"
         class="card-img-top" alt="Roti Coklat" loading="lazy" />
      <div style="position:absolute;top:10px;left:10px;">
       <span class="badge-new">New</span>
      </div>
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Roti Coklat</h5>
      <p class="card-text flex-grow-1">Roti lembut isi coklat premium. Lumer di dalam saat dimakan hangat, cocok untuk sarapan atau camilan sore.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 18.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Cinnamon Roll -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-4 product-item" data-category="bakery">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://plus.unsplash.com/premium_photo-1722002219049-1c41e1a034c8?q=80&w=688&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
         class="card-img-top" alt="Cinnamon Roll" loading="lazy" />
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Cinnamon Roll</h5>
      <p class="card-text flex-grow-1">Gulungan roti hangat dengan isian kayu manis dan krim keju yang manis. Aroma harumnya mengisi seluruh kafe.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 25.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Banana Bread -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-1 product-item" data-category="bakery">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1596241913027-34358037e159?q=80&w=1025&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp"
         class="card-img-top" alt="Banana Bread" loading="lazy" />
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Banana Bread</h5>
      <p class="card-text flex-grow-1">Roti pisang lembut yang dipanggang sempurna. Dibuat dari pisang kepok matang pilihan, tanpa pengawet.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 20.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Red Velvet Cake -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-1 product-item" data-category="bakery">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1578937014788-b8318dc042a1?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp"
         class="card-img-top" alt="Red Velvet Cake" loading="lazy" />
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Red Velvet Cake</h5>
      <p class="card-text flex-grow-1">Kue red velvet lembut dengan rasa khas dan tampilan yang menarik. Dibuat dengan bahan berkualitas tinggi.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 35.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Cheesecake -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-1 product-item" data-category="bakery">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1695088957420-c3b97d1f1138?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp"
         class="card-img-top" alt="Cheesecake" loading="lazy" />
      <div style="position:absolute;top:10px;left:10px;">
       <span class="badge-bestseller">Best Seller</span>
      </div>
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Cheesecake</h5>
      <p class="card-text flex-grow-1">Kue keju lembut dengan rasa khas dan tampilan yang menarik. Dibuat dengan bahan berkualitas tinggi.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 32.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Cheese Bun -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-2 product-item" data-category="bakery">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://plus.unsplash.com/premium_photo-1693086421089-847b0a2724f8?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
         class="card-img-top" alt="Cheese Bun" loading="lazy" />
      <div style="position:absolute;top:10px;left:10px;">
       <span class="badge-bestseller">Best Seller</span>
      </div>
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Cheese Bun</h5>
      <p class="card-text flex-grow-1">Roti fluffy dengan topping keju cheddar meleleh dan taburan wijen. Favorit pelanggan semua usia.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 17.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

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
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Signature Latte</h5>
      <p class="card-text flex-grow-1">Espresso double shot dengan susu full cream yang di-steam sempurna. Creamy, smooth, dan selalu memuaskan.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 28.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Cappuccino -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-2 product-item" data-category="coffee" id="cappuccino">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400&q=80&fm=webp"
         class="card-img-top" alt="Cappuccino" loading="lazy" />
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Cappuccino</h5>
      <p class="card-text flex-grow-1">Cappuccino klasik dengan busa susu tebal dan rasa espresso yang kaya. Disajikan dengan taburan bubuk coklat.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 26.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Cold Brew -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-3 product-item" data-category="coffee">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&q=80&fm=webp"
         class="card-img-top" alt="Cold Brew" loading="lazy" />
      <div style="position:absolute;top:10px;left:10px;">
       <span class="badge-new">New</span>
      </div>
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Cold Brew</h5>
      <p class="card-text flex-grow-1">Kopi diseduh dingin selama 18 jam untuk menghasilkan rasa yang halus, kaya, dan rendah asam. Segar dan bold.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 32.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Espresso -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-4 product-item" data-category="coffee">
    <div class="product-card card h-100">
     <div style="overflow:hidden;">
      <img src="https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=400&q=80&fm=webp"
         class="card-img-top" alt="Espresso" loading="lazy" />
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Espresso</h5>
      <p class="card-text flex-grow-1">Satu shot espresso pekat dari biji kopi arabika Flores single origin. Untuk yang suka rasa kopi yang pure dan autentik.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 22.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Americano -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-2 product-item" data-category="coffee">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1531835207745-506a1bc035d8?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp"
         class="card-img-top" alt="Americano" loading="lazy" />
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Americano</h5>
      <p class="card-text flex-grow-1">Americano klasik dengan rasa espresso yang kaya dan aroma yang kuat. Disajikan dengan es.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 24.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Iced Caramel Latte -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-3 product-item" data-category="coffee">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1527678357412-ef45dfbd9ecc?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp"
         class="card-img-top" alt="Iced Caramel Latte" loading="lazy" />
      <div style="position:absolute;top:10px;left:10px;">
       <span class="badge-new">New</span>
      </div>
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Iced Caramel Latte</h5>
      <p class="card-text flex-grow-1">Latte karamel dingin dengan rasa manis dan karamel yang kaya. Disajikan dengan es dan taburan bubuk coklat.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 35.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

   <!-- Iced Hazelnut Coffee -->
   <div class="col-sm-6 col-lg-3 fade-in-up delay-3 product-item" data-category="coffee">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1584286595398-a59f21d313f5?q=80&w=735&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp"
         class="card-img-top" alt="Iced Hazelnut Coffee" loading="lazy" />
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title">Iced Hazelnut Coffee</h5>
      <p class="card-text flex-grow-1">Kopi dingin dengan rasa hazelnut yang kaya dan lembut. Disajikan dengan es dan taburan bubuk coklat.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 32.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
      </div>
     </div>
    </div>
   </div>

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
<script src="js/script.js"></script>
</body>
</html>
