# Product Requirement Document (PRD)

## Admin Panel & Real-Time CRUD – BakenBrew

---

### 1. Ringkasan Proyek (Overview)

BakenBrew adalah platform web café & bakery modern berbasis *front-end* yang responsif. Dokumen ini dirancang untuk mengimplementasikan **Admin Panel** sebagai pusat kendali data (*back-end*). Dengan memanfaatkan server lokal **Laragon**, sisi admin ini akan memungkinkan pengelolaan data secara dinamis melalui operasi CRUD (Create, Read, Update, Delete) yang langsung tersinkronisasi secara *real-time* dengan halaman pengguna (*user-facing website*).

### 2. Tujuan Utama (Objectives)

* **Sentralisasi Manajemen Konten:** Mengubah halaman katalog produk statis menjadi dinamis yang dikendalikan penuh dari dashboard admin tanpa menyentuh kode program.
* **Integrasi Data Pesanan:** Memindahkan penyimpanan data formulir pesanan dari `sessionStorage` (statis/sementara) ke database MySQL (permanen).
* **Autentikasi Hak Akses:** Memastikan halaman manajemen data hanya dapat diakses oleh admin yang terverifikasi melalui sistem *session login*.

---

### 3. Kebutuhan Fungsional (Functional Requirements)

| Modul | Fitur / Fungsi | Deskripsi | Prioritas |
| --- | --- | --- | --- |
| **Autentikasi** | Login Admin | Halaman khusus admin untuk masuk menggunakan *username* & *password* yang terdaftar di database. | **High** |
|  | Logout | Memutus sesi login (*destroy session*) dan mengarahkan kembali ke halaman login. | **High** |
| **Dashboard** | Statistik Ringkas | Menampilkan *Information Cards*: Total Menu Aktif, Total Pesanan, Menu Terpopuler, dan Status Stok. | **Medium** |
|  | Kontrol Cepat | *Toggle Switch* untuk mengubah status operasional toko (Buka/Tutup) di halaman user secara instan. | **Medium** |
|  | Grafik Visual | Diagram lingkaran (*Pie Chart*) menggunakan **Chart.js** untuk komposisi menu (Kopi vs Bakery). | **Low** |
| **CRUD Produk** | **C**reate Product | Form tambah menu baru (Nama, Harga, Deskripsi, Foto Produk, Kategori: *Bakery/Coffee/Non-Coffee*). | **High** |
|  | **R**ead Product | Menampilkan tabel list produk dari database lengkap dengan pagination dan pencarian. | **High** |
|  | **U**pdate Product | Mengedit informasi produk atau mengganti file gambar produk yang sudah ada. | **High** |
|  | **D**elete Product | Menghapus produk dari database beserta file gambarnya di folder lokal. | **High** |
| **Manajemen Pesanan** | Read & Update | Menampilkan daftar pesanan masuk dari formulir `form.php` dan tombol aksi untuk menandai pesanan selesai. | **High** |

---

### 4. Arsitektur Teknis & Lingkungan (Technical Stack)

* **Bahasa Pemrograman:** PHP (Native / Framework)
* **Environment Server:** Laragon (Apache/Nginx & MySQL)
* **Database:** MySQL (diakses via phpMyAdmin / HeidiSQL bawaan Laragon)
* **Library Tambahan:** Chart.js (via CDN untuk visualisasi grafik di dashboard)

---

### 5. Panduan Visual & UI Konseptual

Untuk menjaga keselarasan dengan tema **"Warm Cozy"** asli dari BakenBrew, halaman admin wajib menerapkan elemen visual berikut:

* **Palet Warna Utama:** * Sidebar Navigasi: Coklat Tua (`#4B2E2B`)
* Background Konten: Cream (`#F5E6D3`) atau Putih Gading
* Aksen Tombol & Kartu: Beige (`#D6BFAF`)


* **Tipografi:** Judul komponen menggunakan font **Playfair Display**, sedangkan teks tabel/form menggunakan **Poppins**.
* **Tata Letak Layout:** Menggunakan standard *Admin Layout* berupa Sidebar di sisi kiri (Navigasi: Dashboard, Kelola Menu, Kelola Pesanan, Logout) dan Main Content Area di sisi kanan.

---

### 6. Kriteria Keberhasilan (Acceptance Criteria)

1. Hak akses halaman `/admin/*` terkunci rapat. Jika belum login, user biasa otomatis dialihkan (*redirect*) ke halaman `login.php`.
2. Saat admin melakukan penambahan, perubahan, atau penghapusan menu di Admin Panel, halaman katalog `product.php` milik user harus **langsung menampilkan perubahan tersebut** setelah di-refresh.
3. Mengunggah gambar produk baru harus otomatis tersimpan ke folder lokal project (`public/images/products/`) dan menghapus file gambar lama jika produk tersebut dihapus dari database.

---

### 7. Batasan Ruang Lingkup Kerja (Scope & Out of Scope)

> ⚠️ **PENTING: JANGAN MENGUBAH HALAMAN YANG SUDAH JADI**

* **In Scope (Fokus Utama Pengembangan):**
* Pembuatan database `db_bakenbrew` di MySQL Laragon.
* Pembuatan folder baru khusus admin (`/admin`) beserta seluruh file sistem logikanya.
* Mengubah ekstensi file utama user dari `.html` menjadi `.php` semata-mata agar kode PHP dapat dieksekusi untuk membaca data dari database.
* Menghubungkan form di `form.php` agar melakukan `INSERT` data ke tabel pesanan di MySQL.


* **Out of Scope (TIDAK BOLEH Diotak-atik):**
* **Dilarang keras mengubah UI/UX, tata letak grid, desain responsive, warna, maupun custom CSS styling** pada halaman depan publik user (`index`, `about`, `gallery`, `contact`) yang sudah dibuat sebelumnya.
* Tidak diperkenankan melakukan perombakan atau penambahan fitur di sisi user di luar jalur sinkronisasi database.



---

### 8. Rencana Struktur File & Folder Terbaru

Struktur repositori akan dikembangkan secara rapi tanpa mengganggu file aset *front-end* yang sudah ada:

```text
bakenbrew/
├── index.php         → (Ekstensi diubah ke .php, layout tetap utuh)
├── product.php       → (Menampilkan 13 menu dinamis dari database)
├── form.php          → (Mengirim data pesanan langsung ke MySQL)
├── about.php         
├── gallery.php       
├── contact.php       
├── css/              → (Aset CSS asli - DILARANG DIUBAH)
├── js/               → (Logika JS asli - Hanya sesuaikan bagian form handling)
│
├── config/           → 📂 FOLDER BARU (Konfigurasi Sistem)
│   └── koneksi.php   → Script PHP koneksi database Laragon
│
└── admin/            → 📂 FOLDER BARU (Sistem Backend Admin)
    ├── login.php     → Form login admin (Autentikasi)
    ├── logout.php    → Sesi keluar (Destroy session)
    ├── dashboard.php → Tampilan utama (Widgets statistik & Chart.js)
    ├── produk.php    → Manajemen CRUD produk (Tabel menu & form aksi)
    └── pesanan.php   → Manajemen log pesanan masuk dari pelanggan

```