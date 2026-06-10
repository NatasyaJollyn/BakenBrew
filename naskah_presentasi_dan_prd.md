# Ringkasan PRD & Naskah Dialog Presentasi EAS – Bake'n Brew

Dokumen ini menggabungkan ringkasan fungsionalitas teknis (PRD) dari seluruh sistem **Bake'n Brew** serta **Naskah Dialog Presentasi** terstruktur (untuk individu maupun kelompok) agar dapat digunakan sebagai showcase portofolio profesional secara publik (misalnya untuk LinkedIn).

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
    *   *Dashboard Ringkasan*: Statistik total produk, total pesanan, status operasional toko (Buka/Tutup) dengan sakelar toggle cepat, bagan rasio kategori menu (Chart.js), serta daftar pesanan terbaru di bagian kanan bawah.
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

## 🗣️ Bagian 2: Naskah Dialog & Alur Presentasi Live (Durasi: ~7-9 Menit)

> [!TIP]
> **Rekomendasi Suara Google AI Studio (Multi-Voice)**:
> Sangat disarankan menggunakan **4 jenis suara AI yang berbeda** (2 suara Cowok dan 2 suara Cewek) agar mencerminkan kontribusi masing-masing anggota kelompok secara dinamis.
> Pada naskah di bawah, setiap bagian dialog telah dilengkapi dengan **Petunjuk Nada & Intonasi** agar hasil konversi text-to-speech tidak terdengar datar.

### 🎭 Pembagian Peran & Rekomendasi Suara AI:
1.  **Presenter 1 (P1 - Suara Cowok 1)**: Menyapa pemirsa, mengenalkan tim secara umum, mendemokan login admin, dashboard analitik, dan status operasional. *(Karakter suara: Tenang, berwibawa, dan profesional)*
2.  **Presenter 2 (P2 - Suara Cewek 1)**: Menjelaskan proses CRUD produk (WebP + GD library), kelola status pesanan, serta notifikasi real-time (polling). *(Karakter suara: Jelas, antusias, dan ramah)*
3.  **Presenter 3 (P3 - Suara Cowok 2)**: Beranda pelanggan, katalog interaktif dengan filter JS, pengisian form order, AJAX submit, dan Double Submit Protection. *(Karakter suara: Tegas, dinamis, dan meyakinkan)*
4.  **Presenter 4 (P4 - Suara Cewek 2)**: Demonstrasi skenario tutup toko, failover database offline (mock data), ringkasan keamanan kode (SQLi/XSS), tombol logout, dan penutup. *(Karakter suara: Serius, mantap, dan ekspresif)*

---

### 🎬 Naskah Percakapan & Petunjuk Layar (Admin-First & Detail Halaman)

#### **Bagian 1: Pembukaan, Login & Dashboard Admin**
*   **Petunjuk Layar**: *Coba ketik langsung URL `/admin/dashboard.php` di browser saat belum login, untuk menunjukkan sistem menolak bypass dan langsung me-redirect kembali ke `/admin/login.php`. Setelah itu, kembalikan ke tampilan halaman login admin `/admin/login.php`.*
*   **P1 - Suara Cowok 1**:
    1. `[Ramah & Sopan]` Halo rekan-rekan sekalian, selamat datang dalam sesi demo produk aplikasi web Bake'n Brew. 
    2. `[Bersemangat & Lantang]` Kami sangat antusias untuk mempresentasikan sekaligus mendemokan sistem pemesanan online kopi dan roti premium ini secara langsung. 
    3. `[Tegas & Menarik Perhatian]` Berbeda dari demo biasa, kami akan memulai presentasi langsung dari pusat kendali sistem, yaitu **Admin Panel atau Backend**.
    4. `[Serius & Profesional]` Untuk menjaga keamanan data operasional, kami mengimplementasikan sistem autentikasi login yang aman. 
    5. `[Informatif & Tenang]` Sebagai pembuktian awal, jika ada pengguna tidak sah mencoba membypass dengan mengetikkan langsung URL dashboard di browser, sistem secara otomatis menolaknya dan langsung me-redirect kembali ke halaman `/admin/login.php` menggunakan session management PHP. 
    6. `[Tegas & Meyakinkan]` Admin harus memasukkan kredensial resmi pada form login ini untuk diverifikasi.
*   **Petunjuk Layar**: *Input username `admin` dan password `admin123` lalu klik Sign In. Setelah masuk, tampilkan Dashboard Utama `/admin/dashboard.php`.*
*   **P1 - Suara Cowok 1**:
    1. `[Senang & Informatif]` Setelah berhasil login, kita masuk ke halaman **Dashboard Admin** (`dashboard.php`). 
    2. `[Tenang & Jelas]` Halaman ini berfungsi sebagai pusat pantau data operasional toko secara aktual. 
    3. `[Informatif & Detail]` Di bagian atas, terdapat dua kartu informasi utama, yaitu **Total Menu** sebanyak 23 produk dan **Total Pesanan** sebanyak 4 pesanan, yang dihitung secara dinamis dari database. 
    4. `[Menarik Perhatian]` Di sebelah kanan atas, terdapat sakelar **Status Operasional Toko** (Buka/Tutup) untuk mengaktifkan atau menonaktifkan transaksi pelanggan secara realtime. 
    5. `[Fokus & Menjelaskan]` Di bagian kiri bawah, kami merender diagram lingkaran **Komposisi Menu** menggunakan pustaka Chart.js untuk melihat perbandingan kategori produk Bakery, Coffee, dan Non-Coffee secara visual. 
    6. `[Informatif & Jelas]` Sementara di bagian kanan bawah, terdapat tabel **Pesanan Terbaru** yang menyajikan antrean order pelanggan lengkap dengan status transaksi mereka. 
    7. `[Tegas & Mantap]` Saya akan mengaktifkan sakelar status operasional ini menjadi **Buka**.



#### **Bagian 2: Kelola Menu (CRUD), Manajemen Pesanan, dan Polling Notifikasi**
*   **Petunjuk Layar**: *Buka halaman produk admin `/admin/produk.php`. Klik tombol "Add New Menu", isi form dummy produk, lalu klik "Save Changes".*
*   **P2 - Suara Cewek 1**:
    1. `[Ceria & Jelas]` Sekarang saya akan menjelaskan bagian pengelolaan data katalog produk dan pesanan. 
    2. `[Mantap & Ramah]` Di halaman **Kelola Produk** (`produk.php`), admin memiliki hak akses CRUD penuh. 
    3. `[Informatif & Profesional]` Tabel data produk ini kami lengkapi dengan sistem pagination SQL menggunakan perintah `LIMIT` dan `OFFSET` agar server tidak overload ketika menangani ratusan produk. 
    4. `[Tenang & Jelas]` Saat menambahkan menu baru, admin dapat mengunggah gambar. 
    5. `[Serius & Tegas]` Di balik layar, script PHP melakukan validasi tipe berkas secara ketat menggunakan fungsi `getimagesize()` untuk menolak file non-gambar berbahaya. 
    6. `[Antusias & Menjelaskan]` Sistem kemudian memproses file gambar menggunakan pustaka GD PHP untuk mengompresnya secara otomatis ke format `.webp`. 
    7. `[Lantang & Bersemangat]` Hal ini menghemat penyimpanan server hingga 80% dan mempercepat loading frontend secara dramatis.
*   **Petunjuk Layar**: *Buka halaman pesanan admin `/admin/pesanan.php`. Tunjukkan tabel pesanan pelanggan berstatus Pending, lalu klik ikon centang hijau untuk memprosesnya menjadi Completed.*
*   **P2 - Suara Cewek 1**:
    1. `[Ceria & Transisi]` Selanjutnya adalah halaman **Kelola Pesanan** (`pesanan.php`). 
    2. `[Tenang & Jelas]` Di sini admin mengelola antrean pesanan pelanggan secara teratur. 
    3. `[Informatif & Detail]` Tabel ini menampilkan detail pesanan, jumlah, catatan khusus, dan status pesanan. 
    4. `[Tegas & Mantap]` Ketika pesanan selesai disiapkan, admin mengklik tombol centang untuk mengubah status pesanan dari *Pending* menjadi *Completed*. 
    5. `[Antusias & Menarik Perhatian]` Fitur krusial lainnya di panel admin ini adalah **Sistem Notifikasi Real-time**. 
    6. `[Informatif & Jelas]` Ikon lonceng di pojok kanan atas terhubung ke script `get_notifications.php` via AJAX dengan polling berkala setiap 5 detik. 
    7. `[Lantang & Bersemangat]` Jika ada pelanggan yang melakukan pemesanan di frontend, lonceng notifikasi admin akan bergoyang secara dinamis dan badge angkanya bertambah tanpa admin perlu me-refresh halaman.

#### **Bagian 3: Tampilan Pelanggan (Frontend), Katalog Interaktif & Alur Transaksi**
*   **Petunjuk Layar**: *Beralih ke tab pelanggan di halaman beranda `index.php`. Scroll perlahan.*
*   **P3 - Suara Cowok 2**:
    1. `[Ramah & Ceria]` Setelah melihat pusat kendali admin, kini kita beralih ke sisi **Pelanggan atau Frontend**. 
    2. `[Antusias & Hangat]` Di halaman utama `index.php`, pelanggan disapa oleh spanduk hero interaktif yang memukau. 
    3. `[Informatif & Jelas]` Di navbar atas, pelanggan dapat melihat lencana status toko aktif, yang saat ini menyala hijau bertuliskan **Buka** berkat setelan admin yang diaktifkan sebelumnya.
*   **Petunjuk Layar**: *Buka halaman produk pelanggan `product.php`. Klik tombol filter 'Bakery' lalu klik filter 'Coffee'.*
*   **P3 - Suara Cowok 2**:
    1. `[Tenang & Profesional]` Di halaman **Katalog Menu** (`product.php`), kami memuat 23 menu café secara responsif dalam tata letak grid. 
    2. `[Antusias & Menjelaskan]` Untuk memberikan pengalaman pengguna yang sangat cepat, kami mengimplementasikan filtrasi menu berbasis manipulasi DOM JavaScript secara langsung. 
    3. `[Lantang & Ramah]` Pelanggan dapat mengklik kategori seperti *Bakery* atau *Coffee*, dan katalog akan tersaring secara instan tanpa loading halaman.
*   **Petunjuk Layar**: *Buka halaman pemesanan `form.php`. Isi form pemesanan secara lengkap, lalu tekan tombol "Order" dan tunjukkan pesan sukses.*
*   **P3 - Suara Cowok 2**:
    1. `[Ceria & Transisi]` Di halaman **Pemesanan** (`form.php`), pelanggan dapat mengirim pesanan secara langsung. 
    2. `[Tenang & Jelas]` Saya akan mengisi Nama, Email, Jumlah, dan Catatan. 
    3. `[Fokus & Menjelaskan]` Saat saya mengklik tombol 'Order', sistem mengirim data via AJAX POST secara asinkron. 
    4. `[Tegas & Menarik Perhatian]` Di saat yang sama, tombol langsung dikunci menjadi abu-abu dan menampilkan teks 'Memproses...'. 
    5. `[Serius & Meyakinkan]` Fitur **Double Submit Protection** ini mencegah penulisan database ganda jika pelanggan tidak sengaja mengklik tombol kirim berkali-kali. 
    6. `[Lantang, Senang & Bersemangat]` Setelah sukses, Toast notifikasi muncul di kanan bawah dan data pesanan langsung ditambahkan ke tabel riwayat pesanan aktif pelanggan di bagian bawah yang datanya disimpan secara aman menggunakan `sessionStorage` browser.

#### **Bagian 4: Ketangguhan Sistem (Skenario Ekstrem), Keamanan Kode & Penutup**
*   **Petunjuk Layar**: *Kembali ke dashboard admin, matikan toggle status toko menjadi "CLOSED". Beralih ke tab pelanggan, refresh halaman `form.php`.*
*   **P4 - Suara Cewek 2**:
    1. `[Serius & Profesional]` Saya akan mendemonstrasikan ketangguhan sistem kami dalam menangani kondisi ekstrem. 
    2. `[Tegas & Jelas]` Skenario pertama adalah penutupan toko. 
    3. `[Informatif & Serius]` Ketika status diubah menjadi *Closed* di panel admin, di sisi pelanggan langsung muncul spanduk pemberitahuan tutup berwarna merah. 
    4. `[Tegas & Meyakinkan]` Jika pelanggan mencoba mengakses `form.php`, formulir pemesanan otomatis terkunci (*disabled*) dan tidak menerima input apa pun untuk menghindari transaksi ilegal.
*   **Petunjuk Layar**: *Matikan servis database MySQL di panel Laragon/XAMPP. Kembali ke tab pelanggan di halaman `product.php` lalu lakukan refresh halaman.*
*   **P4 - Suara Cewek 2**:
    1. `[Serius & Transisi]` Skenario ekstrem kedua adalah kerusakan database atau server mati. 
    2. `[Khawatir & Menjelaskan]` Biasanya, jika database MySQL mati, situs PHP akan langsung crash dan menampilkan error bawaan server yang berantakan. 
    3. `[Bangga & Lantang]` Namun di Bake'n Brew, kami merancang sistem failover dengan **Database Resilience**. 
    4. `[Antusias & Bersemangat]` Halaman katalog tetap dapat dimuat dengan aman! 
    5. `[Informatif & Jelas]` Sistem mendeteksi matinya database, menampilkan spanduk peringatan kuning bertuliskan *'Koneksi Database Offline'*, lalu otomatis memuat data produk cadangan dari berkas JSON lokal. 
    6. `[Tegas & Detail]` Di saat yang sama, di sisi panel admin, seluruh tombol penulisan data seperti tambah, edit, dan hapus otomatis dikunci (*disabled*) untuk melindungi data. 
    7. `[Profesional & Meyakinkan]` Seluruh aplikasi ini dibangun dengan standar keamanan tinggi: bebas dari celah SQL Injection berkat penggunaan *PDO Prepared Statements*, aman dari serangan Cross-Site Scripting berkat sanitasi output menggunakan `htmlspecialchars()`, serta memiliki antarmuka responsif ramah seluler (*mobile-first*).
*   **Petunjuk Layar**: *Nyalakan kembali database MySQL di Laragon. Kembali ke tab admin, klik tombol dropdown profil di pojok kanan atas, lalu klik **Logout**. Sistem akan secara otomatis me-redirect kembali ke halaman login `/admin/login.php`.*
*   **P4 - Suara Cewek 2**:
    1. `[Ceria & Transisi]` Terakhir, sebagai penutup alur keamanan admin, kami menyediakan fitur **Logout**. 
    2. `[Tenang & Jelas]` Saat tombol ini diklik, sistem akan menghapus seluruh data sesi (session) di server untuk memastikan hak akses admin dinonaktifkan secara aman. 
    3. `[Lantang & Bersemangat]` Kesimpulannya, website Bake'n Brew ini siap dirilis secara live di production. 
    4. `[Ramah & Sopan]` Sekian presentasi produk dari tim kami. 
    5. `[Senang, Lantang & Penuh Terima Kasih]` Terima kasih atas perhatiannya.

---

## 💡 Tips Tambahan untuk Menjawab Pertanyaan Pemirsa / Klien:
1.  **Jika ditanya tentang State Management**: Jawablah bahwa status order aktif pelanggan disimpan menggunakan `sessionStorage` di sisi browser, sehingga data keranjang belanja tetap aman selama tab browser tidak ditutup.
2.  **Jika ditanya tentang Kompresi WebP**: Jawablah bahwa gambar produk yang diunggah dikompresi di sisi server menggunakan pustaka GD PHP (`imagecreatefrompng` / `imagewebp`) untuk mengurangi ukuran file hingga 70-80% tanpa mengurangi kualitas visual secara drastif.
3.  **Jika ditanya tentang Polling Notifikasi**: Jawablah bahwa admin menggunakan fungsi `setInterval()` di JavaScript untuk menembak endpoint `get_notifications.php` setiap 5 detik guna mendeteksi status pesanan baru secara berkala tanpa membebani server secara berlebih.
