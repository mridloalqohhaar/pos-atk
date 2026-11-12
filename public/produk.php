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

<div class="page-head">
    <div>
        <h2 class="page-title">Data Produk</h2>
        <p class="page-subtitle">Kelola nama, harga, dan stok produk toko.</p>
    </div>
    <a href="produk_form.php" class="btn btn-primary">
        <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Produk
    </a>
</div>

<?php if ($pesan !== ''): ?>
    <div class="alert alert-success" role="status"><?= e($pesan) ?></div>
<?php endif; ?>
<?php if ($error !== ''): ?>
    <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
<?php endif; ?>

<form method="get" class="card card-body mb-3">
    <div class="row g-2 align-items-center">
        <div class="col-sm-8 col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                <label class="visually-hidden" for="cariProduk">Cari nama produk</label>
                <input type="text" id="cariProduk" name="q" value="<?= e($kata) ?>" class="form-control"
                       placeholder="Cari nama produk...">
            </div>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Cari</button>
            <?php if ($kata !== ''): ?>
                <a href="produk.php" class="btn btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php if ($kata !== ''): ?>
    <p class="text-muted small">
        Hasil pencarian "<?= e($kata) ?>": <?= e(count($daftarProduk)) ?> produk ditemukan.
    </p>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center gap-2">
        <span>Daftar Produk</span>
        <span class="text-muted small"><?= e(count($daftarProduk)) ?> produk</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
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
                <?php $ikon = ikonProduk((string) $p['nama_produk']); ?>
                <tr>
                    <td><?= e($p['id_produk']) ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="product-thumb-sm"
                                  style="--thumb-bg: <?= e($ikon['warna']) ?>"
                                  aria-hidden="true">
                                <i class="bi <?= e($ikon['ikon']) ?>"></i>
                            </span>
                            <span><?= e($p['nama_produk']) ?></span>
                        </div>
                    </td>
                    <td class="text-end"><?= e(rupiah($p['harga'])) ?></td>
                    <td class="text-center">
                        <?php if ((int) $p['stok'] <= 10): ?>
                            <span class="badge text-bg-danger"><?= e($p['stok']) ?></span>
                        <?php else: ?>
                            <?= e($p['stok']) ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            <a href="produk_form.php?id=<?= e($p['id_produk']) ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil" aria-hidden="true"></i> Edit
                            </a>
                            <form method="post" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                                <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                                <input type="hidden" name="aksi" value="hapus">
                                <input type="hidden" name="id_produk" value="<?= e($p['id_produk']) ?>">
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash3" aria-hidden="true"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
