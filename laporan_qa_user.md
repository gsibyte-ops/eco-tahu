# Laporan QA: User Login (`budi@test.com`) - EcoTahu

Tanggal Pengujian: 2026-10-23
User: Budi (`budi@test.com`)

## 1. Fitur Keranjang (Cart)
- **Aksi**: Menambahkan produk ke keranjang dari halaman katalog.
- **Temuan**: Sesi keranjang berfungsi. Saat keranjang kosong, halaman menampilkan status kosong yang informatif ("Keranjang Kosong") dengan tombol navigasi kembali belanja.

## 2. Fitur Checkout
- **Aksi**: Mengakses halaman `/checkout` saat keranjang kosong mengarahkan kembali ke halaman keranjang dengan pesan session error.
- **Form Checkout**: Memuat ringkasan pesanan, pilihan kurir, dan input alamat pengiriman.

## 3. Halaman Profil
- **Aksi**: Mengakses `/profile`.
- **Temuan**: Form update informasi profil dan ganti password tersedia dan berfungsi dengan validasi standar Laravel Breeze.

## 4. Riwayat Pesanan (`/pesanan`)
- **Aksi**: Mengakses halaman `/pesanan`.
- **Temuan**: Daftar pesanan user muncul lengkap dengan status (Pending, Diproses, Dikirim, Selesai, Dibatalkan), metode pembayaran, jarak, total harga, tombol "Lihat Detail", dan tombol "Batal" (untuk pesanan pending). Data pesanan sebelumnya (`#ETI26102114211717`, `#ETI26101721285134`) tampil dengan baik tanpa kerusakan layout.

## 5. Patch Test Tambahan (User)
- **Cek Halaman Edukasi (Auth)**: Mengakses `/edukasi` saat login menampilkan daftar artikel/video edukasi secara lengkap dan interaktif tanpa error.
- **Cek Keranjang Belanja**: Menambahkan produk ke keranjang, mengubah kuantiti menjadi `5`, dan melakukan kalkulasi ulang total harga berfungsi dengan akurat (dikalikan 5).
- **Cek Tombol Batal Pesanan**: Tombol pembatalan pada pesanan berstatus 'Pending/Diproses' berfungsi dengan baik untuk mengubah status pesanan menjadi Batal.
