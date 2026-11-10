# POS Toko ATK

Aplikasi kasir (Point of Sale) sederhana untuk toko alat tulis, dibuat dengan
PHP native berbasis OOP. Dikerjakan sebagai project UKK.

## Fitur

- Login dan logout (password di-hash), semua halaman dilindungi
- Dashboard: transaksi dan omzet hari ini, total produk, stok menipis, produk terlaris
- CRUD produk dengan validasi dan pencarian
- Kasir: keranjang, subtotal, diskon bertingkat, pajak 10%, cek stok
- Simpan transaksi (database transaction) dan kurangi stok otomatis
- Riwayat transaksi, filter tanggal, total omzet, detail, dan cetak struk
- Proteksi CSRF pada semua form
- Unit test dengan PHPUnit

## Teknologi

PHP 8.2+, MySQL (Laragon + phpMyAdmin), PDO, Bootstrap 5 dan Bootstrap Icons (CDN),
PHPUnit, Composer, Git.

## Cara instalasi

1. Pastikan PHP 8.2+, Composer, dan MySQL tersedia (misalnya lewat Laragon).
2. Unduh atau clone project ini, lalu di folder project jalankan:
   ```
   composer install
   ```
3. Buka phpMyAdmin, pilih **Import**, dan impor file `database.sql`.
   Database `pos_atk` akan dibuat otomatis.
   Peringatan: mengimpor ulang akan menghapus semua data lama.
4. Sesuaikan `config/database.php` jika user atau password MySQL berbeda.
5. Buat akun admin (sekali saja):
   ```
   php tools/buat_admin.php
   ```
6. Jalankan aplikasi:
   ```
   php -S localhost:8000 -t public
   ```
7. Buka http://localhost:8000

## Akun demo

| Username | Password |
|---|---|
| admin | admin123 |

Akun ini hanya untuk latihan. Ganti jika dipakai di luar lingkungan belajar.

## Menjalankan tes

```
vendor\bin\phpunit
```

## Rumus perhitungan

```
subtotal = Σ (harga × qty)
diskon   = subtotal × persentase_tier
pajak    = (subtotal − diskon) × 10%
total    = subtotal − diskon + pajak
```

| Subtotal | Diskon |
|---|---|
| < 25.000 | 0% |
| 25.000 – 49.999 | 5% |
| 50.000 – 99.999 | 10% |
| ≥ 100.000 | 15% |

Contoh: subtotal 58.000 → diskon 5.800 → pajak 5.220 → total 57.420.

## Struktur folder

```
pos-atk/
├── config/database.php        konfigurasi database
├── src/                       class: Database, Produk, Transaksi, Keranjang,
│                              KalkulatorTransaksi, Pengguna, Auth, Csrf
├── public/                    halaman web (login, dashboard, produk, kasir, riwayat, struk)
├── tests/                     unit test PHPUnit
├── tools/buat_admin.php       skrip membuat akun admin
├── bootstrap.php              inisialisasi, session, koneksi, fungsi bantu
├── database.sql               skema dan data contoh
└── CATATAN_PROSES.md          catatan keputusan dan log bug
```

## Catatan

- Gambar latar: ilustrasi SVG buatan sendiri (`public/img/latar.svg`).
- Ikon: Bootstrap Icons.
- Dibuat oleh: Ridlo