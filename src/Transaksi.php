<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use PDO;
use RuntimeException;
use Throwable;

/**
 * Menyimpan transaksi penjualan dan membaca riwayatnya.
 */
class Transaksi
{
    private PDO $pdo;
    private KalkulatorTransaksi $kalkulator;

    /**
     * Dependensi diterima lewat constructor (dependency injection).
     */
    public function __construct(Database $db, KalkulatorTransaksi $kalkulator)
    {
        $this->pdo = $db->getConnection();
        $this->kalkulator = $kalkulator;
    }

    /**
     * Menyimpan transaksi: cek stok, simpan header + detail, kurangi stok.
     * Semua langkah berjalan dalam satu database transaction. Jika salah satu
     * gagal, seluruh perubahan dibatalkan (rollback).
     *
     * @param array<int, int> $items Format [id_produk => qty]
     * @return int ID transaksi yang baru disimpan
     * @throws InvalidArgumentException Jika keranjang kosong
     * @throws RuntimeException Jika produk tidak ada atau stok tidak cukup
     */
    public function simpan(array $items): int
    {
        if (count($items) === 0) {
            throw new InvalidArgumentException('Keranjang masih kosong.');
        }

        $this->pdo->beginTransaction();

        try {
            $ambil = $this->pdo->prepare(
                'SELECT id_produk, nama_produk, harga, stok
                 FROM produk WHERE id_produk = :id FOR UPDATE'
            );

            $baris = [];
            $itemsHitung = [];

            foreach ($items as $idProduk => $qty) {
                $ambil->execute(['id' => $idProduk]);
                $produk = $ambil->fetch();

                if ($produk === false) {
                    throw new RuntimeException('Ada produk yang sudah tidak tersedia.');
                }
                if ($qty > (int) $produk['stok']) {
                    throw new RuntimeException(sprintf(
                        'Stok "%s" tidak cukup (sisa %d).',
                        $produk['nama_produk'],
                        (int) $produk['stok']
                    ));
                }

                $harga = (int) $produk['harga'];
                $baris[] = ['id_produk' => (int) $idProduk, 'qty' => $qty, 'harga' => $harga];
                $itemsHitung[] = ['harga' => $harga, 'qty' => $qty];
            }

            $hasil = $this->kalkulator->hitung($itemsHitung);

            $this->pdo->prepare(
                'INSERT INTO transaksi (subtotal, diskon, pajak, total)
                 VALUES (:subtotal, :diskon, :pajak, :total)'
            )->execute($hasil);

            $idTransaksi = (int) $this->pdo->lastInsertId();

            $simpanDetail = $this->pdo->prepare(
                'INSERT INTO detail_transaksi (id_transaksi, id_produk, qty, harga_satuan, subtotal)
                 VALUES (:trx, :produk, :qty, :harga, :subtotal)'
            );
            $kurangiStok = $this->pdo->prepare(
                'UPDATE produk SET stok = stok - :qty WHERE id_produk = :id'
            );

            foreach ($baris as $b) {
                $simpanDetail->execute([
                    'trx'      => $idTransaksi,
                    'produk'   => $b['id_produk'],
                    'qty'      => $b['qty'],
                    'harga'    => $b['harga'],
                    'subtotal' => $b['harga'] * $b['qty'],
                ]);
                $kurangiStok->execute(['qty' => $b['qty'], 'id' => $b['id_produk']]);
            }

            $this->pdo->commit();

            return $idTransaksi;
        } catch (Throwable $ex) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $ex;
        }
    }

    /**
     * Mengambil semua transaksi, yang terbaru di atas.
     *
     * @return array<int, array<string, mixed>>
     */
    public function semua(): array
    {
        return $this->pdo
            ->query('SELECT * FROM transaksi ORDER BY id_transaksi DESC')
            ->fetchAll();
    }

    /**
     * Mencari satu transaksi berdasarkan ID.
     *
     * @return array<string, mixed>|null Null jika tidak ditemukan
     */
    public function cari(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM transaksi WHERE id_transaksi = :id');
        $stmt->execute(['id' => $id]);
        $baris = $stmt->fetch();

        return $baris === false ? null : $baris;
    }

    /**
     * Mengambil rincian item dari satu transaksi.
     *
     * @return array<int, array<string, mixed>>
     */
    public function detail(int $id): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.nama_produk, d.qty, d.harga_satuan, d.subtotal
             FROM detail_transaksi d
             JOIN produk p ON p.id_produk = d.id_produk
             WHERE d.id_transaksi = :id
             ORDER BY d.id_detail'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetchAll();
    }
        /**
     * Merangkum transaksi hari ini: jumlah transaksi dan total omzet.
     *
     * @return array{jumlah: int, omzet: float}
     */
    public function ringkasanHariIni(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) AS jumlah, COALESCE(SUM(total), 0) AS omzet
             FROM transaksi WHERE DATE(tanggal) = :hari'
        );
        $stmt->execute(['hari' => date('Y-m-d')]);
        $baris = $stmt->fetch();

        return ['jumlah' => (int) $baris['jumlah'], 'omzet' => (float) $baris['omzet']];
    }

    /**
     * Mengambil transaksi terbaru.
     *
     * @param int $jumlah Banyaknya transaksi yang diambil
     * @return array<int, array<string, mixed>>
     */
    public function terbaru(int $jumlah = 5): array
    {
        return $this->pdo
            ->query('SELECT * FROM transaksi ORDER BY id_transaksi DESC LIMIT ' . (int) $jumlah)
            ->fetchAll();
    }

    /**
     * Mengambil produk terlaris berdasarkan total qty terjual.
     *
     * @param int $jumlah Banyaknya produk yang diambil
     * @return array<int, array<string, mixed>>
     */
    public function terlaris(int $jumlah = 5): array
    {
        return $this->pdo->query(
            'SELECT p.nama_produk, SUM(d.qty) AS terjual
             FROM detail_transaksi d
             JOIN produk p ON p.id_produk = d.id_produk
             GROUP BY p.id_produk, p.nama_produk
             ORDER BY terjual DESC
             LIMIT ' . (int) $jumlah
        )->fetchAll();
    }
}