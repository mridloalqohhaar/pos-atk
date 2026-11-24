<?php

require __DIR__ . '/../bootstrap.php';

use App\Produk;
use App\UnggahGambar;

wajibLogin($auth);

$produk = new Produk($db);
$unggah = new UnggahGambar(__DIR__ . '/img/produk');
$id = (int) ($_GET['id'] ?? 0);
$data = ['nama_produk' => '', 'harga' => '', 'stok' => '0'];
$gambarLama = null;
$errors = [];

if ($id > 0) {
    $ditemukan = $produk->cari($id);
    if ($ditemukan === null) {
        header('Location: produk.php');
        exit;
    }
    $data = $ditemukan;
    $gambarLama = $ditemukan['gambar'] ?? null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_produk' => trim($_POST['nama_produk'] ?? ''),
        'harga'       => $_POST['harga'] ?? '',
        'stok'        => $_POST['stok'] ?? '',
    ];
    $errors = $produk->validasi($data);

    $file = $_FILES['gambar'] ?? ['error' => UPLOAD_ERR_NO_FILE];
    $pesanGambar = $unggah->validasi($file);
    if ($pesanGambar !== null) {
        $errors['gambar'] = $pesanGambar;
    }

    if (count($errors) === 0) {
        try {
            $gambarBaru = $unggah->simpan($file);

            if ($id > 0) {
                $produk->ubah($id, $data, $gambarBaru);

                if ($gambarBaru !== null) {
                    $unggah->hapus($gambarLama);
                } elseif (isset($_POST['hapus_gambar'])) {
                    $produk->kosongkanGambar($id);
                    $unggah->hapus($gambarLama);
                }

                header('Location: produk.php?pesan=ubah');
            } else {
                $produk->tambah($data, $gambarBaru);
                header('Location: produk.php?pesan=tambah');
            }
            exit;
        } catch (RuntimeException $ex) {
            $errors['gambar'] = $ex->getMessage();
        }
    }
}

$foto = urlGambar($gambarLama);

$judul = $id > 0 ? 'Edit Produk' : 'Tambah Produk';
require __DIR__ . '/partials/header.php';
?>

<h4 class="mb-3"><?= e($judul) ?></h4>

<div class="card card-body">
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">

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

        <div class="mb-3">
            <label class="form-label" for="gambar">Gambar Produk (opsional)</label>
            <div>
                <img id="pratinjau" src="<?= e($foto ?? '') ?>" alt="Pratinjau gambar"
                     class="rounded border mb-2"
                     style="width:120px;height:120px;object-fit:cover"
                     <?= $foto === null ? 'hidden' : '' ?>>
            </div>
            <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp"
                   class="form-control <?= isset($errors['gambar']) ? 'is-invalid' : '' ?>">
            <div class="invalid-feedback"><?= e($errors['gambar'] ?? '') ?></div>
            <div class="form-text">Format JPG, PNG, atau WEBP. Maksimal 2 MB. Foto persegi paling pas.</div>

            <?php if ($id > 0 && $foto !== null): ?>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="hapus_gambar" id="hapus_gambar">
                    <label class="form-check-label" for="hapus_gambar">
                        Hapus gambar (kembali memakai ikon)
                    </label>
                </div>
            <?php endif; ?>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="produk.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
document.getElementById('gambar').addEventListener('change', function () {
    var file = this.files[0];
    var pratinjau = document.getElementById('pratinjau');
    if (file) {
        pratinjau.src = URL.createObjectURL(file);
        pratinjau.hidden = false;
    }
});
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>