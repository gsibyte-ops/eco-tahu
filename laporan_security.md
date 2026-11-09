# Laporan Audit Keamanan (White-Box Testing) - Eco Tahu

## 1. Middleware & Route
- **Temuan**: Semua route administrasi (`/admin/*`) di dalam `routes/web.php` telah dilindungi dengan kombinasi middleware `['auth', 'admin']`. Middleware `admin` terdaftar dengan benar di `bootstrap/app.php` melalui kelas `\App\Http\Middleware\IsAdmin`.
- **Status**: Aman. Tidak ditemukan route admin yang tidak terlindungi.

## 2. Mass Assignment
- **Temuan**: Pada Model `App\Models\User`, kolom `role_id` didefinisikan di dalam properti `$fillable`:
  ```php
  protected $fillable = [
      'role_id', 'username', 'email', 'password', 'alamat', 'no_telepon',
  ];
  ```
- **Risiko**: Jika controller/form request menggunakan `$request->all()` saat membuat atau memperbarui data pengguna tanpa menyaring `role_id`, pengguna berpotensi melakukan eskalasi hak akses (privilege escalation) dengan menyisipkan `role_id` milik admin.
- **Rekomendasi**: 
  - Hapus `role_id` dari `$fillable` pada Model `User`, atau tangani penetapan `role_id` secara eksplisit di dalam controller/service layer (misalnya secara default di-set sebagai regular user).

## 3. SQL Injection
- **Temuan**: Pemeriksaan pada seluruh Controller di `app/Http/Controllers/` menunjukkan bahwa penggunaan `DB::raw()` dan Query Builder (seperti `DashboardController` dan `LaporanController`) hanya digunakan untuk fungsi agregat dan tanggal (misal: `SUM()`, `COUNT()`, `DATE()`) tanpa penggabungan string langsung dari input pengguna (`$request`).
- **Status**: Aman. Tidak ditemukan kerentanan SQL Injection.

## 4. Hardcoded Credentials
- **Temuan**: Pemindaian pada folder `app/` dan `config/` tidak menemukan adanya kredensial sensitif, password, `api_key`, atau `secret` yang ditulis secara hardcoded. Seluruh konfigurasi sensitif menggunakan helper `env()`.
- **Status**: Aman.

## 5. Validasi Form & XSS (Cross-Site Scripting)
- **Temuan**: 
  - Input dari pengguna divalidasi melalui Form Request (`LoginRequest`, `ProfileUpdateRequest`) dan validasi manual di controller.
  - Tampilan Blade menggunakan escape standar `{{ }}`. Penggunaan `{!! !!}` yang ditemukan hanya pada SVG icons statis dan `nl2br(e($artikel->konten))` yang telah di-escape terlebih dahulu menggunakan `e()`.
- **Status**: Aman. Tidak ditemukan kerentanan XSS.
