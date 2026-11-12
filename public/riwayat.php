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

<div class="page-head">
    <div>
        <h2 class="page-title">Riwayat Transaksi</h2>
        <p class="page-subtitle">Pantau penjualan dan cetak ulang struk.</p>
    </div>
    <a href="kasir.php" class="btn btn-primary">
        <i class="bi bi-plus-lg" aria-hidden="true"></i> Transaksi Baru
    </a>
</div>

<form method="get" class="card card-body mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-6 col-md-3">
            <label class="form-label small mb-1" for="dari">Dari tanggal</label>
            <input type="date" id="dari" name="dari" value="<?= e($dari ?? '') ?>" class="form-control">
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label small mb-1" for="sampai">Sampai tanggal</label>
            <input type="date" id="sampai" name="sampai" value="<?= e($sampai ?? '') ?>" class="form-control">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary"><i class="bi bi-funnel" aria-hidden="true"></i> Filter</button>
            <?php if ($difilter): ?>
                <a href="riwayat.php" class="btn btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon tone-blue"><i class="bi bi-receipt" aria-hidden="true"></i></div>
            <div>
                <div class="stat-label">Jumlah transaksi</div>
                <div class="stat-value"><?= e(count($daftar)) ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-5">
        <div class="stat-card">
            <div class="stat-icon tone-green"><i class="bi bi-cash-stack" aria-hidden="true"></i></div>
            <div>
                <div class="stat-label">Total omzet<?= $difilter ? ' (terfilter)' : '' ?></div>
                <div class="stat-value is-money"><?= e(rupiah($totalOmzet)) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center gap-2">
        <span>Daftar Transaksi</span>
        <span class="text-muted small"><?= e(count($daftar)) ?> transaksi</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
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
                        <div class="d-inline-flex gap-1">
                            <a href="detail.php?id=<?= e($t['id_transaksi']) ?>"
                               class="btn btn-sm btn-outline-primary">Detail</a>
                            <a href="struk.php?id=<?= e($t['id_transaksi']) ?>" target="_blank"
                               rel="noopener"
                               class="btn btn-sm btn-outline-dark"
                               aria-label="Cetak struk transaksi #<?= e($t['id_transaksi']) ?>"
                               title="Cetak struk">
                                <i class="bi bi-printer" aria-hidden="true"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
