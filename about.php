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
 <title>Tentang Kami – Bake'n Brew</title>
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
    <li class="breadcrumb-item active">Tentang Kami</li>
   </ol>
  </nav>
  <h1>Tentang Kami</h1>
  <p>Kenali lebih dekat cerita di balik setiap roti dan kopi yang kami sajikan.</p>
 </div>
</section>

<!-- OUR STORY -->
<section class="about-section">
 <div class="container">
  <div class="row align-items-center g-5">
    <div class="col-lg-5 fade-in-up">
     <div class="about-img-wrap">
      <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=700&q=80&fm=webp" alt="Bake'n Brew Story" loading="lazy" />
      <div class="about-badge">
       <div class="txt">Since Day One</div>
      </div>
     </div>
    </div>
   <div class="col-lg-7 fade-in-up delay-2">
    <div class="about-text">
     <p class="fw-semibold mb-1" style="font-size:.85rem;letter-spacing:2px;text-transform:uppercase;color:var(--brown-mid);">Cerita Kami</p>
     <h2 class="section-title mb-3">Dari Dapur Kecil<br>ke Café Impian</h2>
     <div class="divider" style="margin-left:0"></div>
     <p>Bake'n Brew lahir pada tahun 2019 dari sebuah mimpi sederhana di kawasan Darmo, Surabaya. Berawal dari kolaborasi kreatif antara <strong>Natasya Jollyn</strong> (Co-Founder & Head Baker) yang berdedikasi pada kelezatan roti segar buatan tangan, dan <strong>Firzan Syaroni</strong> (Co-Founder & Barista Expert) yang mengkurasi racikan kopi premium. Dari dapur kecil tersebut, kehangatan cita rasa kreasi mereka mulai memikat hati para pencinta kuliner di Surabaya.</p>
     <p>Seiring berkembangnya gerai pertama kami pada Maret 2021, mimpi ini terwujud secara nyata berkat manajemen operasional yang solid dari <strong>Moch. Refi</strong> (Head of Operations) yang memastikan setiap sudut kafe menyajikan kenyamanan terbaik, serta dedikasi tinggi dari <strong>Ratna Yuliana</strong> (Pastry Chef) yang selalu menyempurnakan setiap hidangan manis penutup kami.</p>
     <p>Kini, digerakkan oleh kuartet pendiri yang solid bersama tim berdedikasi lainnya, Bake'n Brew hadir setiap hari menyajikan lebih dari 50 menu pilihan roti segar dan kopi berkualitas tinggi yang dibuat khusus untuk menemani momen berharga Anda.</p>
     <div class="row g-3 mt-3">
      <div class="col-6">
       <div style="display:flex;align-items:center;gap:.7rem;">
        <span style="font-size:1.6rem"></span>
        <div>
         <div style="font-weight:600;font-size:.9rem;color:var(--brown-dark)">Top UMKM Surabaya 2023</div>
         <div style="font-size:.78rem;color:var(--text-mid)">Penghargaan Kota Surabaya</div>
        </div>
       </div>
      </div>
      <div class="col-6">
       <div style="display:flex;align-items:center;gap:.7rem;">
        <span style="font-size:1.6rem"></span>
        <div>
         <div style="font-weight:600;font-size:.9rem;color:var(--brown-dark)">Rating 4.9/5</div>
         <div style="font-size:.78rem;color:var(--text-mid)">Dari 800+ ulasan</div>
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>
  </div>
 </div>
</section>

<!-- VISI MISI -->
<section class="vision-mission-section">
 <div class="container">
  <div class="text-center mb-5 fade-in-up">
   <p class="fw-semibold mb-1" style="font-size:.85rem;letter-spacing:2px;text-transform:uppercase;color:var(--brown-mid);">Arah Kami</p>
   <h2 class="section-title">Visi & Misi</h2>
   <div class="divider"></div>
  </div>
  <div class="row g-4">
   <div class="col-md-4 fade-in-up delay-1">
    <div class="vm-card">
     <div class="icon"></div>
     <h4>Visi</h4>
     <p style="font-size:.9rem;color:var(--text-mid);">Menjadi café dan bakery lokal terbaik di Surabaya yang menghadirkan pengalaman kuliner autentik dan berkesan bagi setiap pelanggan.</p>
    </div>
   </div>
   <div class="col-md-4 fade-in-up delay-2">
    <div class="vm-card">
     <div class="icon"></div>
     <h4>Misi</h4>
     <ul style="font-size:.88rem;color:var(--text-mid);padding-left:1.2rem;">
      <li>Menggunakan bahan baku lokal berkualitas tinggi</li>
      <li>Menyajikan produk segar setiap hari tanpa kompromi</li>
      <li>Memberikan pelayanan hangat dan ramah</li>
      <li>Mendukung petani dan produsen lokal Indonesia</li>
     </ul>
    </div>
   </div>
   <div class="col-md-4 fade-in-up delay-3">
    <div class="vm-card">
     <div class="icon"></div>
     <h4>Nilai Kami</h4>
     <ul style="font-size:.88rem;color:var(--text-mid);padding-left:1.2rem;">
      <li><strong>Kualitas</strong> – Tidak pernah berkompromi soal rasa</li>
      <li><strong>Kehangatan</strong> – Sambut setiap tamu seperti keluarga</li>
      <li><strong>Kreativitas</strong> – Terus berinovasi dalam menu</li>
      <li><strong>Keberlanjutan</strong> – Peduli lingkungan & komunitas</li>
     </ul>
    </div>
   </div>
  </div>
 </div>
</section>

<!-- TIM KAMI -->
<section class="team-section">
 <div class="container">
  <div class="text-center mb-5 fade-in-up">
   <p class="text-brown fw-semibold mb-1" style="font-size:.85rem;letter-spacing:2px;text-transform:uppercase;">Di Balik Layar</p>
   <h2 class="section-title">Tim Kami</h2>
   <div class="divider"></div>
   <p class="section-subtitle">Orang-orang berbakat yang setiap hari bekerja keras menghadirkan yang terbaik untuk kamu.</p>
  </div>
  <div class="row g-4 justify-content-center">
    <div class="col-6 col-md-3 fade-in-up delay-1">
     <div class="team-card">
      <img src="tim%20kami/Natasya%20Jollyn%20Karisya%20Agustin.webp" alt="Natasya Jollyn" loading="lazy" />
      <h5>Natasya Jollyn</h5>
      <p>Co-Founder & Head Baker</p>
     </div>
    </div>
    <div class="col-6 col-md-3 fade-in-up delay-2">
     <div class="team-card">
      <img src="tim%20kami/Firzan%20Syaroni.webp" alt="Firzan Syaroni" loading="lazy" />
      <h5>Firzan Syaroni</h5>
      <p>Co-Founder & Barista Expert</p>
     </div>
    </div>
    <div class="col-6 col-md-3 fade-in-up delay-3">
     <div class="team-card">
      <img src="tim%20kami/Moch.%20Refi%20Febrian%20Alfani.webp" alt="Moch. Refi" loading="lazy" />
      <h5>Moch. Refi</h5>
      <p>Head of Operations</p>
     </div>
    </div>
    <div class="col-6 col-md-3 fade-in-up delay-4">
     <div class="team-card">
      <img src="tim%20kami/Ratna%20Yuliana%20Triyono.webp" alt="Ratna Yuliana" loading="lazy" />
      <h5>Ratna Yuliana</h5>
      <p>Pastry Chef</p>
     </div>
    </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js?v=5.2"></script>
</body>
</html>
