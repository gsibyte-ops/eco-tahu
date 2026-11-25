# Panduan Pengguna (User Guide) â€” Aplikasi Web & Mobile EcoTahu

Panduan lengkap cara mengakses dan menggunakan ekosistem **EcoTahu** (Website & Aplikasi Android) untuk **Pengunjung (Guest)**, **Pelanggan (User Biasa)**, dan **Administrator (Admin)**.

---

## 1. Cara Akses Website & Instalasi Aplikasi Android

### A. Akses Website (Browser Laptop / HP)
Buka browser (Chrome, Brave, Edge, atau Safari), lalu kunjungi alamat berikut (**pastikan TIDAK ada tanda titik `.` di akhir alamat**):

- **Website Utama:** https://ecotahu.wstif-polije.com
- **Halaman Login / Daftar:** https://ecotahu.wstif-polije.com/login
- **Katalog Produk Tahu:** https://ecotahu.wstif-polije.com/produk
- **Katalog Produk Limbah (Okara):** https://ecotahu.wstif-polije.com/limbah
- **Resep Olahan Tahu:** https://ecotahu.wstif-polije.com/resep
- **Edukasi Lingkungan:** https://ecotahu.wstif-polije.com/edukasi

### B. Download & Install Aplikasi Android (APK Resmi v1.0.0)
1. Buka halaman rilis di HP Android: https://github.com/gsibyte-ops/eco-tahu-mobile/releases/tag/v1.0.0
2. Pilih file APK sesuai perangkat:
   - **HP Android modern 64-bit (Rekomendasi â€” 22,66 MB):** [EcoTahu-v1.0.0-arm64-v8a.apk](https://github.com/gsibyte-ops/eco-tahu-mobile/releases/download/v1.0.0/EcoTahu-v1.0.0-arm64-v8a.apk)
   - **HP Android lawas 32-bit (20,13 MB):** [EcoTahu-v1.0.0-armeabi-v7a.apk](https://github.com/gsibyte-ops/eco-tahu-mobile/releases/download/v1.0.0/EcoTahu-v1.0.0-armeabi-v7a.apk)
3. Setelah selesai diunduh, ketuk file `.apk`. Jika muncul peringatan *"Sumber tidak dikenal / Install unknown apps"*, aktifkan izin untuk browser/File Manager Anda lalu ketuk **Install**.
4. Buka aplikasi **EcoTahu** â€” aplikasi sudah otomatis terhubung aman (HTTPS) ke server produksi dan langsung sinkron dengan Website!

### C. Akun Uji Coba / Demo
| Peran | Email | Password | Catatan |
| :--- | :--- | :--- | :--- |
| **Pelanggan (Demo)** | `budi@test.com` | `password` | Bisa langsung dipakai simulasi belanja, keranjang, checkout, & ulasan |
| **Administrator** | `admin@ecotahu.id` | *(gunakan password admin aktif)* | Akses penuh Dashboard Admin di Web & mode pantau Read-Only di Mobile |
| **Pelanggan Baru** | Daftar bebas / Login Google | Minimal 8 karakter | Klik **Daftar** atau **Lanjutkan dengan Google** |

---

## 2. Panduan Pengunjung (Guest â€” Tanpa Login)

Pengunjung yang belum memiliki akun tetap bisa menjelajahi informasi dan katalog produk secara lengkap:

1. **Beranda:** Melihat pengenalan produk tahu organik & ramah lingkungan, ringkasan ulasan pelanggan, produk unggulan, serta fitur ganti tema **Mode Gelap / Terang** (ikon bulan/matahari di navbar).
2. **Eksplorasi Katalog Produk Tahu & Limbah:**
   - Buka menu **Produk Tahu** atau **Produk Limbah** (ampas tahu basah, kering, fermentasi).
   - Gunakan kotak pencarian, filter kategori, dan pengurutan harga.
   - Klik salah satu produk untuk melihat detail deskripsi, harga, stok tersedia, dan ulasan pembeli sebelumnya.
3. **Membaca Resep & Edukasi:**
   - Buka menu **Resep** untuk melihat bahan, langkah memasak, tingkat kesulitan, dan estimasi waktu.
   - Buka menu **Edukasi** untuk membaca artikel pengelolaan limbah & proses higienis beserta video tutorial YouTube.
4. **Pusat Informasi:** Mengakses halaman *Cara Pesan*, *Pengiriman*, *Kebijakan Refund*, *FAQ*, *Bantuan*, *Syarat & Ketentuan*, dan *Kebijakan Privasi* di footer website.
5. **Mendaftar Akun:** Saat mencoba menekan tombol **Tambah ke Keranjang**, **Like**, atau **Checkout**, sistem akan mengarahkan pengunjung ke halaman **Login / Daftar** (bisa registrasi manual atau 1-klik **Lanjutkan dengan Google**).

---

## 3. Panduan Pengguna Biasa (Pelanggan â€” Web & Mobile)

Setelah masuk menggunakan akun pelanggan, seluruh aktivitas belanja tersinkronisasi antara **Website** dan **Aplikasi Android**:

### A. Autentikasi & Profil
- **Masuk / Daftar:** Melalui Email & Password (minimal 8 karakter) atau **Lanjutkan dengan Google**.
- **Lupa Password:** Klik **Lupa password?**, masukkan email terdaftar, lalu cek inbox Gmail untuk menerima link dan kode reset password.
- **Kelola Profil (`/profile`):** Perbarui foto profil (JPG/PNG/WEBP maks 2 MB), nama, nomor HP, alamat, ganti password, atau hapus akun permanen.

### B. Belanja & Checkout
1. **Keranjang Belanja:** Tambahkan produk tahu atau produk limbah ke keranjang, atur jumlah item, atau hapus item. Keranjang tersimpan di database sehingga tidak hilang saat berpindah perangkat (Web â†” HP).
2. **Proses Checkout:**
   - Isi **Nama Penerima**, **Nomor HP**, dan **Alamat Lengkap**.
   - Pilih metode pengiriman:
     - **Ambil di Tempat (Pickup):** Tanpa ongkir dan tanpa minimal belanja.
     - **Diantar (Delivery):** Minimal belanja Rp 15.000, pilih kecamatan atau tandai titik koordinat di peta â€” jarak (Haversine) dan ongkir dihitung otomatis oleh sistem (radius maksimal 10 km).
   - **Promo Pesanan Pertama:** Diskon otomatis terpasang bila akun & nomor HP memenuhi syarat pesanan pertama.
   - Pilih metode pembayaran: **COD (Bayar di Tempat)** atau **Transfer Bank (BCA / BRI / Mandiri / BNI)** yang menghasilkan nomor Virtual Account dan batas waktu 24 jam.
3. **Upload Bukti Transfer:** Untuk metode Transfer, unggah foto bukti transfer pada halaman sukses checkout atau detail pesanan agar segera diverifikasi Admin.

### C. Lacak Pesanan, Refund, & Ulasan
- **Pesanan Saya (`/pesanan`):** Memantau status pesanan secara bertahap: `Pending` â†’ `Diproses` â†’ `Dikirim` â†’ `Selesai` (atau `Dibatalkan`).
- **Live Tracking Kurir:** Saat pesanan berstatus `Dikirim`, buka detail pesanan untuk melihat posisi kurir secara langsung di peta.
- **Pembatalan & Refund:** Pesanan berstatus `Pending` atau `Diproses` dapat dibatalkan dengan mengisi alasan pembatalan (dan data rekening bank bila sudah dibayar via Transfer untuk pengajuan refund).
- **Ulasan & Interaksi:** Setelah pesanan berstatus `Selesai`, pelanggan dapat memberi **rating bintang (1â€“5)**, komentar, serta melampirkan **foto atau video ulasan**. Pelanggan juga bisa menyukai (**Like**) artikel Edukasi dan Resep.
- **Notifikasi:** Klik ikon **lonceng** di pojok kanan atas untuk melihat update status pesanan & pembayaran.

---

## 4. Panduan Administrator (Admin)

### A. Dashboard Web Admin (`/admin/dashboard`)
Login menggunakan akun Admin (`admin@ecotahu.id`) di website akan otomatis mengarahkan Anda ke panel **Admin EcoTahu**:

1. **Dashboard & Notifikasi:**
   - Melihat ringkasan statistik pendapatan, pesanan masuk, pengajuan refund tertunda, dan peringatan stok menipis (`<= 10`).
   - Ikon lonceng admin menampilkan notifikasi real-time setiap ada pesanan baru, unggahan bukti transfer, atau pembatalan.
2. **Manajemen Katalog & Konten (CRUD):**
   - **Kategori:** Tambah, ubah, dan hapus kategori produk.
   - **Produk Tahu & Produk Limbah:** Kelola nama, harga, stok, deskripsi, dan foto produk.
   - **Edukasi & Resep:** Publikasikan artikel edukasi lingkungan, resep olahan tahu, thumbnail, serta tautan video YouTube resmi.
3. **Manajemen Pesanan & Kurir:**
   - **Verifikasi Pembayaran:** Periksa foto bukti transfer pelanggan lalu setujui pembayaran menjadi lunas (`paid`).
   - **Update Status Pesanan:** Ubah alur pesanan (`pending` â†’ `diproses` â†’ `dikirim` â†’ `selesai`) atau batalkan pesanan bila diperlukan.
   - **Tracking Kurir:** Aktifkan **Mulai Tracking**, perbarui lokasi koordinat kurir selama pengantaran, lalu klik **Hentikan Tracking** setelah pesanan sampai.
4. **Manajemen Refund:** Tinjau detail pembatalan, nominal pengembalian, serta rekening tujuan pelanggan, lalu perbarui status pengembalian dana.
5. **Data Pelanggan & Laporan Penjualan:**
   - **Pelanggan:** Melihat daftar seluruh pelanggan terdaftar, riwayat transaksi per pelanggan, dan **Export CSV** (sudah dilengkapi proteksi anti formula injection).
   - **Laporan Penjualan:** Analisis pendapatan dengan filter rentang tanggal (grafik harian otomatis beralih ke bulanan untuk rentang > 31 hari) beserta fitur **Export Laporan**.

### B. Mode Admin di Aplikasi Android (Read-Only Monitoring)
Jika akun Admin login melalui aplikasi **EcoTahu Android**, aplikasi secara otomatis masuk ke **Mode Pemantauan Admin (Read-Only)**:
- Admin dapat memantau ringkasan operasional (`/api/admin/overview`) langsung dari HP.
- Seluruh aksi khusus pelanggan (keranjang, checkout, pembatalan, ulasan, like, dan hapus akun) otomatis diblokir oleh middleware keamanan server (`no-admin-customer-action`) demi menjaga integritas data transaksi.

---

## 5. Skenario Demo Cepat 5 Menit (Siap Presentasi)

1. **Menit 1 â€” Tampilan Guest (Web):** Buka `https://ecotahu.wstif-polije.com` â†’ tunjukkan Beranda, toggle Dark/Light Mode, Katalog Produk Tahu, Produk Limbah, Resep, dan Edukasi.
2. **Menit 2 â€” Login & Belanja Pelanggan (HP Android / Web):** Login sebagai `budi@test.com` (atau pakai **Lanjutkan dengan Google**) â†’ tambah 2 produk ke keranjang â†’ tunjukkan sinkronisasi keranjang antara Web & Aplikasi Android.
3. **Menit 3 â€” Checkout & Upload Bukti:** Lakukan checkout metode Transfer (tunjukkan kalkulasi jarak & ongkir otomatis) â†’ unggah foto bukti transfer.
4. **Menit 4 â€” Panel Admin (Web):** Login sebagai `admin@ecotahu.id` di browser berbeda/Incognito â†’ tunjukkan notifikasi pesanan baru â†’ verifikasi bukti transfer â†’ ubah status pesanan ke `Diproses` â†’ `Dikirim` â†’ `Selesai` â†’ buka grafik **Laporan** & **Data Pelanggan**.
5. **Menit 5 â€” Penutup & Keamanan:** Kembali ke akun pelanggan â†’ lihat status pesanan sudah `Selesai` â†’ kirim ulasan produk, lalu tutup dengan menyoroti keamanan produksi (HTTPS penuh, TiDB Cloud TLS, Enkripsi Token Android Keystore AES-256, Bcrypt, & verifikasi OAuth Google).

---

> Untuk dokumentasi teknis instalasi lokal, arsitektur sistem, dan deployment cloud, lihat [README_PENGEMBANG.md](./README_PENGEMBANG.md).

