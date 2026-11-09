<?php

require __DIR__ . '/../bootstrap.php';
use App\KalkulatorTransaksi;
use App\Transaksi;
wajibLogin($auth);

$transaksi = new Transaksi($db, new KalkulatorTransaksi());
$daftar = $transaksi->semua();

$judul = 'Riwayat Transaksi';
require __DIR__ . '/partials/header.php';
?>

<h4 class="mb-3">Riwayat Transaksi</h4>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th class="text-end">Subtotal</th>
                    <th class="text-end">Diskon</th>
                    <th class="text-end">Pajak</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($daftar) === 0): ?>
                <tr><td colspan="7" class="text-center">Belum ada transaksi.</td></tr>
            <?php endif; ?>
            <?php foreach ($daftar as $t): ?>
                <tr>
                    <td>#<?= e($t['id_transaksi']) ?></td>
                    <td><?= e(date('d/m/Y H:i', strtotime($t['tanggal']))) ?></td>
                    <td class="text-end"><?= e(rupiah($t['subtotal'])) ?></td>
                    <td class="text-end"><?= e(rupiah($t['diskon'])) ?></td>
                    <td class="text-end"><?= e(rupiah($t['pajak'])) ?></td>
                    <td class="text-end fw-bold"><?= e(rupiah($t['total'])) ?></td>
                    <td class="text-center">
                        <a href="detail.php?id=<?= e($t['id_transaksi']) ?>"
                           class="btn btn-sm btn-info">Detail</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>