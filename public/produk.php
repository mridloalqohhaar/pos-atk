<?php

require __DIR__ . '/../bootstrap.php';

use App\Produk;

wajibLogin($auth);

$produk = new Produk($db);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    try {
        $produk->hapus((int) ($_POST['id_produk'] ?? 0));
        header('Location: produk.php?pesan=hapus');
        exit;
    } catch (PDOException $ex) {
        $error = 'Produk tidak bisa dihapus karena sudah dipakai di transaksi.';
    }
}

$daftarPesan = [
    'tambah' => 'Produk berhasil ditambahkan.',
    'ubah'   => 'Produk berhasil diperbarui.',
    'hapus'  => 'Produk berhasil dihapus.',
];
$pesan = $daftarPesan[$_GET['pesan'] ?? ''] ?? '';

$kata = trim($_GET['q'] ?? '');
$daftarProduk = $produk->semua($kata);

$judul = 'Data Produk';
require __DIR__ . '/partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-box-seam"></i> Data Produk</h4>
    <a href="produk_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-sm-8 col-md-6">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="q" value="<?= e($kata) ?>" class="form-control"
                   placeholder="Cari nama produk...">
        </div>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary">Cari</button>
        <?php if ($kata !== ''): ?>
            <a href="produk.php" class="btn btn-outline-secondary">Reset</a>
        <?php endif; ?>
    </div>
</form>

<?php if ($pesan !== ''): ?>
    <div class="alert alert-success"><?= e($pesan) ?></div>
<?php endif; ?>
<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if ($kata !== ''): ?>
    <p class="text-muted">
        Hasil pencarian "<?= e($kata) ?>": <?= e(count($daftarProduk)) ?> produk ditemukan.
    </p>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nama Produk</th>
                    <th class="text-end">Harga</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($daftarProduk) === 0): ?>
                <tr><td colspan="5" class="text-center text-muted">Produk tidak ditemukan.</td></tr>
            <?php endif; ?>
            <?php foreach ($daftarProduk as $p): ?>
                <tr>
                    <td><?= e($p['id_produk']) ?></td>
                    <td><?= e($p['nama_produk']) ?></td>
                    <td class="text-end"><?= e(rupiah($p['harga'])) ?></td>
                    <td class="text-center">
                        <?php if ((int) $p['stok'] <= 10): ?>
                            <span class="badge text-bg-danger"><?= e($p['stok']) ?></span>
                        <?php else: ?>
                            <?= e($p['stok']) ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="produk_form.php?id=<?= e($p['id_produk']) ?>"
                           class="btn btn-sm btn-warning">Edit</a>
                        <form method="post" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                            <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                            <input type="hidden" name="aksi" value="hapus">
                            <input type="hidden" name="id_produk" value="<?= e($p['id_produk']) ?>">
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>