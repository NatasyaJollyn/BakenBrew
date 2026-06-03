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
 <title>Bake'n Brew – Freshly Baked, Perfectly Brewed</title>
 <link rel="icon" type="image/webp" href="public/images/logo.webp?v=2" />
 <!-- Bootstrap CSS -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
 <!-- Bootstrap Icons -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
 <!-- Custom CSS -->
 <link rel="stylesheet" href="css/style.css?v=5.2" />
</head>
<body>

<!-- ======================== NAVBAR ======================== -->
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

<!-- ======================== HERO ======================== -->
<section class="hero-section">
 <div class="container">
  <div class="hero-content col-lg-7">
   <?php if ($store_status === 'closed'): ?>
    <div id="home-closed-banner" class="alert alert-danger d-flex align-items-center gap-2 mb-3" style="border-radius: var(--radius-sm); font-size: 0.85rem; background-color: rgba(220, 53, 69, 0.15); border: 1px solid rgba(220, 53, 69, 0.25); color: #FAF6F0; padding: 0.5rem 1rem; font-family: 'Poppins', sans-serif; width: fit-content;">
     <i class="bi bi-shop"></i> Kami sedang Tutup. Pemesanan dinonaktifkan sementara.
    </div>
   <?php endif; ?>
   <div class="hero-badge"> Café & Bakery Surabaya</div>
   <h1 class="hero-title">Bake'n <span>Brew</span></h1>
   <p class="hero-tagline">Freshly Baked, Perfectly Brewed</p>
   <p style="color:rgba(245,230,211,0.8);font-size:1rem;max-width:500px;margin-bottom:2rem;">
    Nikmati roti yang dipanggang segar setiap hari dan kopi berkualitas premium yang diseduh dengan penuh cinta di satu tempat yang nyaman dan hangat.
   </p>
   <div class="d-flex gap-3 flex-wrap">
    <a href="product.php" class="btn-cream"> Lihat Menu</a>
    <a href="form.php" class="btn-outline-brown" style="color:var(--cream);border-color:var(--cream);">Order Sekarang</a>
   </div>
   <div class="hero-stats">
    <div class="hero-stat">
     <div class="number">5+</div>
     <div class="label">Tahun Berdiri</div>
    </div>
    <div class="hero-stat">
     <div class="number">50+</div>
     <div class="label">Menu Pilihan</div>
    </div>
    <div class="hero-stat">
     <div class="number">2K+</div>
     <div class="label">Pelanggan Puas</div>
    </div>
   </div>
  </div>
 </div>
</section>

<!-- ======================== BEST SELLER ======================== -->
<section class="bestseller-section">
 <div class="container">
  <div class="text-center mb-5 fade-in-up">
   <p class="text-brown fw-semibold mb-1" style="font-size:.85rem;letter-spacing:2px;text-transform:uppercase;">Menu Favorit</p>
   <h2 class="section-title">Best Seller Kami</h2>
   <div class="divider"></div>
   <p class="section-subtitle">Produk-produk terlaris yang paling dicintai pelanggan setia Bake'n Brew.</p>
  </div>
  <div class="row g-4">
   <!-- Card 1 -->
   <div class="col-md-4 fade-in-up delay-1">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=600&q=80&fm=webp"
         class="card-img-top" alt="Croissant Butter" loading="lazy" />
      <div style="position:absolute;top:10px;left:10px;">
       <span class="badge-bestseller">Best Seller</span>
      </div>
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title mb-2">Croissant Butter</h5>
      <p class="card-text flex-grow-1">Croissant berlapis mentega premium dengan tekstur renyah di luar, lembut di dalam. Dipanggang segar setiap pagi.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 22.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.5rem 1.2rem;font-size:.82rem;">Order</a>
      </div>
     </div>
    </div>
   </div>
   <!-- Card 2 -->
   <div class="col-md-4 fade-in-up delay-2">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=600&q=80&fm=webp"
         class="card-img-top" alt="Signature Latte" loading="lazy" />
      <div style="position:absolute;top:10px;left:10px;">
       <span class="badge-bestseller">Best Seller</span>
      </div>
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title mb-2">Signature Latte</h5>
      <p class="card-text flex-grow-1">Espresso double shot dengan susu full cream yang di-steam sempurna — pilihan yang selalu setia menemani harimu.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 28.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.5rem 1.2rem;font-size:.82rem;">Order</a>
      </div>
     </div>
    </div>
   </div>
   <!-- Card 3 -->
   <div class="col-md-4 fade-in-up delay-3">
    <div class="product-card card h-100">
     <div style="overflow:hidden;position:relative;">
      <img src="https://images.unsplash.com/photo-1585459441171-70a603cd5e46?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&fm=webp"
         class="card-img-top" alt="Donut Glazed" loading="lazy" />
      <div style="position:absolute;top:10px;left:10px;">
       <span class="badge-bestseller">Best Seller</span>
      </div>
     </div>
     <div class="card-body d-flex flex-column">
      <h5 class="card-title mb-2">Donut Glazed</h5>
      <p class="card-text flex-grow-1">Donut empuk dengan glazing gula yang mengkilap. Tersedia dalam berbagai rasa: original, coklat, stroberi.</p>
      <div class="d-flex justify-content-between align-items-center mt-3">
       <span class="price">Rp 15.000</span>
       <a href="form.php" class="btn-primary-brown" style="padding:.5rem 1.2rem;font-size:.82rem;">Order</a>
      </div>
     </div>
    </div>
   </div>
  </div>
  <div class="text-center mt-5 fade-in-up">
   <a href="product.php" class="btn-outline-brown">Lihat Semua Menu →</a>
  </div>
 </div>
</section>

<!-- ======================== WHY CHOOSE US ======================== -->
<section class="why-section">
 <div class="container">
  <div class="text-center mb-5 fade-in-up">
   <p class="fw-semibold mb-1" style="font-size:.85rem;letter-spacing:2px;text-transform:uppercase;color:var(--brown-mid);">Mengapa Kami?</p>
   <h2 class="section-title">Keunggulan Bake'n Brew</h2>
   <div class="divider"></div>
  </div>
  <div class="row g-4">
   <div class="col-md-4 fade-in-up delay-1">
    <div class="why-card">
     <span class="why-icon"><i class="bi bi-gem"></i></span>
     <h4>Bahan Premium Pilihan</h4>
     <p>Kami menggunakan bahan-bahan berkualitas tinggi dari supplier lokal terpercaya. Tepung organik, mentega premium, dan biji kopi arabika pilihan.</p>
    </div>
   </div>
   <div class="col-md-4 fade-in-up delay-2">
    <div class="why-card">
     <span class="why-icon"><i class="bi bi-clock"></i></span>
     <h4>Selalu Segar Setiap Hari</h4>
     <p>Semua produk roti dipanggang fresh setiap pagi dan kopi diseduh saat order. Tidak ada produk kemarin yang disajikan hari ini.</p>
    </div>
   </div>
   <div class="col-md-4 fade-in-up delay-3">
    <div class="why-card">
     <span class="why-icon"><i class="bi bi-cup-hot"></i></span>
     <h4>Suasana Cozy & Nyaman</h4>
     <p>Nikmati kopi dan roti dalam suasana kafe yang hangat dan tenang. Tempat yang tepat untuk bekerja, bersantai, atau berkumpul bersama.</p>
    </div>
   </div>
  </div>
 </div>
</section>

<!-- ======================== TESTIMONI ======================== -->
<section class="testi-section">
 <div class="container">
  <div class="text-center mb-5 fade-in-up">
   <p class="text-brown fw-semibold mb-1" style="font-size:.85rem;letter-spacing:2px;text-transform:uppercase;">Suara Mereka</p>
   <h2 class="section-title">Kata Pelanggan Kami</h2>
   <div class="divider"></div>
  </div>
  <div class="row g-4">
   <div class="col-md-4 fade-in-up delay-1">
    <div class="testi-card">
     <div class="quote">"</div>
     <div class="stars">★★★★★</div>
     <p class="review">Croissant-nya beneran enak banget! Renyah di luar, lembut di dalam, dan nggak terlalu berminyak. Udah jadi cemilan wajib tiap pagi aku.</p>
     <div class="testi-author">
      <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80&fm=webp" class="avatar" alt="Sari" loading="lazy" />
      <div>
       <div class="name">Sari Dewi</div>
       <div class="role">Pelanggan Setia</div>
      </div>
     </div>
    </div>
   </div>
   <div class="col-md-4 fade-in-up delay-2">
    <div class="testi-card">
     <div class="quote">"</div>
     <div class="stars">★★★★★</div>
     <p class="review">Signature Latte-nya juara! Espresso-nya strong tapi nggak pahit. Suasana kafenya juga cozy banget buat kerja remote. Recommended!</p>
     <div class="testi-author">
      <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&q=80&fm=webp" class="avatar" alt="Budi" loading="lazy" />
      <div>
       <div class="name">Budi Santoso</div>
       <div class="role">Coffee Enthusiast</div>
      </div>
     </div>
    </div>
   </div>
   <div class="col-md-4 fade-in-up delay-3">
    <div class="testi-card">
     <div class="quote">"</div>
     <div class="stars">★★★★★</div>
     <p class="review">Tempatnya estetik, makanannya enak, harganya masih oke. Donut-nya yang coklat wajib coba! Sering banget jadi tempat nongkrong sama teman.</p>
     <div class="testi-author">
      <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&q=80&fm=webp" class="avatar" alt="Maya" loading="lazy" />
      <div>
       <div class="name">Maya Rahayu</div>
       <div class="role">Food Blogger</div>
      </div>
     </div>
    </div>
   </div>
  </div>
 </div>
</section>

<!-- ======================== CTA BANNER ======================== -->
<section style="padding: 6rem 0; background: var(--cream); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
 <div class="container text-center fade-in-up">
  <h2 style="font-family:'Playfair Display',serif;color:var(--brown-dark);font-size:2.5rem;margin-bottom:0.8rem;font-weight:600;">
   Siap Mencicipi Pengalaman Terbaik?
  </h2>
  <p style="color:var(--text-mid);margin-bottom:2.5rem;max-width:480px;margin-left:auto;margin-right:auto;font-weight:300;font-size:0.95rem;">
   Kunjungi Bake'n Brew hari ini atau pesan langsung melalui form order kami.
  </p>
  <div class="d-flex gap-3 justify-content-center flex-wrap">
   <a href="form.php" class="btn-primary-brown">Order Sekarang</a>
   <a href="contact.php" class="btn-outline-brown">Hubungi Kami</a>
  </div>
 </div>
</section>

<!-- ======================== FOOTER ======================== -->
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
     <span style="color:var(--cream)">Sabtu – Minggu</span><br>08.00 – 22.00 WIB<br><br>
     <span style="color:var(--beige);font-size:.8rem;"><i class="bi bi-geo-alt-fill me-1"></i>Jl. Raya Darmo No. 88, Surabaya</span>
    </p>
   </div>
  </div>
 </div>
 <div class="footer-bottom">
  <div class="container">
   <p>© 2024 Bake'n Brew. Dibuat dengan cinta di Surabaya. All rights reserved.</p>
  </div>
 </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js?v=5.2"></script>
</body>
</html>
