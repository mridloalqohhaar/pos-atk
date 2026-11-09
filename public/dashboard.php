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

<div class="hero p-4 p-md-5 mb-4">
    <h2 class="fw-bold">Halo, <?= e($namaUser) ?>! 👋</h2>
    <p class="mb-3"><?= e(date('d/m/Y')) ?> · Siap melayani pelanggan hari ini?</p>
    <a href="kasir.php" class="btn btn-light btn-lg">
        <i class="bi bi-cart3"></i> Buka Kasir
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card card-body d-flex flex-row align-items-center gap-3">
            <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-receipt"></i></div>
            <div>
                <div class="text-muted small">Transaksi hari ini</div>
                <div class="fs-4 fw-bold"><?= e($hariIni['jumlah']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-body d-flex flex-row align-items-center gap-3">
            <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="text-muted small">Omzet hari ini</div>
                <div class="fs-5 fw-bold"><?= e(rupiah($hariIni['omzet'])) ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-body d-flex flex-row align-items-center gap-3">
            <div class="stat-icon bg-info-subtle text-info"><i class="bi bi-box-seam"></i></div>
            <div>
                <div class="text-muted small">Total produk</div>
                <div class="fs-4 fw-bold"><?= e($produk->jumlah()) ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-body d-flex flex-row align-items-center gap-3">
            <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-exclamation-triangle"></i></div>
            <div>
                <div class="text-muted small">Stok menipis</div>
                <div class="fs-4 fw-bold"><?= e(count($stokMenipis)) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-clock-history"></i> Transaksi Terbaru
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>No</th><th>Tanggal</th><th class="text-end">Total</th><th></th></tr>
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
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-trophy"></i> Produk Terlaris
            </div>
            <ul class="list-group list-group-flush">
                <?php if (count($terlaris) === 0): ?>
                    <li class="list-group-item text-muted">Belum ada penjualan.</li>
                <?php endif; ?>
                <?php foreach ($terlaris as $i => $t): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= e($i + 1) ?>. <?= e($t['nama_produk']) ?></span>
                        <span class="badge text-bg-primary"><?= e($t['terjual']) ?> terjual</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-exclamation-triangle text-warning"></i> Stok Menipis (≤ 10)
            </div>
            <ul class="list-group list-group-flush">
                <?php if (count($stokMenipis) === 0): ?>
                    <li class="list-group-item text-muted">Semua stok aman 👍</li>
                <?php endif; ?>
                <?php foreach ($stokMenipis as $s): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= e($s['nama_produk']) ?></span>
                        <span class="badge text-bg-danger">sisa <?= e($s['stok']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>