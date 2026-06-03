
---

# Product Requirement Document (PRD) - Revisi Website Bake N Brew

## 1. Informasi Dokumen

* **Nama Proyek:** Revisi Website Bake N Brew (Panel Admin & Sisi Pengguna)
* **Pemilik Produk (Product Owner):** Firzan Syaroni
* **Tanggal Pembuatan:** 3 Juni 2026
* **Status Dokumen:** Draft - Siap Didiskusikan

---

## 2. Latar Belakang & Tujuan

* **Latar Belakang:** Website Bake N Brew saat ini memerlukan pembenahan pada beberapa aspek struktural, fungsional, dan visual, baik di sisi back-office (Panel Admin) maupun di sisi depan (Tampilan Pengguna).
* **Tujuan Utama:** Revisi ini bertujuan untuk menyederhanakan antarmuka admin dengan menghapus elemen non-fungsional, meningkatkan estetika UI agar lebih premium, serta memastikan integritas data pesanan dan sinkronisasi status operasional toko antara admin dan pelanggan berjalan secara *real-time* serta terisolasi dengan aman per perangkat.

---

## 3. Ruang Lingkup Revisi (Scope of Work)

* **In-Scope (Akan Dikerjakan):**
* Pembersihan UI Halaman Dashboard Admin dengan menghapus komponen yang tidak esensial.
* Pembaruan komponen global tombol "Logout" di seluruh halaman Admin agar lebih estetik dan premium.
* Perancangan mekanisme sinkronisasi status operasional toko ("Buka/Tutup") dari Admin ke Tampilan Pengguna.
* Penerapan sistem penanganan sesi pesanan (*session/device isolation*) pada menu order sisi pengguna agar data pesanan antar user tidak bercampur.
* Konsolidasi seluruh log pesanan dari berbagai pengguna agar dapat dipantau secara terpusat oleh Admin.


* **Out-of-Scope (Tidak Dikerjakan):**
* Pembuatan aplikasi mobile native (iOS/Android).
* Perubahan total skema warna dasar atau *rebranding* identitas visual Bake N Brew.



---

## 4. Kebutuhan Fungsional: Panel Admin (Admin Dashboard)

| Halaman / Modul | Komponen Semula | Deskripsi Perubahan / Revisi |
| --- | --- | --- |
| **Dashboard Utama** | Card "Menu Terpopuler" (menampilkan Croissant Butter). | Dihapus sepenuhnya dari antarmuka untuk mengurangi kepadatan informasi (*clutter*) karena dinilai tidak berfungsi jika ditinjau dari sisi bisnis dan visual. |
| **Komponen Global Admin** | Tombol "Logout" berupa teks merah seadanya di bawah judul halaman. | Didesain ulang menjadi elemen UI yang lebih premium, elegan, dan menyatu dengan tema hangat dari Bake N Brew (misal: diubah menjadi button berikon estetik di area profil atau sidebar). |
| **Kelola Pesanan** | Log Pesanan Pelanggan terpusat. | Dipastikan harus mampu menarik dan menampilkan seluruh data pesanan masuk dari setiap user secara komprehensif tanpa ada data yang terlewat atau tercampur. |

---

## 5. Kebutuhan Fungsional: Sisi Pengguna (User/Client Side)

* **Indikator Status Operasional Toko:** Informasi dari toggle "STATUS OPERASIONAL" (BUKA/TUTUP) di sisi admin wajib diteruskan ke tampilan user. Sistem harus menampilkan banner pemberitahuan atau badge status di halaman utama/landing page user. Jika status menunjukkan toko "TUTUP", tombol atau aksi pemesanan (order menu) di sisi pengguna secara otomatis dinonaktifkan (*disabled*) dengan pesan edukatif yang jelas.
* **Isolasi Sesi Pemesanan (Device/User Isolation):** Untuk mencegah kebingungan antar pelanggan, sistem wajib memisahkan data aktivitas pemesanan. Ketika User 1 dan User 2 mengakses menu order secara bersamaan dari device yang berbeda, tampilan keranjang dan status pesanan pribadi mereka harus benar-benar terisolasi. Data pesanan User 1 tidak boleh bocor atau tampil di layar User 2. Pemisahan ini dapat diimplementasikan menggunakan manajemen session, local storage yang unik, atau sistem login/autentikasi akun.

---

## 6. Poin Validasi & Diskusi Teknikal (Agenda dengan Developer / Antigravity)

Bagian ini berisi daftar pertanyaan krusial yang perlu divalidasi langsung dengan tim pengembang untuk memastikan kelayakan teknis:

1. **Status Kelola Menu:** Apakah data daftar menu yang tampil pada tabel saat ini masih bersifat statis (*hardcoded*) atau sudah dinamis terhubung dengan database operasional?
2. **Integrasi Backend User:** Apakah alur pemesanan pada tampilan user menu order saat ini sudah sepenuhnya terintegrasi dengan backend, ataukah masih berupa mockup/prototipe frontend saja?
3. **Sinkronisasi Real-Time Dashboard:** Apakah tabel "Pesanan Terbaru" pada halaman Dashboard Admin sudah dirancang untuk menerima pembaruan secara *real-time* (misal menggunakan WebSocket atau polling) begitu user menekan tombol order, atau masih memerlukan refresh manual?
4. **Mekanisme Hubungan Admin-User:** Bagaimana arsitektur penanganan status toko Buka/Tutup agar perubahannya langsung berdampak instan pada aksesibilitas menu order di sisi klien?

---

## 7. Kebutuhan Non-Fungsional (Non-Functional Requirements)

* **Keamanan & Privasi Data:** Kebocoran data pesanan antar user harus berada pada tingkat 0% melalui pembatasan akses session yang ketat.
* **Kemudahan Penggunaan (UX):** Perubahan status toko dan proses checkout user harus intuitif tanpa menambah langkah (*friction*) yang tidak perlu bagi pelanggan.

---