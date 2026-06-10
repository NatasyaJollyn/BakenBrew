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

*Berikut adalah draf percakapan terstruktur. Naskah ini ditulis untuk **Presentasi Kelompok (4 Orang)**, namun jika Anda mempresentasikannya **Individu**, Anda tinggal membacakan seluruh bagian narator dengan mulus.*

### 🎭 Pembagian Peran
1.  **Presenter 1 (P1 - Moderator & Frontend)**: Pembukaan, Latar Belakang, Tampilan Beranda & Menu.
2.  **Presenter 2 (P2 - Alur Transaksi & Validasi)**: Form Pemesanan, Double Submit Protection, & Respon Sukses.
3.  **Presenter 3 (P3 - Admin Panel & Notifikasi)**: Dashboard Admin, Real-time Notification, & Operasional Toko.
4.  **Presenter 4 (P4 - Ketangguhan Sistem & Penutup)**: Demonstrasi Mode Offline (Failover), Aspek Keamanan, & Penutupan.

---

### 🎬 Naskah Percakapan & Petunjuk Layar

#### **Bagian 1: Pembukaan & Tampilan Depan (Pelanggan)**
*   **Petunjuk Layar**: *Tampilkan halaman utama `index.php` secara live dari domain hosting `http://king-zays.infinityfreeapp.com`.*
*   **P1 (Bicara)**:
    > "Selamat pagi/siang kepada Bapak/Ibu Dosen Penguji dan rekan-rekan sekalian. Hari ini kelompok kami akan mendemokan aplikasi web **Bake'n Brew**, sebuah platform pemesanan online untuk produk kopi dan roti premium secara langsung dan terintegrasi.
    > 
    > Seperti yang terlihat di layar, kami menerapkan desain visual bertema hangat (*warm coffee shop vibe*) dengan tipografi modern untuk kenyamanan mata pengguna. Pada navbar atas, terdapat lencana status toko aktif, yang saat ini menyala hijau bertuliskan **Buka**."
*   **Petunjuk Layar**: *Beralih ke halaman produk `product.php`. Klik filter kategori 'Bakery' lalu klik filter 'Coffee'.*
*   **P1 (Bicara)**:
    > "Di halaman Katalog Menu, pengguna dapat menelusuri produk yang kami tawarkan. Kami menyematkan sistem filtrasi berbasis JavaScript yang sangat cepat dan interaktif tanpa memuat ulang halaman. Setiap kartu produk juga dilengkapi lencana dinamis seperti *Best Seller* atau *New* untuk produk unggulan."

#### **Bagian 2: Proses Transaksi & Pengamanan Formulir**
*   **Petunjuk Layar**: *Beralih ke halaman pemesanan `form.php`.*
*   **P2 (Bicara)**:
    > "Sekarang kita akan mensimulasikan alur transaksi pelanggan. Saya akan memilih menu *Croissant Butter* dari dropdown, mengisi Nama Lengkap, Email, Jumlah Pesanan sebanyak `2`, dan menambahkan catatan khsusus: *'Minta dihangatkan'*.
    > 
    > Harap perhatikan tombol **Order** ketika saya menekannya."
*   **Petunjuk Layar**: *Klik tombol "Order". Tunjukkan tombol yang berubah menjadi abu-abu (disabled) dengan teks "Memproses..." sebelum menampilkan toast sukses.*
*   **P2 (Bicara)**:
    > "Ketika tombol diklik, sistem langsung mengunci tombol tersebut dan memunculkan animasi loading. Fitur **Double Submit Protection** ini kami rancang untuk mencegah pelanggan tidak sengaja mengirimkan pesanan ganda akibat klik ganda saat koneksi melambat. 
    > 
    > Setelah berhasil, notifikasi Toast sukses muncul di sudut kanan atas, dan baris pesanan baru langsung ter-render di tabel pesanan aktif pelanggan di bagian bawah."

#### **Bagian 3: Panel Admin & Notifikasi Real-time**
*   **Petunjuk Layar**: *Buka tab admin yang telah login dan berada di Dashboard `admin/dashboard.php`. Jangan di-refresh. Tunggu ikon lonceng bergoyang.*
*   **P3 (Bicara)**:
    > "Kini kita beralih ke sisi **Admin Panel**. Di dashboard utama, admin disajikan data analitik operasional seperti jumlah produk, pesanan aktif, total omzet, serta bagan rasio produk.
    > 
    > Lihat pada ikon lonceng di navbar kanan atas. Lonceng bergoyang (*jiggle animation*) secara otomatis dan badge angkanya bertambah. Sistem melakukan polling berkala di latar belakang untuk mendeteksi transaksi baru tanpa admin perlu me-refresh halaman."
*   **Petunjuk Layar**: *Klik ikon lonceng, lalu klik notifikasi teratas untuk masuk ke halaman `admin/pesanan.php`.*
*   **P3 (Bicara)**:
    > "Ketika admin mengklik baris notifikasi tersebut, sistem otomatis mengarahkan ke halaman **Kelola Pesanan**. Di sini pesanan baru berstatus *Pending*. Admin dapat menyiapkannya dan kemudian mengklik ikon centang hijau untuk menandai pesanan selesai. Status pesanan pun langsung diperbarui di database."

#### **Bagian 4: Pengujian Kondisi Ekstrem & Keamanan (Sesi Demonstrasi Utama)**
*   **Petunjuk Layar**: *Kembali ke dashboard admin, matikan toggle status toko menjadi "CLOSED". Beralih ke tab pelanggan, refresh halaman `index.php` dan `form.php`.*
*   **P4 (Bicara)**:
    > "Salah satu nilai tambah terbesar dari Bake'n Brew adalah penanganan kondisi ekstrem. Jika admin menonaktifkan status operasional toko menjadi **Tutup**, di sisi pelanggan langsung muncul banner merah di bagian bawah layar. Selain itu, formulir pemesanan otomatis terkunci dan diarsir buram untuk mencegah transaksi ilegal saat toko tutup.
    > 
    > Selanjutnya, kami akan mendemonstrasikan fitur ketangguhan sistem yang paling krusial, yaitu penanganan database offline."
*   **Petunjuk Layar**: *Buka Laragon/XAMPP, matikan servis MySQL (Apache tetap menyala). Kembali ke tab pelanggan di halaman produk `product.php` lalu lakukan refresh.*
*   **P4 (Bicara)**:
    > "Umumnya, jika database MySQL mati, halaman PHP akan crash dan memunculkan pesan error kode yang berantakan. Namun pada sistem Bake'n Brew, halaman tetap dapat diakses dengan mulus! 
    > 
    > Sistem mendeteksi kegagalan koneksi, memunculkan banner warning kuning bertuliskan *'Koneksi Database Offline'*, lalu memuat *mock data* produk dari berkas JSON lokal agar pelanggan tidak melihat halaman kosong yang rusak."
*   **Petunjuk Layar**: *Buka halaman admin `admin/produk.php` (dalam kondisi MySQL mati).*
*   **P4 (Bicara)**:
    > "Di sisi admin, seluruh tombol aksi manipulasi data seperti tambah menu, edit, atau hapus otomatis dikunci dan dinonaktifkan demi melindungi integritas data dari kerusakan saat server database mati.
    > 
    > Seluruh kode program kami rancang menggunakan standar keamanan tertinggi: menangkal serangan SQL Injection dengan *PDO Prepared Statements*, meng-escape karakter input menggunakan `htmlspecialchars()` untuk mencegah XSS, serta memverifikasi validitas file gambar menggunakan `getimagesize()` dan mengompresnya ke format WebP agar performa loading website sangat cepat dan efisien.
    > 
    > Kesimpulannya, website Bake'n Brew ini siap dirilis dengan predikat kelayakan penuh [READY TO RELEASE]. Sekian presentasi dari kelompok kami, kami persilakan jika ada pertanyaan dari Bapak/Ibu Dosen. Terima kasih."

---

## 💡 Tips Tambahan untuk Menjawab Pertanyaan Dosen:
1.  **Jika ditanya tentang State Management**: Jawablah bahwa status order aktif pelanggan disimpan menggunakan `sessionStorage` di sisi browser, sehingga data keranjang belanja tetap aman selama tab browser tidak ditutup.
2.  **Jika ditanya tentang Kompresi WebP**: Jawablah bahwa gambar produk yang diunggah dikompresi di sisi server menggunakan pustaka GD PHP (`imagecreatefrompng` / `imagewebp`) untuk mengurangi ukuran file hingga 70-80% tanpa mengurangi kualitas visual secara drastif.
3.  **Jika ditanya tentang Polling Notifikasi**: Jawablah bahwa admin menggunakan fungsi `setInterval()` di JavaScript untuk menembak endpoint `get_notifications.php` setiap 5 detik guna mendeteksi status pesanan baru secara berkala tanpa membebani server secara berlebih.
