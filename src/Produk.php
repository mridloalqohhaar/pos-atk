<?php

declare(strict_types=1);

namespace App;

use PDO;

/**
 * Mengelola data produk: ambil, tambah, ubah, hapus, dan validasi.
 */
class Produk
{
    private PDO $pdo;

    /**
     * Objek Database diterima lewat constructor (dependency injection).
     */
    public function __construct(Database $db)
    {
        $this->pdo = $db->getConnection();
    }

    /**
     * Mengambil semua produk, yang terbaru di atas.
     *
     * @return array<int, array<string, mixed>>
     */
    public function semua(): array
    {
        return $this->pdo
            ->query('SELECT * FROM produk ORDER BY id_produk DESC')
            ->fetchAll();
    }

    /**
     * Mencari satu produk berdasarkan ID.
     *
     * @return array<string, mixed>|null Null jika tidak ditemukan
     */
    public function cari(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM produk WHERE id_produk = :id');
        $stmt->execute(['id' => $id]);
        $baris = $stmt->fetch();

        return $baris === false ? null : $baris;
    }

    /**
     * Menyimpan produk baru.
     *
     * @param array{nama_produk: string, harga: int|string, stok: int|string} $data
     */
    public function tambah(array $data): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO produk (nama_produk, harga, stok) VALUES (:nama, :harga, :stok)'
        );
        $stmt->execute([
            'nama'  => trim($data['nama_produk']),
            'harga' => (int) $data['harga'],
            'stok'  => (int) $data['stok'],
        ]);
    }

    /**
     * Mengubah data produk yang sudah ada.
     *
     * @param array{nama_produk: string, harga: int|string, stok: int|string} $data
     */
    public function ubah(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE produk SET nama_produk = :nama, harga = :harga, stok = :stok
             WHERE id_produk = :id'
        );
        $stmt->execute([
            'nama'  => trim($data['nama_produk']),
            'harga' => (int) $data['harga'],
            'stok'  => (int) $data['stok'],
            'id'    => $id,
        ]);
    }

    /**
     * Menghapus produk.
     *
     * @throws \PDOException Jika produk sudah dipakai di transaksi
     */
    public function hapus(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM produk WHERE id_produk = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * Memvalidasi input form produk.
     *
     * @param array<string, mixed> $data Input mentah dari form
     * @return array<string, string> Daftar pesan error per kolom (kosong jika valid)
     */
    public function validasi(array $data): array
    {
        $errors = [];
        $nama = trim((string) ($data['nama_produk'] ?? ''));

        if ($nama === '') {
            $errors['nama_produk'] = 'Nama produk wajib diisi.';
        } elseif (mb_strlen($nama) > 100) {
            $errors['nama_produk'] = 'Nama produk maksimal 100 karakter.';
        }

        $harga = filter_var($data['harga'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($harga === false) {
            $errors['harga'] = 'Harga harus berupa angka bulat lebih dari 0.';
        }

        $stok = filter_var($data['stok'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($stok === false) {
            $errors['stok'] = 'Stok harus berupa angka bulat 0 atau lebih.';
        }

        return $errors;
    }
}