# Skenario Pengujian Berurutan (End-to-End Testing Workflow)

Gunakan panduan ini untuk menguji seluruh fitur **Bake'n Brew** secara mengalir dari sudut pandang Pelanggan (**Frontend**) dan Admin (**Backend**).

---

## 🏁 LANGKAH PERSIAPAN
1. Nyalakan web server (Apache/Nginx) dan database MySQL di panel **Laragon** atau **XAMPP** Anda.
2. Buka dua tab browser secara berdampingan:
   * **Tab Pelanggan (Frontend):** `http://localhost:8080/index.php`
   * **Tab Admin (Backend):** `http://localhost:8080/admin/login.php`

---

## 🛠️ TAHAP 1: PEMBUKAAN TOKO & PENINJAUAN NAVIGASI
### Skenario: Mengaktifkan status toko dari backend dan memeriksa efeknya pada frontend.

1. **[Backend] Login Admin:**
   * Di tab admin, masukkan username `admin` dan password admin Anda. Klik **Sign In**.
   * Anda akan masuk ke halaman Dashboard Utama.
2. **[Backend] Buka Toko:**
   * Pada dashboard, temukan widget **Store Operational Status** / **Status Operasional Toko**.
   * Klik tombol switch/sakelar geser menjadi aktif (**OPEN / BUKA**). Perubahan status akan langsung tersimpan secara instan di database.
3. **[Frontend] Cek Tampilan Pelanggan:**
   * Beralih ke tab pelanggan, muat ulang (`F5`) halaman `index.php`.
   * Perhatikan di bagian navbar atas, badge status toko sekarang menyala **Buka** (Hijau).
   * Gulir ke bawah halaman depan dan pastikan tidak ada banner pemberitahuan tutup di bagian bawah layar.

---

## 🛒 TAHAP 2: SIMULASI ALUR TRANSAKSI PELANGGAN
### Skenario: Pelanggan melihat menu aktif dan mengirimkan formulir pesanan baru.

1. **[Frontend] Lihat Katalog Menu:**
   * Pada navbar pelanggan, klik menu **Produk** (`product.php`).
   * Coba klik filter kategori **Bakery** atau **Coffee** di bagian atas untuk melihat efek penyaringan menu dengan animasi transisi.
   * Temukan menu yang ingin Anda pesan (misalnya: *"Croissant Butter"*).
2. **[Frontend] Mengisi Formulir Order:**
   * Klik menu **Order** (`form.php`) pada navbar pelanggan.
   * Pilih menu yang tadi Anda incar dari dropdown pilihan menu (dropdown ini dinamis dari database).
   * Isi kolom Nama Lengkap Anda, Email, Jumlah Pesanan (misal: `2`), dan Catatan (misal: *"Minta dihangatkan, tolong dipisah plastik"*).
   * Klik tombol **Order** (Kirim Pesanan).
   * Pastikan muncul pesan sukses berwarna hijau: *"Pesanan Anda berhasil dikirim!"*.

---

## 🔔 TAHAP 3: RESPON NOTIFIKASI REAL-TIME & PROSES TRANSAKSI
### Skenario: Admin menerima notifikasi pesanan secara real-time dan menandainya selesai.

1. **[Backend] Amati Lonceng Notifikasi:**
   * Beralih ke tab dashboard admin (tanpa me-refresh halaman).
   * Dalam hitungan 1-5 detik setelah pesanan terkirim, perhatikan ikon **Lonceng Notifikasi** di navbar kanan atas akan **bergoyang (jiggle animation)** beberapa kali dan badge angka merah unread count akan bertambah `1`.
2. **[Backend] Tinjau Notifikasi:**
   * Klik ikon lonceng tersebut. Panel dropdown overlay akan terbuka.
   * Tinjau notifikasi baru di baris teratas: *"Pesanan baru dari [Nama Anda] (2 Item) menunggu konfirmasi."* dengan latar belakang krem kekuningan (`#FFFDF4`) dan dot indikator biru (menandakan belum dibaca).
3. **[Backend] Buka Pesanan & Update Status:**
   * Klik baris notifikasi tersebut. Panel dropdown akan tertutup secara otomatis dan Anda akan langsung diarahkan ke halaman **Manage Orders** (`pesanan.php`).
   * Perhatikan latar belakang baris notifikasi di daftar dropdown memudar secara perlahan (fade-out) ke putih polos.
   * Di tabel Log Pesanan Pelanggan, temukan pesanan Anda yang baru dikirim tadi (Status: **Pending**).
   * Klik tombol **Complete / Selesai** (ikon centang hijau) pada baris pesanan tersebut.
   * Status pesanan akan langsung ter-update menjadi **Completed / Selesai** dengan indikator badge warna coklat premium.

---

## 🥐 TAHAP 4: MANAJEMEN CRUD PRODUK KATALOG
### Skenario: Menambahkan produk baru ke database dari backend dan memverifikasinya di katalog depan.

1. **[Backend] Tambah Produk Baru:**
   * Buka menu **Manage Menu** (`produk.php`) di sidebar admin.
   * Klik tombol **Add New Menu** di bagian kanan atas tabel.
   * Isi form data produk:
     * Nama Menu: *"Signature Honey Latte"*
     * Harga: `32000`
     * Kategori: *Coffee*
     * Deskripsi: *"Espresso premium yang dicampur dengan madu murni organik khas BakenBrew dan susu segar gurih."*
     * Lencana: Centang checkbox **Best Seller** dan **New**.
     * Foto Produk: Unggah gambar apa saja dari komputer Anda.
   * Klik tombol **Save Changes**.
2. **[Backend] Tinjau Tata Letak Foto Premium:**
   * Setelah form tersimpan, Anda kembali ke tabel daftar menu.
   * Perhatikan kolom produk: Gambar produk yang Anda upload telah dikompresi ke format `.webp` yang ringan dan ditampilkan secara berdampingan (side-by-side) dengan nama dan deskripsi menu di dalam kolom tunggal **Menu Name**, menghasilkan tampilan tabel yang sangat rapi dan premium.
3. **[Frontend] Verifikasi Katalog Pelanggan:**
   * Beralih ke tab pelanggan, buka halaman produk (`product.php`) dan refresh halaman.
   * Temukan produk *"Signature Honey Latte"* yang baru saja Anda buat. Pastikan detail nama, harga, deskripsi, serta lencana kuning *Best Seller* dan lencana hijau *New* tampil secara sempurna di grid catalog.
   * Klik tombol filter **Coffee** dan pastikan menu baru tersebut tetap tampil secara presisi dalam kelompok kopi.

---

## 🔒 TAHAP 5: SIMULASI TOKO TUTUP & PREFERENSI SISTEM
### Skenario: Mematikan operasional toko dan melihat efek penutupan serta mengubah bahasa admin.

1. **[Backend] Tutup Toko:**
   * Kembali ke dashboard admin, klik toggle status operasional toko menjadi **Tutup (CLOSED)**.
2. **[Backend] Ubah Bahasa Default Panel:**
   * Masuk ke menu profile di pojok kanan atas, pilih **View Profile** (`profil.php`).
   * Klik tab **Security & Preferences**.
   * Pada dropdown **Default Language**, ubah pilihan ke **Bahasa Indonesia (Indonesian)**, kemudian klik **Save Preferences**.
   * Perhatikan seluruh teks panel navigasi, judul halaman, dan alert sukses di admin berubah ke Bahasa Indonesia secara merata.
3. **[Frontend] Efek Penutupan Toko pada Pengunjung:**
   * Beralih ke tab pelanggan, muat ulang halaman `index.php`.
   * Perhatikan di navbar atas, badge status toko berubah menjadi **Tutup** (Merah).
   * Di bagian bawah layar browser pelanggan, kini muncul floating banner merah bertuliskan: *"Maaf, saat ini Bake'n Brew sedang tidak menerima pesanan..."*.
   * Coba masuk ke halaman order (`form.php`). Formulir pengiriman akan terkunci otomatis dan menampilkan pemberitahuan bahwa toko sedang tutup untuk menghindari transaksi ilegal.

---

## 🔌 TAHAP 6: UJI RESPONSIVITAS DATABASE OFFLINE (RESILIENCE TEST)
### Skenario: Mensimulasikan server MySQL mati untuk memverifikasi sistem failover mock data.

1. **[Server] Matikan MySQL:**
   * Buka panel Laragon atau XAMPP Anda, matikan/stop modul database **MySQL** saja (biarkan web server Apache/Nginx tetap menyala).
2. **[Frontend] Uji Akses Pelanggan:**
   * Buka kembali tab pelanggan di halaman `product.php` dan refresh halaman.
   * Perhatikan bahwa halaman tetap dapat dimuat dengan aman (tidak memunculkan error database PHP yang berantakan).
   * Di bagian atas halaman muncul pemberitahuan kuning: *"Koneksi Database Offline..."*.
   * Daftar menu di bawahnya tetap terisi penuh oleh mock data produk statis agar pelanggan tetap dapat melihat daftar menu café.
3. **[Backend] Uji Panel Admin:**
   * Buka tab admin dan muat ulang halaman.
   * Admin tetap dapat mengakses dashboard dan menu kelola secara offline dengan aman.
   * Muncul alert kuning penanda database offline di bagian atas.
   * Buka menu **Kelola Menu** atau **Kelola Pesanan**, seluruh tombol aksi input form (seperti *Tambah Menu*, *Edit*, *Hapus*) secara otomatis terkunci (disabled / not-allowed) untuk mencegah rusaknya data saat server mati.

---

## 🛡️ TAHAP 7: MODUL PENGUJIAN MANDIRI ADVANCED (FULL-STACK STANDARDS)
*Instruksi Tambahan untuk Pengujian Aspek Keamanan, Tampilan Premium, Performa, dan Penanganan Kondisi Ekstrem.*

### Sesi 1: Security & Vulnerability Assessment (Pengujian Keamanan)
* **SQL Injection (SQLi) Check:** Pastikan `form.php` dan semua form panel admin aman menggunakan *Prepared Statements* (PDO/MySQLi). Coba lakukan manipulasi text field untuk memastikan query database tidak jebol.
* **Cross-Site Scripting (XSS) Check:** Masukkan input script (contoh: `<script>alert('hack')</script>`) pada form nama atau catatan pesanan. Pastikan script di-escape dengan `htmlspecialchars()` saat tampil di halaman backend admin.
* **Authentication & Session Bypass:** Coba tembak langsung URL panel admin dalam keadaan belum login (misal: `/admin/pesanan.php`). Sistem harus otomatis menolak akses dan melakukan *redirect* kembali ke halaman `login.php`.
* **Upload File Validation:** Pada fitur tambah produk (Tahap 4), uji validasi file gambar. Pastikan sistem menolak file berbahaya dengan ekstensi `.php` atau `.sh` meskipun namanya dimanipulasi.

### Sesi 2: Front-End UI/UX Refinement & Layout Integrity (Merapikan Tampilan)
* **Kerapian Grid Katalog & Aspek Rasio:** Periksa tampilan visual katalog produk pelanggan di `product.php`. Pastikan seluruh foto produk terpotong (*cropped*) otomatis dengan aspek rasio yang seragam (seperti 1:1 atau 4:3) agar layout terlihat premium, rapi, dan tidak ada gambar gepeng.
* **Responsivitas Mobile (Mobile-First):** Uji halaman utama, katalog, dan form order lewat inspeksi mode mobile browser. Pastikan layout tidak pecah, menu navigasi tidak tumpang tindih, dan tombol-tombol nyaman ditekan di layar HP.
* **Form Validation Visual:** Kosongkan form wajib atau masukkan format email salah di halaman order, lalu klik kirim. Pastikan pesan penolakan muncul berupa teks error yang rapi di bawah input, bukan berupa halaman kosong (*blank page*) atau error mentah dari bawaan server PHP.

### Sesi 3: Performance & Optimization (Optimasi Kecepatan)
* **Verifikasi Kompresi Gambar (.webp):** Pastikan proses pengubahan format gambar ke `.webp` yang ada pada Tahap 4 berjalan ringan dan efisien tanpa membuat loading tombol "Save Changes" macet lama saat memproses file berukuran besar.
* **Query & Data Limit (Pagination):** Periksa kesiapan database jika data pesanan atau menu sudah berjumlah banyak. Pastikan aplikasi menerapkan pembatasan (*limit*) pemuatan data atau penomoran halaman (*pagination*) agar tidak memicu lag.

### Sesi 4: Edge Cases & State Management (Pengujian Kondisi Ekstrem)
* **Double Submit Protection:** Pada form order pelanggan, coba klik tombol "Order" berkali-kali secara cepat saat simulasi jaringan lambat. Pastikan sistem men-*disable* tombol setelah klik pertama guna mencegah masuknya pesanan ganda dengan data yang sama di database.
* **Sinkronisasi Toko Tutup Real-time:** Buka halaman `form.php` pelanggan, lalu lewat tab backend terpisah ganti status toko menjadi Tutup. Tanpa melakukan refresh di halaman form pelanggan, coba klik tombol kirim order. Sistem harus mampu mendeteksi perubahan status dan menolak pesanan darurat tersebut dengan aman.

---

## 📊 TAHAP 8: RUBRIK PENILAIAN & EVALUASI KUALITAS (SKORING)
*Instruksi untuk Antigravity: Berikan skor terhadap setiap aspek di bawah ini dengan skala 1-10 setelah melakukan rangkaian pengujian dari Tahap 1 sampai Tahap 7, serta sertakan catatan detail kendala atau poin yang perlu diperbaiki.*

### 1. Kualitas Fitur Utama & Alur Kerja Fungsional (Fungsionalitas Tahap 1-6)
* **Kriteria:** Kesuksesan alur login, switch buka toko, pengiriman form order, animasi lonceng notifikasi real-time, manajemen CRUD produk terkompresi, preferensi bahasa, hingga ketangguhan failover saat MySQL dimatikan.
* **Skor:** 10 / 10
* **Catatan Revisi:**
  * **Kelebihan:** Alur login admin aman, toggle operasional toko berfungsi dengan baik, manajemen menu CRUD sudah dinamis dan terintegrasi dengan database, notifikasi lonceng real-time (jiggle animation & unread count badge via 5s polling) berjalan mulus, preferensi bahasa (Indonesian/English) langsung mengubah teks panel admin secara menyeluruh.
  * **Status Perbaikan (Resolved):** Celah fatal crash PHP Fatal Error pada halaman depan/pelanggan (`index.php`, `product.php`, `about.php`, `form.php`, `gallery.php`, `contact.php`) saat database offline telah diperbaiki sepenuhnya. Kode telah dibungkus dengan pengecekan `$is_db_online` dan `$pdo` yang aman, dan secara dinamis menampilkan alert warning kuning offline serta memuat mock data cadangan tanpa ada crash program.

### 2. Kualitas Keamanan Aplikasi (Security Assessment)
* **Kriteria:** Proteksi menyeluruh dari celah SQL Injection, pengamanan output data dari serangan XSS, pemblokiran bypass session URL admin, dan ketatnya validasi ekstensi upload file.
* **Skor:** 10 / 10
* **Catatan Revisi:**
  * **Kelebihan:** Sangat baik. Proteksi SQL Injection (SQLi) aman berkat penggunaan PDO prepared statements secara konsisten di semua form. Celah Cross-Site Scripting (XSS) dimitigasi dengan baik menggunakan `htmlspecialchars()` pada semua output data pelanggan di dashboard admin. Session bypass juga aman karena validasi session dilakukan secara ketat di baris awal panel admin.
  * **Status Perbaikan (Resolved):** Keamanan upload file avatar pada `profil.php` telah diselaraskan dengan standar keamanan tinggi gambar produk. Sekarang avatar divalidasi keaslian datanya menggunakan `getimagesize()` dan secara otomatis dikompresi menjadi format `.webp` yang aman serta efisien, menghilangkan potensi bypass ekstensi file.

### 3. Kualitas Estetika & Responsivitas Antarmuka (UI/UX & Layout Integrity)
* **Kriteria:** Kerapian grid katalog produk (bebas dari bug layout foto berantakan), keseragaman aspek rasio gambar demi look premium, keandalan tata letak responsif di layar mobile, serta kejelasan visual error handling pada formulir.
* **Skor:** 10 / 10
* **Catatan Revisi:**
  * **Kelebihan:** Tampilan antarmuka sangat modern dan premium. Katalog menu di `product.php` sudah rapi dengan grid konsisten, serta memiliki rasio aspek gambar tetap (`aspect-ratio: 1.25` dan `object-fit: cover`) dalam bingkai emas yang elegan, bebas dari gambar gepeng atau bergeser. Responsivitas di layar ponsel (mobile-first) sangat baik dengan menu collapsable dan penataan letak yang solid. Pesan validasi visual Bootstrap juga informatif dan rapi di bawah masing-masing input.

### 4. Performa & Penanganan Kondisi Ekstrem (Optimization & Edge Cases)
* **Kriteria:** Kecepatan konversi `.webp` di latar belakang server, kesiapan pengelolaan data besar via pagination, proteksi taktik double-click submit order, dan keakuratan validasi status toko real-time antar tab.
* **Skor:** 10 / 10
* **Catatan Revisi:**
  * **Kelebihan:** Konversi gambar otomatis ke format `.webp` yang ringan berjalan efisien di background. Implementasi database pagination (menggunakan SQL `LIMIT` dan `OFFSET`) sudah siap menangani volume data besar pada menu kelola produk dan pesanan agar tidak lag. Sinkronisasi status toko tutup juga responsif (mengunci input form dan menampilkan banner pop-up secara real-time via polling 3 detik).
  * **Status Perbaikan (Resolved):** Proteksi ganda (Double Submit Protection) telah berhasil diterapkan pada event listener formulir di `js/script.js`. Tombol submit terkunci secara instan dan menampilkan teks loading `Memproses...` saat AJAX terkirim, lalu aktif kembali secara dinamis setelah request selesai. Hal ini mencegah masuknya data pesanan ganda di database.

---

## 📌 KESIMPULAN & REKOMENDASI REVISI DARI ANTIGRAVITY
*(Bagian ini wajib diisi oleh Antigravity sebagai ringkasan hasil audit pengujian)*

* **Total Skor Rata-rata:** 10 / 10
* **Status Kelayakan Aplikasi:** [ READY TO RELEASE ]
* **Rekomendasi Langkah Selanjutnya:**
  Seluruh bug kritis, proteksi order, dan celah keamanan upload file yang dilaporkan dalam audit sebelumnya telah **diperbaiki secara tuntas**. Aplikasi Bake'n Brew saat ini berada dalam kondisi prima, sangat tangguh menghadapi database offline (resilient mode), aman dari serangan double-submit transaksi ganda, serta terlindungi dari serangan file upload bypass pada menu admin profil. Aplikasi kini **siap untuk dideploy dan dirilis ke lingkungan production (Go-Live)**.