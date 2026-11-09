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

$judul = 'Data Produk';
$daftarProduk = $produk->semua();

require __DIR__ . '/partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Data Produk</h4>
    <a href="produk_form.php" class="btn btn-primary">+ Tambah Produk</a>
</div>

<?php if ($pesan !== ''): ?>
    <div class="alert alert-success"><?= e($pesan) ?></div>
<?php endif; ?>
<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
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
                <tr><td colspan="5" class="text-center">Belum ada produk.</td></tr>
            <?php endif; ?>
            <?php foreach ($daftarProduk as $p): ?>
                <tr>
                    <td><?= e($p['id_produk']) ?></td>
                    <td><?= e($p['nama_produk']) ?></td>
                    <td class="text-end"><?= e(rupiah($p['harga'])) ?></td>
                    <td class="text-center"><?= e($p['stok']) ?></td>
                    <td class="text-center">
                        <a href="produk_form.php?id=<?= e($p['id_produk']) ?>"
                           class="btn btn-sm btn-warning">Edit</a>
                        <form method="post" class="d-inline"
                              onsubmit="return confirm('Hapus produk ini?')">
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