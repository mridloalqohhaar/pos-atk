<?php
$halaman = basename($_SERVER['SCRIPT_NAME']);
$alias = ['produk_form.php' => 'produk.php', 'detail.php' => 'riwayat.php'];
$aktif = $alias[$halaman] ?? $halaman;
$penggunaLogin = $auth->pengguna();
$menu = [
    'dashboard.php' => ['bi-speedometer2', 'Dashboard'],
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
    <title><?= e($judul ?? 'POS Toko ATK') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6fb; }
        .card { border: 0; box-shadow: 0 2px 10px rgba(0, 0, 0, .06); border-radius: .75rem; }
        .hero {
            position: relative;
            overflow: hidden;
            color: #fff;
            border-radius: 1rem;
            text-shadow: 0 1px 6px rgba(0, 0, 0, .35);
            background:
                linear-gradient(120deg, rgba(13, 110, 253, .5), rgba(8, 50, 140, .55)),
                var(--gambar, linear-gradient(#0d6efd, #0d6efd)) center / cover,
                #0d6efd;
        }
        .hero > * { position: relative; z-index: 1; }
        .hero .dekor {
            position: absolute;
            z-index: 0;
            right: 1.5rem;
            bottom: -1rem;
            font-size: 9rem;
            opacity: .15;
            transform: rotate(-12deg);
            pointer-events: none;
        }
        .stat-icon {
            width: 52px; height: 52px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="bi bi-pencil-square"></i> POS Toko ATK
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuUtama">
            <ul class="navbar-nav me-auto">
                <?php foreach ($menu as $file => [$ikon, $label]): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $aktif === $file ? 'active fw-semibold' : '' ?>" href="<?= e($file) ?>">
                            <i class="bi <?= e($ikon) ?>"></i> <?= e($label) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if ($penggunaLogin !== null): ?>
                <span class="navbar-text me-3">
                    <i class="bi bi-person-circle"></i> <?= e($penggunaLogin['nama']) ?>
                </span>
                <a href="logout.php" class="btn btn-sm btn-light">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container pb-4">