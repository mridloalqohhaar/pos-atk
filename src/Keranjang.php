<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

/**
 * Menyimpan isi keranjang belanja dalam bentuk array [id_produk => qty].
 * Array penyimpanan diberikan dari luar (misalnya $_SESSION) lewat reference,
 * sehingga class ini mudah diuji tanpa session.
 */
class Keranjang
{
    /** @var array<int, int> */
    private array $data;

    /**
     * @param array<int, int> $penyimpanan Array tempat isi keranjang disimpan
     */
    public function __construct(array &$penyimpanan)
    {
        $this->data = &$penyimpanan;
    }

    /**
     * Menambah produk ke keranjang. Jika sudah ada, qty dijumlahkan.
     *
     * @throws InvalidArgumentException Jika qty kurang dari 1
     */
    public function tambah(int $idProduk, int $qty): void
    {
        if ($qty < 1) {
            throw new InvalidArgumentException('Qty minimal 1.');
        }

        $this->data[$idProduk] = ($this->data[$idProduk] ?? 0) + $qty;
    }

    /**
     * Mengurangi qty satu produk di keranjang.
     * Produk otomatis dihapus jika qty tersisa nol atau kurang.
     *
     * @throws InvalidArgumentException Jika qty kurang dari 1
     */
    public function kurangi(int $idProduk, int $qty = 1): void
    {
        if ($qty < 1) {
            throw new InvalidArgumentException('Qty minimal 1.');
        }

        if (!isset($this->data[$idProduk])) {
            return;
        }

        $sisa = $this->data[$idProduk] - $qty;

        if ($sisa < 1) {
            unset($this->data[$idProduk]);

            return;
        }

        $this->data[$idProduk] = $sisa;
    }

    /**
     * Mengambil qty satu produk di keranjang (0 jika belum ada).
     */
    public function qty(int $idProduk): int
    {
        return $this->data[$idProduk] ?? 0;
    }

    /**
     * Menghapus satu produk dari keranjang.
     */
    public function hapus(int $idProduk): void
    {
        unset($this->data[$idProduk]);
    }

    /**
     * Mengosongkan seluruh keranjang.
     */
    public function kosongkan(): void
    {
        $this->data = [];
    }

    /**
     * Mengambil seluruh isi keranjang.
     *
     * @return array<int, int> Format [id_produk => qty]
     */
    public function semua(): array
    {
        return $this->data;
    }

    /**
     * Mengecek apakah keranjang kosong.
     */
    public function kosong(): bool
    {
        return count($this->data) === 0;
    }
}