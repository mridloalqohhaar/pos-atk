<?php

require __DIR__ . '/../bootstrap.php';

use App\KalkulatorTransaksi;
use App\Transaksi;

wajibLogin($auth);

$transaksi = new Transaksi($db, new KalkulatorTransaksi());

$id = (int) ($_GET['id'] ?? 0);
$trx = $transaksi->cari($id);

if ($trx === null) {
    header('Location: riwayat.php');
    exit;
}

$items = $transaksi->detail($id);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk #<?= e($id) ?></title>
    <style>
        body {
            font-family: "Courier New", monospace;
            font-size: 13px;
            background: #eee;
            margin: 0;
            padding: 20px;
        }
        .struk {
            width: 300px;
            margin: 0 auto;
            background: #fff;
            padding: 16px;
        }
        .tengah { text-align: center; }
        .garis { border-top: 1px dashed #000; margin: 8px 0; }
        .baris { display: flex; justify-content: space-between; gap: 8px; }
        .tebal { font-weight: bold; }
        .aksi { text-align: center; margin-top: 16px; }
        .aksi a, .aksi button {
            font: inherit;
            padding: 6px 14px;
            margin: 0 4px;
            cursor: pointer;
            text-decoration: none;
            color: #000;
            background: #fff;
            border: 1px solid #000;
            border-radius: 4px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .struk { width: 100%; padding: 0; }
            .aksi { display: none; }
        }
    </style>
</head>
<body>
<div class="struk">
    <div class="tengah">
        <div class="tebal">TOKO ATK</div>
        <div>Alat Tulis &amp; Perlengkapan Kantor</div>
    </div>

    <div class="garis"></div>
    <div class="baris"><span>No</span><span>#<?= e($trx['id_transaksi']) ?></span></div>
    <div class="baris"><span>Tanggal</span><span><?= e(date('d/m/Y H:i', strtotime($trx['tanggal']))) ?></span></div>
    <div class="garis"></div>

    <?php foreach ($items as $i): ?>
        <div><?= e($i['nama_produk']) ?></div>
        <div class="baris">
            <span><?= e($i['qty']) ?> x <?= e(number_format((float) $i['harga_satuan'], 0, ',', '.')) ?></span>
            <span><?= e(number_format((float) $i['subtotal'], 0, ',', '.')) ?></span>
        </div>
    <?php endforeach; ?>

    <div class="garis"></div>
    <div class="baris"><span>Subtotal</span><span><?= e(rupiah($trx['subtotal'])) ?></span></div>
    <div class="baris"><span>Diskon</span><span>- <?= e(rupiah($trx['diskon'])) ?></span></div>
    <div class="baris"><span>Pajak 10%</span><span><?= e(rupiah($trx['pajak'])) ?></span></div>
    <div class="garis"></div>
    <div class="baris tebal"><span>TOTAL</span><span><?= e(rupiah($trx['total'])) ?></span></div>
    <div class="garis"></div>

    <div class="tengah">Terima kasih sudah berbelanja!</div>
</div>

<div class="aksi">
    <button onclick="window.print()">Cetak</button>
    <a href="detail.php?id=<?= e($trx['id_transaksi']) ?>">Kembali</a>
</div>
</body>
</html>