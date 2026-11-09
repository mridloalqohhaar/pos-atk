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

<h4 class="mb-3">Transaksi #<?= e($trx['id_transaksi']) ?></h4>

<?php if ($sukses): ?>
    <div class="alert alert-success">Transaksi berhasil disimpan.</div>
<?php endif; ?>

<p class="text-muted">Tanggal: <?= e(date('d/m/Y H:i', strtotime($trx['tanggal']))) ?></p>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-dark">
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
            <tfoot>
                <tr><td colspan="3" class="text-end">Subtotal</td>
                    <td class="text-end"><?= e(rupiah($trx['subtotal'])) ?></td></tr>
                <tr><td colspan="3" class="text-end">Diskon</td>
                    <td class="text-end">- <?= e(rupiah($trx['diskon'])) ?></td></tr>
                <tr><td colspan="3" class="text-end">Pajak 10%</td>
                    <td class="text-end"><?= e(rupiah($trx['pajak'])) ?></td></tr>
                <tr class="table-primary fw-bold"><td colspan="3" class="text-end">TOTAL</td>
                    <td class="text-end"><?= e(rupiah($trx['total'])) ?></td></tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mt-3">
    <a href="kasir.php" class="btn btn-primary">Transaksi Baru</a>
    <a href="riwayat.php" class="btn btn-secondary">Ke Riwayat</a>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>