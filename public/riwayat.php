<?php

require __DIR__ . '/../bootstrap.php';

use App\KalkulatorTransaksi;
use App\Transaksi;

wajibLogin($auth);

/**
 * Memastikan teks berformat tanggal Y-m-d yang valid, selain itu dianggap kosong.
 */
function tanggalValid(string $teks): ?string
{
    $tgl = DateTime::createFromFormat('Y-m-d', $teks);

    return ($tgl !== false && $tgl->format('Y-m-d') === $teks) ? $teks : null;
}

$dari = tanggalValid(trim($_GET['dari'] ?? ''));
$sampai = tanggalValid(trim($_GET['sampai'] ?? ''));

$transaksi = new Transaksi($db, new KalkulatorTransaksi());
$daftar = $transaksi->semua($dari, $sampai);
$totalOmzet = array_sum(array_column($daftar, 'total'));
$difilter = $dari !== null || $sampai !== null;

$judul = 'Riwayat Transaksi';
require __DIR__ . '/partials/header.php';
?>

<h4 class="mb-3"><i class="bi bi-clock-history"></i> Riwayat Transaksi</h4>

<form method="get" class="row g-2 align-items-end mb-3">
    <div class="col-6 col-md-3">
        <label class="form-label small mb-1">Dari tanggal</label>
        <input type="date" name="dari" value="<?= e($dari ?? '') ?>" class="form-control">
    </div>
    <div class="col-6 col-md-3">
        <label class="form-label small mb-1">Sampai tanggal</label>
        <input type="date" name="sampai" value="<?= e($sampai ?? '') ?>" class="form-control">
    </div>
    <div class="col-auto">
        <button class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
        <?php if ($difilter): ?>
            <a href="riwayat.php" class="btn btn-outline-secondary">Reset</a>
        <?php endif; ?>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card card-body">
            <div class="text-muted small">Jumlah transaksi</div>
            <div class="fs-4 fw-bold"><?= e(count($daftar)) ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-body">
            <div class="text-muted small">Total omzet<?= $difilter ? ' (terfilter)' : '' ?></div>
            <div class="fs-5 fw-bold text-success"><?= e(rupiah($totalOmzet)) ?></div>
        </div>
    </div>
</div>

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
                <tr><td colspan="7" class="text-center text-muted">Tidak ada transaksi.</td></tr>
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
                        <a href="struk.php?id=<?= e($t['id_transaksi']) ?>" target="_blank"
                           class="btn btn-sm btn-outline-dark"><i class="bi bi-printer"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>