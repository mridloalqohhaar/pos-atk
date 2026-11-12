# Catatan Proses POS Toko ATK

## Keputusan
- Tema: POS Toko ATK (data sederhana, mudah dibayangkan)
- Teknologi: PHP native OOP + PDO. Alasan: lebih mudah dijelaskan ke penguji
  dibanding framework besar, setup cepat, dan logika terlihat jelas.
- Pajak dihitung SETELAH diskon, sesuai contoh slide (58.000 → 57.420).
- Tier diskon: lihat KalkulatorTransaksi.php (perlu konfirmasi instruktur).

## Kebutuhan singkat
- Fitur wajib: CRUD produk, transaksi + hitung subtotal/diskon/pajak,
  simpan transaksi, riwayat.
- Fitur penting: login sederhana.
- Opsional: dashboard.


## Day 4 (nilai tambah)
- Pencarian produk (LIKE + prepared statement).
- Filter riwayat per tanggal + total omzet terfilter.
- Cetak struk (struk.php) dengan CSS @media print.
- Struk memakai harga_satuan dari detail_transaksi.


## Day 5 (redesign UI)
- Design token dan komponen terpusat di public/assets/css/app.css.
- Layout sidebar + topbar; sidebar berubah menjadi bottom bar di mobile.
- Kasir: grid produk, pencarian client-side, panel keranjang, stepper qty.
- Login, dashboard, produk, riwayat, dan detail memakai komponen yang sama.
- Aksesibilitas: skip link, aria-label, role alert/status, focus-visible, rel noopener.
- struk.php tidak diubah karena sudah memiliki CSS print sendiri.



## Log bug
| Tanggal | Bug | Penyebab | Perbaikan |
|---|---|---|---|