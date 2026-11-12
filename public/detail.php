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
$sukses = ($_GET['pesan'] ?? '') === 'sukses';

$judul = 'Detail Transaksi #' . $id;
require __DIR__ . '/partials/header.php';
?>

<div class="page-head">
    <div>
        <h2 class="page-title">Transaksi #<?= e($trx['id_transaksi']) ?></h2>
        <p class="page-subtitle">
            <?= e(date('d/m/Y H:i', strtotime($trx['tanggal']))) ?>
        </p>
    </div>
    <a href="riwayat.php" class="btn btn-outline-secondary">
        <i class="bi bi-clock-history" aria-hidden="true"></i> Riwayat
    </a>
</div>

<?php if ($sukses): ?>
    <div class="alert alert-success" role="status">Transaksi berhasil disimpan.</div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Rincian Item</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $i): ?>
                        <tr>
                            <td><?= e($i['nama_produk']) ?></td>
                            <td class="text-end"><?= e(rupiah($i['harga_satuan'])) ?></td>
                            <td class="text-center"><?= e($i['qty']) ?></td>
                            <td class="text-end"><?= e(rupiah($i['subtotal'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Ringkasan Pembayaran</div>
            <div class="card-body">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span><?= e(rupiah($trx['subtotal'])) ?></span>
                </div>
                <div class="summary-row">
                    <span>Diskon</span>
                    <span>- <?= e(rupiah($trx['diskon'])) ?></span>
                </div>
                <div class="summary-row">
                    <span>Pajak 10%</span>
                    <span><?= e(rupiah($trx['pajak'])) ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total Akhir</span>
                    <span><?= e(rupiah($trx['total'])) ?></span>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <a href="struk.php?id=<?= e($trx['id_transaksi']) ?>" target="_blank"
                       rel="noopener"
                       class="btn btn-dark">
                        <i class="bi bi-printer" aria-hidden="true"></i> Cetak Struk
                    </a>
                    <a href="kasir.php" class="btn btn-primary">Transaksi Baru</a>
                    <a href="riwayat.php" class="btn btn-outline-secondary">Ke Riwayat</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
