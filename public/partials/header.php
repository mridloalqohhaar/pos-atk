<?php
$halaman = basename($_SERVER['SCRIPT_NAME']);
$alias = ['produk_form.php' => 'produk.php', 'detail.php' => 'riwayat.php'];
$aktif = $alias[$halaman] ?? $halaman;
$penggunaLogin = $auth->pengguna();
$judulHalaman = $judul ?? 'POS Toko ATK';
$inisial = '?';

if ($penggunaLogin !== null && $penggunaLogin['nama'] !== '') {
    $inisial = mb_strtoupper(mb_substr($penggunaLogin['nama'], 0, 1));
}

$menu = [
    'dashboard.php' => ['bi-grid-1x2', 'Dashboard'],
    'produk.php'    => ['bi-box-seam', 'Produk'],
    'kasir.php'     => ['bi-cart3', 'Kasir'],
    'riwayat.php'   => ['bi-clock-history', 'Riwayat'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judulHalaman) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/app.css" rel="stylesheet">
</head>
<body class="app-body">
<a class="visually-hidden-focusable" href="#konten">Lewati ke konten</a>
<div class="app-shell">
    <aside class="app-sidebar" aria-label="Menu utama">
        <a class="sidebar-brand" href="dashboard.php" aria-label="POS Toko ATK">
            <i class="bi bi-pencil-square" aria-hidden="true"></i>
        </a>

        <nav class="sidebar-nav">
            <?php foreach ($menu as $file => [$ikon, $label]): ?>
                <a class="sidebar-link <?= $aktif === $file ? 'active' : '' ?>"
                   href="<?= e($file) ?>"
                   data-label="<?= e($label) ?>"
                   title="<?= e($label) ?>"
                   aria-label="<?= e($label) ?>"
                   <?= $aktif === $file ? 'aria-current="page"' : '' ?>>
                    <i class="bi <?= e($ikon) ?>" aria-hidden="true"></i>
                    <span class="visually-hidden"><?= e($label) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if ($penggunaLogin !== null): ?>
            <a class="sidebar-link sidebar-logout"
               href="logout.php"
               data-label="Keluar"
               title="Keluar"
               aria-label="Keluar">
                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                <span class="visually-hidden">Keluar</span>
            </a>
        <?php endif; ?>
    </aside>

    <div class="app-main">
        <header class="app-topbar">
            <div class="topbar-left">
                <?php if ($halaman !== 'dashboard.php'): ?>
                    <button type="button" class="btn-back" onclick="history.back()" aria-label="Kembali ke halaman sebelumnya">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    </button>
                <?php endif; ?>
                <div class="topbar-heading">
                    <h1 class="topbar-title"><?= e($judulHalaman) ?></h1>
                    <div class="topbar-subtitle">Toko ATK &middot; <?= e(date('d/m/Y')) ?></div>
                </div>
            </div>

            <div class="topbar-actions">
                <form class="search-pill" method="get" action="produk.php" role="search">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <label class="visually-hidden" for="cariGlobal">Cari produk</label>
                    <input id="cariGlobal" type="search" name="q" placeholder="Cari produk..." autocomplete="off">
                </form>

                <span class="date-chip">
                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                    <?= e(date('d/m/Y')) ?>
                </span>

                <?php if ($penggunaLogin !== null): ?>
                    <div class="user-chip">
                        <span class="avatar" aria-hidden="true"><?= e($inisial) ?></span>
                        <span>
                            <span class="user-name"><?= e($penggunaLogin['nama']) ?></span>
                            <span class="user-role"><?= e($penggunaLogin['username']) ?></span>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <main class="app-content" id="konten">
