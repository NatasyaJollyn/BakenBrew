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
?>
<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8" />
 <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 <title>Galeri – Bake'n Brew</title>
 <link rel="icon" type="image/png" href="public/images/logo.png" />
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
    <li class="breadcrumb-item active">Galeri</li>
   </ol>
  </nav>
  <h1>Galeri</h1>
  <p>Sekilas momen dan sajian terbaik dari Bake'n Brew yang abadi dalam gambar.</p>
 </div>
</section>

<!-- GALLERY SECTION -->
<section class="gallery-section">
 <div class="container">
  <div class="text-center mb-5 fade-in-up">
   <p class="text-brown fw-semibold mb-1" style="font-size:.85rem;letter-spacing:2px;text-transform:uppercase;">Visual Kami</p>
   <h2 class="section-title">Momen di Bake'n Brew</h2>
   <div class="divider"></div>
   <p class="section-subtitle">Dari produk unggulan hingga suasana kafe yang hangat, semuanya ada di sini. Klik gambar untuk memperbesar.</p>
  </div>

  <div class="gallery-marquee-container fade-in-up">
   <div class="gallery-marquee-track">
    <!-- Track 1 (Original 9 Items) -->
    <div class="gallery-item" data-caption="Croissant Butter Freshly Baked">
     <img src="https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=600&q=80&fm=webp" alt="Croissant Bake'n Brew" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Croissant Butter</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Suasana Cozy Café Bake'n Brew">
     <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=600&q=80&fm=webp" alt="Interior Café" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Interior Café</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Signature Latte dengan Latte Art">
     <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=600&q=80&fm=webp" alt="Signature Latte" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Signature Latte</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Donut Glazed Warna-warni">
     <img src="https://images.unsplash.com/photo-1585459441171-70a603cd5e46?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp" alt="Donut Glazed" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Donut Glazed</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Area Duduk yang Cozy dan Nyaman">
     <img src="https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?w=600&q=80&fm=webp" alt="Area Duduk Café" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Area Duduk Café</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Cappuccino Premium">
     <img src="https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=600&q=80&fm=webp" alt="Cappuccino" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Cappuccino Premium</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Proses Memanggang Roti Segar">
     <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&q=80&fm=webp" alt="Proses Baking" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Proses Baking</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Matcha Latte Creamy">
     <img src="https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=600&q=80&fm=webp" alt="Matcha Latte" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Matcha Latte</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Cinnamon Roll Hangat">
     <img src="https://plus.unsplash.com/premium_photo-1722002219049-1c41e1a034c8?q=80&w=688&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Cinnamon Roll" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Cinnamon Roll</p>
     </div>
    </div>

    <!-- Track 2 (Duplicate 9 Items for Seamless Loop) -->
    <div class="gallery-item" data-caption="Croissant Butter Freshly Baked">
     <img src="https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=600&q=80&fm=webp" alt="Croissant Bake'n Brew" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Croissant Butter</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Suasana Cozy Café Bake'n Brew">
     <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=600&q=80&fm=webp" alt="Interior Café" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Interior Café</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Signature Latte dengan Latte Art">
     <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=600&q=80&fm=webp" alt="Signature Latte" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Signature Latte</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Donut Glazed Warna-warni">
     <img src="https://images.unsplash.com/photo-1585459441171-70a603cd5e46?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp" alt="Donut Glazed" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Donut Glazed</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Area Duduk yang Cozy dan Nyaman">
     <img src="https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?w=600&q=80&fm=webp" alt="Area Duduk Café" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Area Duduk Café</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Cappuccino Premium">
     <img src="https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=600&q=80&fm=webp" alt="Cappuccino" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Cappuccino Premium</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Proses Memanggang Roti Segar">
     <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&q=80&fm=webp" alt="Proses Baking" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Proses Baking</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Matcha Latte Creamy">
     <img src="https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=600&q=80&fm=webp" alt="Matcha Latte" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Matcha Latte</p>
     </div>
    </div>
    <div class="gallery-item" data-caption="Cinnamon Roll Hangat">
     <img src="https://plus.unsplash.com/premium_photo-1722002219049-1c41e1a034c8?q=80&w=688&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Cinnamon Roll" loading="lazy" />
     <div class="gallery-overlay">
      <div class="icon"><i class="bi bi-zoom-in" style="font-size:1.5rem;color:var(--accent-gold);"></i></div>
      <p>Cinnamon Roll</p>
     </div>
    </div>
   </div>
  </div><!-- end gallery-grid -->
 </div>
</section>

<!-- LIGHTBOX -->
<div class="lightbox-overlay" id="lightboxOverlay">
 <span class="lightbox-close" id="lightboxClose">&times;</span>
 <div style="text-align:center;">
  <img class="lightbox-img" id="lightboxImg" src="" alt="Gallery" loading="lazy" />
  <p id="lightboxCaption" style="color:rgba(255,255,255,0.7);margin-top:1rem;font-size:.9rem;font-family:'Playfair Display',serif;"></p>
 </div>
</div>

<!-- CTA -->
<section style="padding:4rem 0;background:linear-gradient(135deg,var(--cream),var(--beige-light));">
 <div class="container text-center fade-in-up">
  <h2 class="section-title mb-3">Ingin Merasakan Sendiri?</h2>
  <p style="color:var(--text-mid);margin-bottom:1.8rem;max-width:420px;margin-left:auto;margin-right:auto;">
   Kunjungi kami di Jl. Raya Darmo No. 88, Surabaya atau pesan melalui form online kami.
  </p>
  <div class="d-flex gap-3 justify-content-center flex-wrap">
   <a href="form.php" class="btn-primary-brown">Order Sekarang</a>
   <a href="contact.php" class="btn-outline-brown">Temukan Kami</a>
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
    <h6>Menu Populer</h6>
    <ul class="footer-links">
     <li><a href="product.php#croissant-butter">Croissant Butter</a></li>
     <li><a href="product.php#signature-latte">Signature Latte</a></li>
     <li><a href="product.php#donut-glazed">Donut Glazed</a></li>
     <li><a href="product.php#cappuccino">Cappuccino</a></li>
     <li><a href="product.php#roti-coklat">Roti Coklat</a></li>
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
