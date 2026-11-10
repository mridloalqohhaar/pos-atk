<?php

require __DIR__ . '/../bootstrap.php';

use App\Pengguna;

if ($auth->cek()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $pengguna = (new Pengguna($db))->login($username, $password);

        if ($pengguna === null) {
            $error = 'Username atau password salah.';
        } else {
            session_regenerate_id(true);
            $auth->masuk($pengguna);
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - POS Toko ATK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .panel-gambar {
            position: relative;
            overflow: hidden;
            color: #fff;
            text-shadow: 0 1px 8px rgba(0, 0, 0, .4);
            background:
                linear-gradient(160deg, rgba(13, 110, 253, .45), rgba(8, 40, 120, .55)),
                var(--gambar, linear-gradient(#0d6efd, #0d6efd)) center / cover,
                #0d6efd;
        }
        .panel-gambar .isi { position: relative; z-index: 1; }
        .panel-gambar .dekor {
            position: absolute;
            z-index: 0;
            opacity: .12;
            pointer-events: none;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row min-vh-100">
        <div class="col-md-7 d-none d-md-flex panel-gambar align-items-center justify-content-center"
             style="<?= e(gayaLatar()) ?>">
            <?php if (GAMBAR_LATAR === ''): ?>
                <i class="bi bi-journal-text dekor" style="font-size: 12rem; left: 4%; top: 8%; transform: rotate(-10deg);"></i>
                <i class="bi bi-pencil dekor" style="font-size: 10rem; right: 6%; top: 12%; transform: rotate(15deg);"></i>
                <i class="bi bi-paperclip dekor" style="font-size: 9rem; left: 12%; bottom: 6%; transform: rotate(25deg);"></i>
                <i class="bi bi-scissors dekor" style="font-size: 9rem; right: 10%; bottom: 8%; transform: rotate(-20deg);"></i>
            <?php endif; ?>

            <div class="isi text-center px-5">
                <i class="bi bi-pencil-square" style="font-size: 5rem;"></i>
                <h1 class="fw-bold mt-3">POS Toko ATK</h1>
                <p class="lead">Kasir cepat, stok terkontrol, laporan rapi.</p>
                <div class="d-flex justify-content-center gap-4 mt-4 fs-5">
                    <span><i class="bi bi-cart3"></i> Kasir</span>
                    <span><i class="bi bi-box-seam"></i> Stok</span>
                    <span><i class="bi bi-receipt"></i> Riwayat</span>
                </div>
            </div>
        </div>

        <div class="col-md-5 d-flex align-items-center justify-content-center bg-light">
            <div class="w-100 p-4" style="max-width: 380px;">
                <div class="text-center d-md-none mb-3 text-primary">
                    <i class="bi bi-pencil-square" style="font-size: 3rem;"></i>
                    <h4 class="fw-bold">POS Toko ATK</h4>
                </div>

                <h3 class="fw-bold mb-1">Selamat datang 👋</h3>
                <p class="text-muted mb-4">Masuk untuk membuka kasir.</p>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger py-2"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="post"> <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" value="<?= e($username) ?>"
                                   class="form-control" autofocus>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control">
                        </div>
                    </div>
                    <button class="btn btn-primary w-100 py-2">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>