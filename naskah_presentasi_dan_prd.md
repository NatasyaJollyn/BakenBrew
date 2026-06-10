# Ringkasan PRD & Naskah Dialog Presentasi EAS – Bake'n Brew

Dokumen ini menggabungkan ringkasan fungsionalitas teknis (PRD) dari seluruh sistem **Bake'n Brew** serta **Naskah Dialog Presentasi** terstruktur (untuk individu maupun kelompok) agar Anda tampil prima di hadapan dosen penguji.

---

## 📊 Bagian 1: Ringkasan PRD & Spesifikasi Teknis

Sistem **Bake'n Brew** adalah aplikasi web pemesanan kopi dan roti (*bakery*) berbasis *Full-Stack PHP-MySQL* dengan standar komersial premium. 

### 1. Spesifikasi Tech Stack
*   **Frontend**: HTML5, Vanilla CSS3 (desain visual modern, transisi halus, warna hangat bertema kopi), JavaScript (AJAX, State Management, DOM manipulation).
*   **Backend & Database**: Native PHP 8.x (arsitektur modular, OOP PDO), MySQL/MariaDB database.
*   **Integrasi Pihak Ketiga**: Bootstrap 5.3.3 (sistem grid responsif), Bootstrap Icons, Google Fonts (Outfit & Playfair Display).
*   **Deployment**: InfinityFree (PHP + MySQL Live Hosting) dengan domain `http://king-zays.infinityfreeapp.com`.

### 2. Fitur Utama & Keunggulan Fungsional
*   **Fitur Frontend (Pelanggan)**:
    *   *Katalog Dinamis*: Filtrasi produk interaktif (kategori Bakery, Coffee, Non-Coffee) dengan lencana dinamis *Best Seller* dan *New*.
    *   *Sistem Keranjang & Form Order*: Formulir order interaktif yang mengirimkan data via AJAX tanpa memuat ulang halaman.
    *   *Personal Transaction Cache*: Log pesanan aktif di bagian bawah form order yang disimpan di session browser pelanggan.
    *   *Real-time Store Status*: Banner status operasional (Buka/Tutup) yang tersinkronisasi langsung dengan setelan admin.
*   **Fitur Backend (Admin Panel)**:
    *   *Dashboard Ringkasan*: Statistik total produk, pesanan aktif, total omzet, bagan rasio kategori, serta toggle cepat status operasional toko.
    *   *CRUD Katalog Menu*: Manajemen produk lengkap dengan upload foto otomatis terkompresi ke WebP (hemat storage server).
    *   *Order Management Log*: Daftar pesanan masuk dengan kemampuan menandai selesai (*complete*) atau menghapus log transaksi.
    *   *Sistem Notifikasi Real-time*: Animasi lonceng bergoyang (*jiggle*) dan badge angka unread setiap ada pesanan baru masuk lewat polling 5 detik.
    *   *Multi-Language Preferences*: Pengubah bahasa panel admin (Inggris ↔ Indonesia) instan menggunakan session-state.

### 3. Aspek Keamanan & Ketangguhan Sistem (Resiliency)
> [!IMPORTANT]
> Aspek-aspek ini adalah poin krusial yang menaikkan skor EAS Anda menjadi **10/10** pada audit backend:
*   **Anti-Crash Database Failover**: Apabila koneksi MySQL terputus, halaman web tidak akan *crash* dengan layar putih error. Sistem secara otomatis menampilkan banner warning kuning dan memuat *mock data* (data cadangan statis dari JSON) sehingga pengunjung tetap bisa melihat menu.
*   **Double Submit Protection**: Tombol "Order" dikunci seketika (*disabled*) dan memunculkan teks loading saat pelanggan melakukan pemesanan untuk menghindari duplikasi data akibat klik ganda.
*   **SQL Injection & XSS Mitigation**: Menggunakan *PDO Prepared Statements* di seluruh query database untuk menangkal SQLi, serta fungsi `htmlspecialchars()` di setiap output teks untuk menangkal injeksi naskah jahat (XSS).
*   **Upload File Integrity**: Validasi file upload menggunakan `getimagesize()` dan kompresi WebP untuk mengamankan direktori admin dari berkas berbahaya.

---

## 🗣️ Bagian 2: Naskah Dialog & Alur Presentasi Live (Durasi: ~5-7 Menit)

> [!TIP]
> **Rekomendasi Suara Google AI Studio (Multi-Voice)**:
> Untuk demo kelompok beranggotakan 5 orang, sangat disarankan menggunakan **5 jenis suara AI yang berbeda** (misalnya memadukan suara laki-laki dan perempuan dengan intonasi berbeda) agar mencerminkan kontribusi masing-masing anggota.
> Di awal bagian, masing-masing suara AI harus menyebutkan nama anggotanya agar dosen tahu siapa yang sedang berkontribusi di bagian tersebut.

### 🎭 Pembagian Peran & Rekomendasi Suara AI:
1.  **Presenter 1 (P1 - Pembukaan & Intro)**: Menyapa penguji, mengenalkan tim, dan mendemokan beranda. *(Rekomendasi: Suara Laki-laki A, tenang & berwibawa)*
2.  **Presenter 2 (P2 - Katalog Menu)**: Mendemokan halaman produk, filter kategori, dan lencana produk. *(Rekomendasi: Suara Perempuan A, ceria & jelas)*
3.  **Presenter 3 (P3 - Formulir Transaksi)**: Mengisi form pesanan, mendemokan Double Submit Protection, dan toast sukses. *(Rekomendasi: Suara Laki-laki B, tegas & presisi)*
4.  **Presenter 4 (P4 - Dashboard Admin & Notifikasi)**: Menjelaskan login admin, grafik dashboard, dan lonceng notifikasi real-time. *(Rekomendasi: Suara Perempuan B, profesional & terstruktur)*
5.  **Presenter 5 (P5 - Ketangguhan Sistem & Penutup)**: Mendemokan setelan tutup toko, simulasi database offline, keamanan kode, dan penutup. *(Rekomendasi: Suara Laki-laki C, logis & informatif)*

---

### 🎬 Naskah Percakapan & Petunjuk Layar

#### **Bagian 1: Pembukaan & Tampilan Beranda**
*   **Petunjuk Layar**: *Tampilkan halaman utama `index.php` secara live dari domain hosting `http://king-zays.infinityfreeapp.com`.*
*   **P1 - Suara Laki-laki A (Bicara)**:
    > "Selamat pagi/siang kepada Bapak/Ibu Dosen Penguji dan rekan-rekan sekalian. Saya perwakilan kelompok 5 akan memandu jalannya presentasi web **Bake'n Brew**. Hari ini kami sangat antusias mendemokan aplikasi pemesanan online untuk produk kopi dan roti premium secara live. 
    > 
    > Seperti yang terlihat di layar, kami menerapkan desain visual bertema hangat (*warm coffee shop*) dengan tipografi modern untuk kenyamanan mata pengguna. Pada navbar atas, terdapat lencana status toko aktif, yang saat ini menyala hijau bertuliskan **Buka**. Selanjutnya, rekan saya akan mendemokan halaman menu."

#### **Bagian 2: Katalog Menu & Fitur Filter**
*   **Petunjuk Layar**: *Beralih ke halaman produk `product.php`. Klik filter kategori 'Bakery' lalu klik filter 'Coffee'.*
*   **P2 - Suara Perempuan A (Bicara)**:
    > "Halo, saya Natasya. Saya akan menjelaskan bagian Katalog Menu. Di halaman `product.php` ini, pelanggan dapat menelusuri produk yang kami tawarkan. 
    > 
    > Kami menyematkan filter kategori berbasis JavaScript yang bekerja sangat cepat dan interaktif tanpa memuat ulang halaman. Setiap kartu produk juga dilengkapi lencana dinamis seperti *Best Seller* atau *New* untuk produk unggulan, dengan aspek rasio gambar yang seragam agar layout terlihat premium."

#### **Bagian 3: Alur Pemesanan & Proteksi Tombol**
*   **Petunjuk Layar**: *Beralih ke halaman pemesanan `form.php`. Isi form dengan lengkap.*
*   **P3 - Suara Laki-laki B (Bicara)**:
    > "Halo, saya Firzan. Saya akan mendemonstrasikan alur pemesanan pelanggan di halaman `form.php`. Saya akan memilih menu *Croissant Butter*, mengisi Nama, Email, Jumlah Pesanan sebanyak `2`, dan menambahkan catatan: *'Minta dihangatkan'*. Harap perhatikan tombol **Order** ketika saya menekannya."
*   **Petunjuk Layar**: *Klik tombol "Order". Tunjukkan tombol yang berubah menjadi abu-abu (disabled) dengan teks "Memproses..." sebelum menampilkan toast sukses.*
*   **P3 - Suara Laki-laki B (Bicara)**:
    > "Ketika tombol diklik, sistem langsung mengunci tombol tersebut (*disabled*) dan memunculkan animasi loading. Fitur **Double Submit Protection** ini dirancang untuk mencegah pelanggan tidak sengaja mengirimkan pesanan ganda akibat klik ganda saat koneksi lambat. Setelah berhasil, notifikasi Toast sukses muncul di sudut kanan atas, dan baris pesanan baru langsung ter-render di tabel pesanan aktif pelanggan."

#### **Bagian 4: Dashboard Admin & Notifikasi Real-time**
*   **Petunjuk Layar**: *Buka tab admin yang telah login dan berada di Dashboard `admin/dashboard.php`. Jangan di-refresh. Tunggu ikon lonceng bergoyang.*
*   **P4 - Suara Perempuan B (Bicara)**:
    > "Halo, saya Ratna. Kini kita beralih ke sisi **Admin Panel** di `/admin/dashboard.php`. Di dashboard utama ini, admin disajikan data analitik operasional seperti statistik pesanan, total omzet, bagan rasio produk, serta tombol toggle cepat status operasional toko.
    > 
    > Perhatikan ikon lonceng di navbar kanan atas. Ikon lonceng bergoyang (*jiggle animation*) secara otomatis dan badge angkanya bertambah `1`. Sistem melakukan polling berkala di latar belakang untuk mendeteksi transaksi baru tanpa admin perlu me-refresh halaman."
*   **Petunjuk Layar**: *Klik ikon lonceng, lalu klik notifikasi teratas untuk masuk ke halaman `admin/pesanan.php`.*
*   **P4 - Suara Perempuan B (Bicara)**:
    > "Ketika admin mengklik notifikasi tersebut, sistem otomatis mengarahkan ke halaman **Kelola Pesanan**. Di sini pesanan baru berstatus *Pending*. Admin dapat menyiapkannya dan kemudian mengklik ikon centang hijau untuk menandai pesanan selesai."

#### **Bagian 5: Pengujian Kondisi Ekstrem & Keamanan**
*   **Petunjuk Layar**: *Kembali ke dashboard admin, matikan toggle status toko menjadi "CLOSED". Beralih ke tab pelanggan, refresh halaman `index.php` dan `form.php`.*
*   **P5 - Suara Laki-laki C (Bicara)**:
    > "Halo, saya Refi. Saya akan menjelaskan aspek ketangguhan dan keamanan sistem. Jika admin menonaktifkan status operasional toko menjadi **Tutup**, di sisi pelanggan langsung muncul banner merah di bawah layar. Selain itu, formulir pemesanan otomatis terkunci dan diarsir buram untuk mencegah transaksi ilegal saat toko tutup.
    > 
    > Selanjutnya, kami juga mendemonstrasikan penanganan database offline."
*   **Petunjuk Layar**: *Buka Laragon/XAMPP, matikan servis MySQL (Apache tetap menyala). Kembali ke tab pelanggan di halaman produk `product.php` lalu lakukan refresh.*
*   **P5 - Suara Laki-laki C (Bicara)**:
    > "Jika database MySQL mati, halaman PHP Bake'n Brew tidak akan crash! Sistem mendeteksi kegagalan koneksi, memunculkan banner warning kuning bertuliskan *'Koneksi Database Offline'*, lalu memuat *mock data* produk dari berkas JSON lokal agar pelanggan tetap bisa melihat menu. 
    > 
    > Seluruh kode program kami rancang menggunakan standar keamanan tertinggi: menangkal SQL Injection dengan *Prepared Statements*, menyaring input dari XSS dengan `htmlspecialchars()`, serta memvalidasi upload file gambar menggunakan `getimagesize()` dan mengompresnya ke format WebP agar performa loading website sangat cepat dan efisien.
    > 
    > Kesimpulannya, website Bake'n Brew siap dideploy dan dirilis ke production [READY TO RELEASE]. Terima kasih."

---

## 💡 Tips Tambahan untuk Menjawab Pertanyaan Dosen:
1.  **Jika ditanya tentang State Management**: Jawablah bahwa status order aktif pelanggan disimpan menggunakan `sessionStorage` di sisi browser, sehingga data keranjang belanja tetap aman selama tab browser tidak ditutup.
2.  **Jika ditanya tentang Kompresi WebP**: Jawablah bahwa gambar produk yang diunggah dikompresi di sisi server menggunakan pustaka GD PHP (`imagecreatefrompng` / `imagewebp`) untuk mengurangi ukuran file hingga 70-80% tanpa mengurangi kualitas visual secara drastif.
3.  **Jika ditanya tentang Polling Notifikasi**: Jawablah bahwa admin menggunakan fungsi `setInterval()` di JavaScript untuk menembak endpoint `get_notifications.php` setiap 5 detik guna mendeteksi status pesanan baru secara berkala tanpa membebani server secara berlebih.
