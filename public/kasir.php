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
    } elseif ($aksi === 'kurangi') {
        $idProduk = (int) ($_POST['id_produk'] ?? 0);
        $qty = (int) ($_POST['qty'] ?? 1);

        if ($qty < 1) {
            $error = 'Qty minimal 1.';
        } else {
            $keranjang->kurangi($idProduk, $qty);
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
        'stok'      => (int) $p['stok'],
        'subtotal'  => $harga * $qty,
    ];
    $itemsHitung[] = ['harga' => $harga, 'qty' => $qty];
}

$hasil = $kalkulator->hitung($itemsHitung);
$daftarProduk = $produk->semua();
$jumlahItem = array_sum(array_column($baris, 'qty'));

$judul = 'Kasir';
require __DIR__ . '/partials/header.php';
?>

<?php if ($error !== ''): ?>
    <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 class="h5 fw-bold mb-1">Pilih Produk</h2>
                <p class="text-muted small mb-0">Klik tombol + untuk menambah ke keranjang.</p>
            </div>
            <span class="badge text-bg-light border"><?= e(count($daftarProduk)) ?> produk</span>
        </div>

        <div class="search-pill w-100 mb-3">
            <i class="bi bi-search" aria-hidden="true"></i>
            <label class="visually-hidden" for="cariProduk">Cari produk</label>
            <input id="cariProduk" type="search" placeholder="Cari nama produk..." autocomplete="off">
        </div>

        <div class="product-grid" id="gridProduk">
            <?php foreach ($daftarProduk as $p): ?>
                <?php
                $stok = (int) $p['stok'];
                $habis = $stok <= 0;
                $ikon = ikonProduk((string) $p['nama_produk']);
                ?>
                <form method="post"
                      class="product-card <?= $habis ? 'is-out' : '' ?>"
                      data-nama="<?= e(mb_strtolower((string) $p['nama_produk'])) ?>">
                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                    <input type="hidden" name="aksi" value="tambah">
                    <input type="hidden" name="id_produk" value="<?= e($p['id_produk']) ?>">
                    <input type="hidden" name="qty" value="1">

                    <div class="product-thumb"
                         style="--thumb-bg: <?= e($ikon['warna']) ?>"
                         aria-hidden="true">
                        <i class="bi <?= e($ikon['ikon']) ?>"></i>
                    </div>

                    <div>
                        <div class="product-name"><?= e($p['nama_produk']) ?></div>
                        <div class="product-meta">
                            <?php if ($stok <= 10): ?>
                                <span class="badge text-bg-danger">
                                    <?= $stok === 0 ? 'Stok habis' : 'Stok ' . e($stok) ?>
                                </span>
                            <?php else: ?>
                                Stok <?= e($stok) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-foot">
                        <span class="product-price"><?= e(rupiah($p['harga'])) ?></span>
                        <button type="submit"
                                class="btn-add"
                                <?= $habis ? 'disabled' : '' ?>
                                aria-label="Tambah <?= e($p['nama_produk']) ?> ke keranjang">
                            <i class="bi bi-plus-lg" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>
            <?php endforeach; ?>
        </div>

        <p class="text-muted text-center py-4 mb-0" id="produkKosong" hidden>
            Produk tidak ditemukan.
        </p>
    </div>

    <div class="col-lg-4">
        <aside class="cart-panel" aria-label="Detail keranjang">
            <div class="cart-head">
                <div>
                    <h2 class="cart-title">Detail Keranjang</h2>
                    <div class="cart-subtitle"><?= e($jumlahItem) ?> item terpilih</div>
                </div>
                <?php if (count($baris) > 0): ?>
                    <form method="post" onsubmit="return confirm('Kosongkan keranjang?')">
                        <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                        <input type="hidden" name="aksi" value="kosongkan">
                        <button type="submit" class="cart-clear">Hapus Semua</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="cart-items">
                <?php if (count($baris) === 0): ?>
                    <div class="cart-empty">
                        Keranjang masih kosong.<br>
                        Pilih produk di sebelah kiri.
                    </div>
                <?php endif; ?>

                <?php foreach ($baris as $b): ?>
                    <div class="cart-item">
                        <div>
                            <div class="cart-item-name"><?= e($b['nama']) ?></div>
                            <div class="cart-item-price"><?= e(rupiah($b['harga'])) ?> / item</div>
                            <div class="cart-item-tools">
                                <div class="qty-control">
                                    <form method="post">
                                        <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                                        <input type="hidden" name="aksi" value="kurangi">
                                        <input type="hidden" name="id_produk" value="<?= e($b['id_produk']) ?>">
                                        <input type="hidden" name="qty" value="1">
                                        <button type="submit"
                                                class="qty-btn"
                                                aria-label="Kurangi qty <?= e($b['nama']) ?>"
                                                title="Kurangi">
                                            <i class="bi bi-dash-lg" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <span class="qty-value">x<?= e($b['qty']) ?></span>
                                    <form method="post">
                                        <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                                        <input type="hidden" name="aksi" value="tambah">
                                        <input type="hidden" name="id_produk" value="<?= e($b['id_produk']) ?>">
                                        <input type="hidden" name="qty" value="1">
                                        <button type="submit"
                                                class="qty-btn"
                                                <?= $b['qty'] >= $b['stok'] ? 'disabled' : '' ?>
                                                aria-label="Tambah qty <?= e($b['nama']) ?>"
                                                title="<?= $b['qty'] >= $b['stok'] ? 'Stok maksimum' : 'Tambah' ?>">
                                            <i class="bi bi-plus-lg" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                                <form method="post">
                                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                                    <input type="hidden" name="aksi" value="hapus">
                                    <input type="hidden" name="id_produk" value="<?= e($b['id_produk']) ?>">
                                    <button type="submit"
                                            class="cart-remove"
                                            aria-label="Hapus <?= e($b['nama']) ?> dari keranjang"
                                            title="Hapus">
                                        <i class="bi bi-trash3" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="cart-item-total"><?= e(rupiah($b['subtotal'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span><?= e(rupiah($hasil['subtotal'])) ?></span>
                </div>
                <div class="summary-row">
                    <span>Diskon</span>
                    <span>- <?= e(rupiah($hasil['diskon'])) ?></span>
                </div>
                <div class="summary-row">
                    <span>Pajak 10%</span>
                    <span><?= e(rupiah($hasil['pajak'])) ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total Akhir</span>
                    <span><?= e(rupiah($hasil['total'])) ?></span>
                </div>

                <form method="post" class="mt-3">
                    <input type="hidden" name="_token" value="<?= e($csrf->token()) ?>">
                    <input type="hidden" name="aksi" value="simpan">
                    <button type="submit"
                            class="btn btn-primary btn-block"
                            <?= count($baris) === 0 ? 'disabled' : '' ?>>
                        Simpan Transaksi
                    </button>
                </form>
            </div>
        </aside>
    </div>
</div>

<script src="assets/js/kasir.js"></script>

<?php require __DIR__ . '/partials/footer.php'; ?>
