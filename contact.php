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
 <title>Kontak – Bake'n Brew</title>
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
    <li class="breadcrumb-item active">Kontak</li>
   </ol>
  </nav>
  <h1>Hubungi Kami</h1>
  <p>Ada pertanyaan atau ingin buat reservasi? Kami senang mendengar dari kamu!</p>
 </div>
</section>

<!-- CONTACT INFO + FORM -->
<section class="contact-section">
 <div class="container">
  <div class="row g-4">

   <!-- Info Card -->
   <div class="col-lg-5 fade-in-up">
    <div class="contact-info-card">
     <h3>Bake'n Brew</h3>
     <p>Café & Bakery terbaik di Surabaya. Kami selalu siap melayani kamu dengan senyuman.</p>

     <div class="contact-item">
      <div class="ci-icon"><i class="bi bi-geo-alt-fill"></i></div>
      <div class="ci-text">
       <div class="label">Alamat</div>
       <div class="value">Jl. Raya Darmo No. 88, Wonokromo,<br>Surabaya, Jawa Timur 60241</div>
      </div>
     </div>

     <div class="contact-item">
      <div class="ci-icon"><i class="bi bi-telephone-fill"></i></div>
      <div class="ci-text">
       <div class="label">Telepon / WhatsApp</div>
       <div class="value"><a href="https://wa.me/6282335871770" target="_blank" rel="noopener noreferrer" style="color:var(--cream);text-decoration:none;">+62 823-3587-1770</a></div>
      </div>
     </div>

     <div class="contact-item">
      <div class="ci-icon"><i class="bi bi-envelope-fill"></i></div>
      <div class="ci-text">
       <div class="label">Email</div>
       <div class="value"><a href="mailto:natasyajollyn@gmail.com" style="color:var(--cream);text-decoration:none;">natasyajollyn@gmail.com</a></div>
      </div>
     </div>

     <div class="contact-item">
      <div class="ci-icon"><i class="bi bi-instagram"></i></div>
      <div class="ci-text">
       <div class="label">Instagram</div>
       <div class="value"><a href="https://www.instagram.com/natasyajollyn/" target="_blank" rel="noopener noreferrer" style="color:var(--cream);text-decoration:none;">@natasyajollyn</a></div>
      </div>
     </div>

     <div class="contact-item">
      <div class="ci-icon"><i class="bi bi-clock-fill"></i></div>
      <div class="ci-text">
       <div class="label">Jam Buka</div>
       <div class="value">Sen–Jum: 07.00 – 21.00 WIB<br>Sab–Min: 08.00 – 22.00 WIB</div>
      </div>
     </div>

     <div class="social-links">
      <a href="https://www.instagram.com/natasyajollyn/" target="_blank" rel="noopener noreferrer" class="social-link"><i class="bi bi-instagram"></i></a>
      <a href="https://wa.me/6282335871770" target="_blank" rel="noopener noreferrer" class="social-link"><i class="bi bi-whatsapp"></i></a>
      <a href="https://www.tiktok.com/@lynnatzz?_r=1&_t=ZS-96sLinjnNw1" target="_blank" rel="noopener noreferrer" class="social-link"><i class="bi bi-tiktok"></i></a>
      <a href="mailto:natasyajollyn@gmail.com" class="social-link"><i class="bi bi-envelope"></i></a>
     </div>
    </div>
   </div>

   <!-- Contact Form -->
   <div class="col-lg-7 fade-in-up delay-2">
    <div class="contact-form-card">
     <h3 class="mb-1" style="color:var(--brown-dark)">Kirim Pesan</h3>
     <p style="color:var(--text-mid);font-size:.9rem;margin-bottom:2rem;">Isi form berikut dan kami akan membalas dalam 1×24 jam.</p>

     <form id="contactForm" novalidate>
      <div class="row g-3">
       <div class="col-sm-6">
        <label class="form-label" for="cNama">Nama Lengkap *</label>
        <input type="text" class="form-control" id="cNama" placeholder="Contoh: Budi Santoso" required />
        <div class="invalid-feedback">Nama tidak boleh kosong.</div>
       </div>
       <div class="col-sm-6">
        <label class="form-label" for="cEmail">Email *</label>
        <input type="email" class="form-control" id="cEmail" placeholder="emailkamu@gmail.com" required />
        <div class="invalid-feedback">Masukkan email yang valid.</div>
       </div>
       <div class="col-sm-6">
        <label class="form-label" for="cPhone">Nomor HP</label>
        <input type="tel" class="form-control" id="cPhone" placeholder="+62 812-XXXX-XXXX" />
       </div>
       <div class="col-sm-6">
        <label class="form-label" for="cSubject">Subjek *</label>
        <select class="form-select" id="cSubject" required>
         <option value="">Pilih subjek...</option>
         <option>Reservasi Tempat</option>
         <option>Pesanan / Catering</option>
         <option>Kerjasama / Partnership</option>
         <option>Saran & Kritik</option>
         <option>Lainnya</option>
        </select>
        <div class="invalid-feedback">Pilih subjek pesan.</div>
       </div>
       <div class="col-12">
        <label class="form-label" for="cPesan">Pesan *</label>
        <textarea class="form-control" id="cPesan" rows="5" placeholder="Tulis pesanmu di sini..." required style="resize:none;"></textarea>
        <div class="invalid-feedback">Pesan tidak boleh kosong.</div>
       </div>
       <div class="col-12">
        <button type="submit" class="btn-primary-brown w-100" style="border:none;">
         <i class="bi bi-send me-2"></i>Kirim Pesan
        </button>
       </div>
      </div>
     </form>

     <!-- Success message -->
     <div id="contactSuccess" style="display:none;margin-top:1.5rem;padding:1.2rem;background:#f0fdf4;border:1px solid #86efac;border-radius:var(--radius-sm);">
      <p style="color:#166534;margin:0;font-size:.9rem;font-weight:500;">
        Pesan kamu berhasil terkirim! Kami akan membalas dalam 1×24 jam ke email yang kamu daftarkan.
      </p>
     </div>
    </div>
   </div>
  </div>

  <!-- GOOGLE MAPS -->
  <div class="map-frame mt-5 fade-in-up">
   <iframe
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.6!2d112.7314!3d-7.2897!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fc5f3e9d1b47%3A0x8abc5c3e2b65fa1!2sJl.+Raya+Darmo%2C+Surabaya!5e0!3m2!1sid!2sid!4v1635000000000!5m2!1sid!2sid"
    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
   </iframe>
  </div>

  <!-- Quick Info Cards -->
   <div class="row g-3 mt-4">
    <div class="col-md-4 fade-in-up delay-1">
     <div style="background:var(--cream);border-radius:var(--radius-md);padding:1.5rem;text-align:center;">
      <div style="font-size:2rem;margin-bottom:.5rem;color:var(--brown-dark);"><i class="bi bi-p-circle"></i></div>
      <h6 style="color:var(--brown-dark);margin-bottom:.4rem;">Parkir Tersedia</h6>
      <p style="font-size:.82rem;color:var(--text-mid);margin:0;">Area parkir luas tersedia untuk motor dan mobil di belakang gedung.</p>
     </div>
    </div>
    <div class="col-md-4 fade-in-up delay-2">
     <div style="background:var(--cream);border-radius:var(--radius-md);padding:1.5rem;text-align:center;">
      <div style="font-size:2rem;margin-bottom:.5rem;color:var(--brown-dark);"><i class="bi bi-wifi"></i></div>
      <h6 style="color:var(--brown-dark);margin-bottom:.4rem;">Free Wi-Fi</h6>
      <p style="font-size:.82rem;color:var(--text-mid);margin:0;">Nikmati koneksi internet gratis berkecepatan tinggi selama berada di kafe.</p>
     </div>
    </div>
    <div class="col-md-4 fade-in-up delay-3">
     <div style="background:var(--cream);border-radius:var(--radius-md);padding:1.5rem;text-align:center;">
      <div style="font-size:2rem;margin-bottom:.5rem;color:var(--brown-dark);"><i class="bi bi-universal-access"></i></div>
      <h6 style="color:var(--brown-dark);margin-bottom:.4rem;">Ramah Disabilitas</h6>
      <p style="font-size:.82rem;color:var(--text-mid);margin:0;">Fasilitas kami dirancang untuk kenyamanan semua pengunjung.</p>
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
<script>
 // Contact Form submit handler
 document.getElementById('contactForm').addEventListener('submit', function(e){
  e.preventDefault();
  if (!this.checkValidity()) { this.classList.add('was-validated'); return; }
  document.getElementById('contactSuccess').style.display = 'block';
  this.reset();
  this.classList.remove('was-validated');
  document.getElementById('contactSuccess').scrollIntoView({ behavior: 'smooth', block: 'center' });
 });
</script>
</body>
</html>
