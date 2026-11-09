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

## Log bug
| Tanggal | Bug | Penyebab | Perbaikan |
|---|---|---|---|