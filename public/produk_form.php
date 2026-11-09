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

<h4 class="mb-3"><?= e($judul) ?></h4>

<div class="card card-body">
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nama Produk</label>
            <input type="text" name="nama_produk" value="<?= e($data['nama_produk']) ?>"
                   class="form-control <?= isset($errors['nama_produk']) ? 'is-invalid' : '' ?>">
            <div class="invalid-feedback"><?= e($errors['nama_produk'] ?? '') ?></div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" value="<?= e($data['harga']) ?>"
                       class="form-control <?= isset($errors['harga']) ? 'is-invalid' : '' ?>">
                <div class="invalid-feedback"><?= e($errors['harga'] ?? '') ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Stok</label>
                <input type="number" name="stok" value="<?= e($data['stok']) ?>"
                       class="form-control <?= isset($errors['stok']) ? 'is-invalid' : '' ?>">
                <div class="invalid-feedback"><?= e($errors['stok'] ?? '') ?></div>
            </div>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="produk.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>