<?php

require __DIR__ . '/../bootstrap.php';
use App\KalkulatorTransaksi;
use App\Keranjang;
use App\Produk;
use App\Transaksi;
wajibLogin($auth);

$_SESSION['keranjang'] ??= [];

$keranjang = new Keranjang($_SESSION['keranjang']);
$produk = new Produk($db);
$kalkulator = new KalkulatorTransaksi();
$transaksi = new Transaksi($db, $kalkulator);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'tambah') {
        $idProduk = (int) ($_POST['id_produk'] ?? 0);
        $qty = (int) ($_POST['qty'] ?? 0);
        $dipilih = $produk->cari($idProduk);

        if ($dipilih === null) {
            $error = 'Pilih produk terlebih dahulu.';
        } elseif ($qty < 1) {
            $error = 'Qty minimal 1.';
        } elseif ($keranjang->qty($idProduk) + $qty > (int) $dipilih['stok']) {
            $error = 'Stok "' . $dipilih['nama_produk'] . '" tidak cukup (sisa ' . $dipilih['stok'] . ').';
        } else {
            $keranjang->tambah($idProduk, $qty);
            header('Location: kasir.php');
            exit;
        }
    } elseif ($aksi === 'hapus') {
        $keranjang->hapus((int) ($_POST['id_produk'] ?? 0));
        header('Location: kasir.php');
        exit;
    } elseif ($aksi === 'kosongkan') {
        $keranjang->kosongkan();
        header('Location: kasir.php');
        exit;
    } elseif ($aksi === 'simpan') {
        try {
            $idBaru = $transaksi->simpan($keranjang->semua());
            $keranjang->kosongkan();
            header('Location: detail.php?id=' . $idBaru . '&pesan=sukses');
            exit;
        } catch (RuntimeException | InvalidArgumentException $ex) {
            $error = $ex->getMessage();
        }
    }
}

// Susun data tampilan keranjang dan hitung total lewat kalkulator
$baris = [];
$itemsHitung = [];

foreach ($keranjang->semua() as $idProduk => $qty) {
    $p = $produk->cari((int) $idProduk);

    if ($p === null) {
        $keranjang->hapus((int) $idProduk);
        continue;
    }

    $harga = (int) $p['harga'];
    $baris[] = [
        'id_produk' => $idProduk,
        'nama'      => $p['nama_produk'],
        'harga'     => $harga,
        'qty'       => $qty,
        'subtotal'  => $harga * $qty,
    ];
    $itemsHitung[] = ['harga' => $harga, 'qty' => $qty];
}

$hasil = $kalkulator->hitung($itemsHitung);
$daftarProduk = $produk->semua();

$judul = 'Kasir';
require __DIR__ . '/partials/header.php';
?>

<h4 class="mb-3">Kasir</h4>

<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4 mb-3">
        <div class="card card-body">
            <h6>Tambah ke Keranjang</h6>
            <form method="post">
                <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                <input type="hidden" name="aksi" value="tambah">
                <div class="mb-3">
                    <label class="form-label">Produk</label>
                    <select name="id_produk" class="form-select">
                        <option value="">-- Pilih produk --</option>
                        <?php foreach ($daftarProduk as $p): ?>
                            <?php if ((int) $p['stok'] > 0): ?>
                                <option value="<?= e($p['id_produk']) ?>">
                                    <?= e($p['nama_produk']) ?> - <?= e(rupiah($p['harga'])) ?> (stok <?= e($p['stok']) ?>)
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Qty</label>
                    <input type="number" name="qty" value="1" min="1" class="form-control">
                </div>
                <button class="btn btn-primary w-100">+ Tambah</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Produk</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (count($baris) === 0): ?>
                        <tr><td colspan="5" class="text-center">Keranjang masih kosong.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($baris as $b): ?>
                        <tr>
                            <td><?= e($b['nama']) ?></td>
                            <td class="text-end"><?= e(rupiah($b['harga'])) ?></td>
                            <td class="text-center"><?= e($b['qty']) ?></td>
                            <td class="text-end"><?= e(rupiah($b['subtotal'])) ?></td>
                            <td class="text-end">
                                <form method="post">
                                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                                    <input type="hidden" name="aksi" value="hapus">
                                    <input type="hidden" name="id_produk" value="<?= e($b['id_produk']) ?>">
                                    <button class="btn btn-sm btn-outline-danger">x</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end">Subtotal</td>
                            <td class="text-end"><?= e(rupiah($hasil['subtotal'])) ?></td><td></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">Diskon</td>
                            <td class="text-end">- <?= e(rupiah($hasil['diskon'])) ?></td><td></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">Pajak 10%</td>
                            <td class="text-end"><?= e(rupiah($hasil['pajak'])) ?></td><td></td>
                        </tr>
                        <tr class="table-primary fw-bold">
                            <td colspan="3" class="text-end">TOTAL</td>
                            <td class="text-end"><?= e(rupiah($hasil['total'])) ?></td><td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <?php if (count($baris) > 0): ?>
        <div class="d-flex gap-2 mt-3">
            <form method="post">
                <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                <input type="hidden" name="aksi" value="simpan">
                <button class="btn btn-success">Simpan Transaksi</button>
            </form>
            <form method="post" onsubmit="return confirm('Kosongkan keranjang?')">
                <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                <input type="hidden" name="aksi" value="kosongkan">
                <button class="btn btn-outline-secondary">Kosongkan</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>