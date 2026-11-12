<?php

require __DIR__ . '/../bootstrap.php';

use App\Produk;

wajibLogin($auth);

$produk = new Produk($db);
$id = (int) ($_GET['id'] ?? 0);
$data = ['nama_produk' => '', 'harga' => '', 'stok' => '0'];
$errors = [];

if ($id > 0) {
    $ditemukan = $produk->cari($id);
    if ($ditemukan === null) {
        header('Location: produk.php');
        exit;
    }
    $data = $ditemukan;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_produk' => trim($_POST['nama_produk'] ?? ''),
        'harga'       => $_POST['harga'] ?? '',
        'stok'        => $_POST['stok'] ?? '',
    ];
    $errors = $produk->validasi($data);

    if (count($errors) === 0) {
        if ($id > 0) {
            $produk->ubah($id, $data);
            header('Location: produk.php?pesan=ubah');
        } else {
            $produk->tambah($data);
            header('Location: produk.php?pesan=tambah');
        }
        exit;
    }
}

$judul = $id > 0 ? 'Edit Produk' : 'Tambah Produk';
require __DIR__ . '/partials/header.php';
?>

<div class="page-head">
    <div>
        <h2 class="page-title"><?= e($judul) ?></h2>
        <p class="page-subtitle">
            <?= $id > 0 ? 'Perbarui informasi produk yang sudah ada.' : 'Lengkapi informasi produk baru.' ?>
        </p>
    </div>
    <a href="produk.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card">
            <div class="card-header">Informasi Produk</div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">

                    <div class="mb-3">
                        <label class="form-label" for="nama_produk">Nama Produk</label>
                        <input type="text" id="nama_produk" name="nama_produk"
                               value="<?= e($data['nama_produk']) ?>"
                               class="form-control <?= isset($errors['nama_produk']) ? 'is-invalid' : '' ?>">
                        <div class="invalid-feedback"><?= e($errors['nama_produk'] ?? '') ?></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="harga">Harga (Rp)</label>
                            <input type="number" id="harga" name="harga" value="<?= e($data['harga']) ?>"
                                   class="form-control <?= isset($errors['harga']) ? 'is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= e($errors['harga'] ?? '') ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="stok">Stok</label>
                            <input type="number" id="stok" name="stok" value="<?= e($data['stok']) ?>"
                                   class="form-control <?= isset($errors['stok']) ? 'is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= e($errors['stok'] ?? '') ?></div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">
                            <i class="bi bi-check2" aria-hidden="true"></i> Simpan
                        </button>
                        <a href="produk.php" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
