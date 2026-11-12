<?php

require __DIR__ . '/../bootstrap.php';

use App\KalkulatorTransaksi;
use App\Produk;
use App\Transaksi;

wajibLogin($auth);

$produk = new Produk($db);
$transaksi = new Transaksi($db, new KalkulatorTransaksi());

$hariIni = $transaksi->ringkasanHariIni();
$stokMenipis = $produk->stokMenipis(10);
$terbaru = $transaksi->terbaru(5);
$terlaris = $transaksi->terlaris(5);
$namaUser = $auth->pengguna()['nama'] ?? '';

$judul = 'Dashboard';
require __DIR__ . '/partials/header.php';
?>

<div class="hero p-4 p-md-5 mb-4" style="<?= e(gayaLatar()) ?>">
    <?php if (GAMBAR_LATAR === ''): ?>
        <i class="bi bi-pencil-square dekor" aria-hidden="true"></i>
    <?php endif; ?>
    <h2 class="fw-bold">Halo, <?= e($namaUser) ?>!</h2>
    <p class="mb-3"><?= e(date('d/m/Y')) ?> &middot; Siap melayani pelanggan hari ini?</p>
    <a href="kasir.php" class="btn btn-light btn-lg">
        <i class="bi bi-cart3" aria-hidden="true"></i> Buka Kasir
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon tone-blue"><i class="bi bi-receipt" aria-hidden="true"></i></div>
            <div>
                <div class="stat-label">Transaksi hari ini</div>
                <div class="stat-value"><?= e($hariIni['jumlah']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon tone-green"><i class="bi bi-cash-stack" aria-hidden="true"></i></div>
            <div>
                <div class="stat-label">Omzet hari ini</div>
                <div class="stat-value is-money"><?= e(rupiah($hariIni['omzet'])) ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon tone-cyan"><i class="bi bi-box-seam" aria-hidden="true"></i></div>
            <div>
                <div class="stat-label">Total produk</div>
                <div class="stat-value"><?= e($produk->jumlah()) ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon tone-amber"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></div>
            <div>
                <div class="stat-label">Stok menipis</div>
                <div class="stat-value"><?= e(count($stokMenipis)) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center gap-2">
                <span><i class="bi bi-clock-history" aria-hidden="true"></i> Transaksi Terbaru</span>
                <a href="riwayat.php" class="btn btn-sm btn-outline-secondary">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th class="text-end">Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (count($terbaru) === 0): ?>
                        <tr><td colspan="4" class="text-center text-muted">Belum ada transaksi.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($terbaru as $t): ?>
                        <tr>
                            <td>#<?= e($t['id_transaksi']) ?></td>
                            <td><?= e(date('d/m/Y H:i', strtotime($t['tanggal']))) ?></td>
                            <td class="text-end fw-semibold"><?= e(rupiah($t['total'])) ?></td>
                            <td class="text-end">
                                <a href="detail.php?id=<?= e($t['id_transaksi']) ?>"
                                   class="btn btn-sm btn-outline-primary">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-trophy" aria-hidden="true"></i> Produk Terlaris
            </div>
            <ul class="rank-list">
                <?php if (count($terlaris) === 0): ?>
                    <li class="empty-state">Belum ada penjualan.</li>
                <?php endif; ?>
                <?php foreach ($terlaris as $i => $t): ?>
                    <li class="rank-item">
                        <span class="rank-number"><?= e($i + 1) ?></span>
                        <span class="rank-name"><?= e($t['nama_produk']) ?></span>
                        <span class="badge text-bg-primary"><?= e($t['terjual']) ?> terjual</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-2">
                <span>
                    <i class="bi bi-exclamation-triangle text-warning" aria-hidden="true"></i>
                    Stok Menipis (&le; 10)
                </span>
                <a href="produk.php" class="btn btn-sm btn-outline-secondary">Kelola stok</a>
            </div>
            <ul class="rank-list">
                <?php if (count($stokMenipis) === 0): ?>
                    <li class="empty-state">Semua stok aman.</li>
                <?php endif; ?>
                <?php foreach ($stokMenipis as $s): ?>
                    <li class="rank-item">
                        <span class="rank-name"><?= e($s['nama_produk']) ?></span>
                        <span class="badge text-bg-danger">sisa <?= e($s['stok']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
