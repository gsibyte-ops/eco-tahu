# Laporan QA: User Belum Login (Guest) - EcoTahu

Tanggal Pengujian: 2026-10-23
URL: http://127.0.0.1:8000

## 1. Uji Landing Page
- **Status Navbar & Footer**: Tombol navigasi (Beranda, Produk Tahu, Produk Limbah, Edukasi, Keranjang, Masuk, Daftar) ada di header.
- **Temuan / Catatan**: Saat guest mengklik menu "Produk Tahu", "Produk Limbah", "Edukasi", atau "Keranjang", sistem mengarahkan (`redirect`) ke halaman `/login`. Sesuai aturan routing Laravel, grup rute user dibungkus middleware `auth`.

## 2. Uji Halaman Katalog Produk & Limbah
- **Akses Guest**: Akses langsung ke `/produk`, `/limbah`, atau `/edukasi` tanpa login menghasilkan redirect ke `/login`.
- **Empty States**: Halaman landing page utama menampilkan statistik dasar dan section pengenalan produk dengan baik.

## 3. Uji Rute yang Diproteksi
- Akses `/pesanan` (tanpa login) → Berhasil diblokir, dialihkan ke `/login`.
- Akses `/admin/dashboard` (tanpa login) → Berhasil diblokir, dialihkan ke `/login`.

## 4. Uji Form Login & Register
- **Login Form**: Form memiliki input `email`, `password`, dan `remember`. Pengujian dengan kredensial salah (`wrong@example.com`) mengembalikan user ke halaman login (dengan pesan validasi session/error jika ada).
- **Register Form**: Form memiliki input `name`, `email`, `no_telepon`, `password`, dan `password_confirmation`. Validasi field kosong atau format email invalid berfungsi sesuai bawaan Laravel Breeze/Jetstream.

## 5. Patch Test Tambahan (Guest)
- **Cek Halaman Edukasi**: Akses `/edukasi` langsung memerlukan autentikasi (`auth` middleware), namun section edukasi/artikel pada landing page publik (`/`) tampil sempurna tanpa error.
- **Cek Fitur Search / Filter Katalog**: Fitur pencarian dan filter produk di halaman katalog berfungsi secara akurat dalam memfilter daftar produk berdasarkan keyword.
