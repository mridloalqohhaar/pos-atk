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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/app.css" rel="stylesheet">
</head>
<body class="login-page">
<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">
        <div class="col-md-7 d-none d-md-flex login-visual align-items-center justify-content-center"
             style="<?= e(gayaLatar()) ?>">
            <?php if (GAMBAR_LATAR === ''): ?>
                <i class="bi bi-journal-text dekor dekor-buku" aria-hidden="true"></i>
                <i class="bi bi-pencil dekor dekor-pensil" aria-hidden="true"></i>
                <i class="bi bi-paperclip dekor dekor-paperclip" aria-hidden="true"></i>
                <i class="bi bi-scissors dekor dekor-gunting" aria-hidden="true"></i>
            <?php endif; ?>

            <div class="isi text-center px-5">
                <span class="login-brand">
                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                </span>
                <h1 class="fw-bold mt-2">POS Toko ATK</h1>
                <p class="lead mb-0">Kasir cepat, stok terkontrol, laporan rapi.</p>

                <div class="login-features">
                    <span class="login-feature">
                        <i class="bi bi-cart3" aria-hidden="true"></i>
                        Kasir
                    </span>
                    <span class="login-feature">
                        <i class="bi bi-box-seam" aria-hidden="true"></i>
                        Stok
                    </span>
                    <span class="login-feature">
                        <i class="bi bi-receipt" aria-hidden="true"></i>
                        Riwayat
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-5 login-panel">
            <div class="login-card">
                <div class="d-md-none text-center mb-4">
                    <span class="login-logo mx-auto">
                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                    </span>
                    <h4 class="fw-bold mb-0">POS Toko ATK</h4>
                </div>

                <h3 class="fw-bold mb-1">Selamat datang</h3>
                <p class="text-muted mb-4">Masuk untuk membuka kasir.</p>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger py-2" role="alert"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                    <div class="mb-3">
                        <label class="form-label" for="username">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person" aria-hidden="true"></i></span>
                            <input type="text" id="username" name="username" value="<?= e($username) ?>"
                                   class="form-control" autofocus autocomplete="username">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock" aria-hidden="true"></i></span>
                            <input type="password" id="password" name="password"
                                   class="form-control" autocomplete="current-password">
                        </div>
                    </div>
                    <button class="btn btn-primary w-100 py-2">
                        <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
