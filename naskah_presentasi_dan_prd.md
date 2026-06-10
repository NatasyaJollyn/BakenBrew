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

## 🗣️ Bagian 2: Naskah Dialog & Alur Presentasi Live (Durasi: ~7-9 Menit)

> [!TIP]
> **Rekomendasi Suara Google AI Studio (Multi-Voice)**:
> Untuk presentasi kelompok beranggotakan **4 orang**, sangat disarankan menggunakan **4 jenis suara AI yang berbeda** (misalnya memadukan suara laki-laki dan perempuan dengan intonasi berbeda) agar mencerminkan kontribusi masing-masing anggota.
> Di awal bagian, masing-masing suara AI harus menyebutkan nama anggotanya agar dosen tahu siapa yang sedang berkontribusi di bagian tersebut.

### 🎭 Pembagian Peran & Rekomendasi Suara AI:
1.  **Presenter 1 (P1 - Pembukaan, Login & Dashboard Admin)**: Menyapa penguji, mengenalkan tim, mendemokan login admin, dashboard analitik, dan status operasional. *(Rekomendasi: Suara Laki-laki A, tenang & berwibawa)*
2.  **Presenter 2 (P2 - CRUD Katalog & Kelola Pesanan)**: Menjelaskan proses CRUD produk (WebP + GD library), kelola status pesanan, serta sistem notifikasi real-time (polling). *(Rekomendasi: Suara Perempuan A, ceria & jelas)*
3.  **Presenter 3 (P3 - Frontend Pelanggan, Katalog & Transaksi)**: Beranda pelanggan, katalog interaktif dengan filter JS, pengisian form order, AJAX submit, dan Double Submit Protection. *(Rekomendasi: Suara Laki-laki B, tegas & presisi)*
4.  **Presenter 4 (P4 - Ketangguhan Sistem, Keamanan & Penutup)**: Demonstrasi skenario tutup toko, failover database offline (mock data), ringkasan keamanan kode (SQLi/XSS), dan penutupan. *(Rekomendasi: Suara Perempuan B, profesional & terstruktur)*

---

### 🎬 Naskah Percakapan & Petunjuk Layar (Admin-First & Detail Halaman)

#### **Bagian 1: Pembukaan, Login & Dashboard Admin**
*   **Petunjuk Layar**: *Coba ketik langsung URL `/admin/dashboard.php` di browser saat belum login, untuk menunjukkan sistem menolak bypass dan langsung me-redirect kembali ke `/admin/login.php`. Setelah itu, kembalikan ke tampilan halaman login admin `/admin/login.php`.*
*   **P1 - Suara Laki-laki A (Bicara)**:
    > "Selamat pagi/siang kepada Bapak/Ibu Dosen Penguji dan rekan-rekan sekalian. Kami dari kelompok Bake'n Brew akan mempresentasikan aplikasi web pemesanan online untuk produk kopi dan roti premium secara live. 
    > 
    > Berbeda dari demo biasa, kami memulai presentasi dari pusat kendali sistem, yaitu **Admin Panel (Backend)**. 
    > 
    > Untuk memenuhi syarat tugas, kami menerapkan sistem otentikasi login yang aman. Sebagai pembuktian awal, jika ada pengguna tidak sah mencoba membypass dengan mengetikkan langsung URL dashboard di browser, sistem secara otomatis menolaknya dan langsung me-redirect kembali ke halaman `/admin/login.php` menggunakan session management PHP. Admin harus memasukkan kredensial resmi pada form login ini untuk diverifikasi."
*   **Petunjuk Layar**: *Input username `admin` dan password `admin123` lalu klik Sign In. Setelah masuk, tampilkan Dashboard Utama `/admin/dashboard.php`.*
*   **P1 - Suara Laki-laki A (Bicara)**:
    > "Setelah berhasil login, kita masuk ke halaman **Dashboard Admin** (`dashboard.php`). Halaman ini berfungsi sebagai pusat pantau data operasional toko secara aktual. 
    > 
    > Di bagian atas, terdapat widget informasi total produk, pesanan aktif yang butuh diproses, dan total omzet yang dihitung dinamis menggunakan query agregat SQL. Di tengah, kami merender diagram lingkaran rasio kategori menu menggunakan CSS murni untuk melihat segmentasi produk secara visual. 
    > 
    > Di kanan atas dashboard, terdapat sakelar **Status Operasional Toko** (Open/Closed). Setelan ini menyimpan status toko langsung ke database tabel `settings` dan mengontrol seluruh alur transaksi di sisi pelanggan secara langsung. Saya akan mengeset status operasional ini menjadi **Open / Buka**."

#### **Bagian 2: Kelola Menu (CRUD), Manajemen Pesanan, dan Polling Notifikasi**
*   **Petunjuk Layar**: *Buka halaman produk admin `/admin/produk.php`. Klik tombol "Add New Menu", isi form dummy produk, lalu klik "Save Changes".*
*   **P2 - Suara Perempuan A (Bicara)**:
    > "Halo, saya Natasya. Saya akan menjelaskan bagian pengelolaan data katalog produk dan pesanan. 
    > 
    > Di halaman **Kelola Produk** (`produk.php`), admin memiliki hak akses CRUD penuh. Tabel data produk ini kami lengkapi dengan sistem pagination SQL menggunakan perintah `LIMIT` dan `OFFSET` agar server tidak overload ketika menangani ratusan produk. 
    > 
    > Saat menambahkan menu baru, admin dapat mengunggah gambar. Di balik layar, script PHP melakukan validasi tipe berkas secara ketat menggunakan fungsi `getimagesize()` untuk menolak file non-gambar berbahaya. Sistem kemudian memproses file gambar menggunakan pustaka GD PHP untuk mengompresnya secara otomatis ke format `.webp`. Hal ini menghemat penyimpanan server hingga 80% dan mempercepat loading frontend secara dramatis."
*   **Petunjuk Layar**: *Buka halaman pesanan admin `/admin/pesanan.php`. Tunjukkan tabel pesanan pelanggan berstatus Pending, lalu klik ikon centang hijau untuk memprosesnya menjadi Completed.*
*   **P2 - Suara Perempuan A (Bicara)**:
    > "Selanjutnya adalah halaman **Kelola Pesanan** (`pesanan.php`). Di sini admin mengelola antrean pesanan pelanggan secara teratur. Tabel ini menampilkan detail pesanan, jumlah, catatan khusus, dan status pesanan. Ketika pesanan selesai disiapkan, admin mengklik tombol centang untuk mengubah status pesanan dari *Pending* menjadi *Completed*.
    > 
    > Fitur krusial lainnya di panel admin ini adalah **Sistem Notifikasi Real-time**. Ikon lonceng di pojok kanan atas terhubung ke script `get_notifications.php` via AJAX dengan polling berkala setiap 5 detik. Jika ada pelanggan yang melakukan pemesanan di frontend, lonceng notifikasi admin akan bergoyang secara dinamis dan badge angkanya bertambah tanpa admin perlu me-refresh halaman."

#### **Bagian 3: Tampilan Pelanggan (Frontend), Katalog Interaktif & Alur Transaksi**
*   **Petunjuk Layar**: *Beralih ke tab pelanggan di halaman beranda `index.php`. Scroll perlahan.*
*   **P3 - Suara Laki-laki B (Bicara)**:
    > "Halo, saya Firzan. Sekarang mari kita beralih ke sisi **Pelanggan (Frontend)**. Di halaman utama `index.php`, pelanggan disapa oleh spanduk hero interaktif yang memukau. Di navbar atas, pelanggan dapat melihat lencana status toko aktif, yang saat ini menyala hijau bertuliskan **Buka** berkat setelan admin yang diaktifkan sebelumnya."
*   **Petunjuk Layar**: *Buka halaman produk pelanggan `product.php`. Klik tombol filter 'Bakery' lalu klik filter 'Coffee'.*
*   **P3 - Suara Laki-laki B (Bicara)**:
    > "Di halaman **Katalog Menu** (`product.php`), kami memuat 23 menu café secara responsif dalam tata letak grid. Untuk memberikan pengalaman pengguna yang sangat cepat, kami mengimplementasikan filtrasi menu berbasis manipulasi DOM JavaScript secara langsung. Pelanggan dapat mengklik kategori seperti *Bakery* atau *Coffee*, dan katalog akan tersaring secara instan tanpa loading halaman."
*   **Petunjuk Layar**: *Buka halaman pemesanan `form.php`. Isi form pemesanan secara lengkap, lalu tekan tombol "Order" dan tunjukkan pesan sukses.*
*   **P3 - Suara Laki-laki B (Bicara)**:
    > "Di halaman **Pemesanan** (`form.php`), pelanggan dapat mengirim pesanan secara langsung. Saya akan mengisi Nama, Email, Jumlah, dan Catatan. Saat saya mengklik tombol 'Order', sistem mengirim data via AJAX POST secara asinkron. 
    > 
    > Di saat yang sama, tombol langsung dikunci menjadi abu-abu dan menampilkan teks 'Memproses...'. Fitur **Double Submit Protection** ini mencegah penulisan database ganda jika pelanggan tidak sengaja mengklik tombol kirim berkali-kali. Setelah sukses, Toast notifikasi muncul di kanan bawah dan data pesanan langsung ditambahkan ke tabel riwayat pesanan aktif pelanggan di bagian bawah yang datanya disimpan secara aman menggunakan `sessionStorage` browser."

#### **Bagian 4: Ketangguhan Sistem (Skenario Ekstrem), Keamanan Kode & Penutup**
*   **Petunjuk Layar**: *Kembali ke dashboard admin, matikan toggle status toko menjadi "CLOSED". Beralih ke tab pelanggan, refresh halaman `form.php`.*
*   **P4 - Suara Perempuan B (Bicara)**:
    > "Halo, saya Ratna. Saya akan mendemonstrasikan ketangguhan sistem kami dalam menangani kondisi ekstrem. 
    > 
    > Skenario pertama adalah penutupan toko. Ketika status diubah menjadi *Closed* di panel admin, di sisi pelanggan langsung muncul spanduk pemberitahuan tutup berwarna merah. Jika pelanggan mencoba mengakses `form.php`, formulir pemesanan otomatis terkunci (*disabled*) dan tidak menerima input apa pun untuk menghindari transaksi ilegal."
*   **Petunjuk Layar**: *Matikan servis database MySQL di panel Laragon/XAMPP. Kembali ke tab pelanggan di halaman `product.php` lalu lakukan refresh halaman.*
*   **P4 - Suara Perempuan B (Bicara)**:
    > "Skenario ekstrem kedua adalah kerusakan database atau server mati. Biasanya, jika database MySQL mati, situs PHP akan langsung crash dan menampilkan error bawaan server yang berantakan. Namun di Bake'n Brew, kami merancang sistem failover dengan **Database Resilience**.
    > 
    > Halaman katalog tetap dapat dimuat dengan aman! Sistem mendeteksi matinya database, menampilkan spanduk peringatan kuning bertuliskan *'Koneksi Database Offline'*, lalu otomatis memuat data produk cadangan dari berkas JSON lokal. Di saat yang sama, di sisi panel admin, seluruh tombol penulisan data seperti tambah, edit, dan hapus otomatis dikunci (*disabled*) untuk melindungi data.
    > 
    > Seluruh aplikasi ini dibangun dengan standar keamanan tinggi: bebas dari celah SQL Injection berkat penggunaan *PDO Prepared Statements*, aman dari serangan Cross-Site Scripting berkat sanitasi output menggunakan `htmlspecialchars()`, serta memiliki antarmuka responsif ramah seluler (*mobile-first*). 
    > 
    > Aplikasi web Bake'n Brew saat ini dinyatakan siap rilis secara live di production. Sekian presentasi dari kelompok kami. Terima kasih atas perhatiannya."

---

## 💡 Tips Tambahan untuk Menjawab Pertanyaan Dosen:
1.  **Jika ditanya tentang State Management**: Jawablah bahwa status order aktif pelanggan disimpan menggunakan `sessionStorage` di sisi browser, sehingga data keranjang belanja tetap aman selama tab browser tidak ditutup.
2.  **Jika ditanya tentang Kompresi WebP**: Jawablah bahwa gambar produk yang diunggah dikompresi di sisi server menggunakan pustaka GD PHP (`imagecreatefrompng` / `imagewebp`) untuk mengurangi ukuran file hingga 70-80% tanpa mengurangi kualitas visual secara drastif.
3.  **Jika ditanya tentang Polling Notifikasi**: Jawablah bahwa admin menggunakan fungsi `setInterval()` di JavaScript untuk menembak endpoint `get_notifications.php` setiap 5 detik guna mendeteksi status pesanan baru secara berkala tanpa membebani server secara berlebih.
