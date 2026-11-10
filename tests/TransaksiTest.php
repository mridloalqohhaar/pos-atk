<?php

declare(strict_types=1);

namespace Tests;

use App\Database;
use App\KalkulatorTransaksi;
use App\Transaksi;
use PDO;
use PHPUnit\Framework\TestCase;

class TransaksiTest extends TestCase
{
    private PDO $pdo;
    private Transaksi $transaksi;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $this->pdo->exec(
            'CREATE TABLE produk (
                id_produk INTEGER PRIMARY KEY AUTOINCREMENT,
                nama_produk TEXT, harga INTEGER, stok INTEGER
            )'
        );
        $this->pdo->exec(
            'CREATE TABLE transaksi (
                id_transaksi INTEGER PRIMARY KEY AUTOINCREMENT,
                tanggal TEXT, subtotal REAL, diskon REAL, pajak REAL, total REAL
            )'
        );
        $this->pdo->exec(
            'CREATE TABLE detail_transaksi (
                id_detail INTEGER PRIMARY KEY AUTOINCREMENT,
                id_transaksi INTEGER, id_produk INTEGER, qty INTEGER,
                harga_satuan INTEGER, subtotal REAL
            )'
        );

        $db = $this->createStub(Database::class);
        $db->method('getConnection')->willReturn($this->pdo);

        $this->transaksi = new Transaksi($db, new KalkulatorTransaksi());
    }

    private function tambahTransaksi(string $tanggal, float $total): void
    {
        $this->pdo->prepare(
            'INSERT INTO transaksi (tanggal, subtotal, diskon, pajak, total)
             VALUES (:tanggal, :sub, 0, 0, :total)'
        )->execute(['tanggal' => $tanggal, 'sub' => $total, 'total' => $total]);
    }

    public function testSemuaDenganFilterTanggal(): void
    {
        $this->tambahTransaksi('2026-01-10 10:00:00', 10000);
        $this->tambahTransaksi('2026-02-10 10:00:00', 20000);

        $this->assertCount(2, $this->transaksi->semua());
        $this->assertCount(1, $this->transaksi->semua('2026-02-01'));
        $this->assertCount(1, $this->transaksi->semua('2026-01-01', '2026-01-31'));
        $this->assertCount(0, $this->transaksi->semua('2026-03-01'));
    }

    public function testRingkasanHariIniHanyaMenghitungHariIni(): void
    {
        $this->tambahTransaksi(date('Y-m-d H:i:s'), 57420);
        $this->tambahTransaksi('2020-01-01 08:00:00', 99999);

        $ringkasan = $this->transaksi->ringkasanHariIni();

        $this->assertSame(1, $ringkasan['jumlah']);
        $this->assertEqualsWithDelta(57420, $ringkasan['omzet'], 0.01);
    }

    public function testProdukTerlarisDiurutkanBerdasarkanQty(): void
    {
        $this->pdo->exec("INSERT INTO produk (nama_produk, harga, stok) VALUES ('Pulpen', 2500, 50), ('Buku', 4500, 50)");
        $this->tambahTransaksi('2026-01-10 10:00:00', 1000);
        $this->tambahTransaksi('2026-01-11 10:00:00', 1000);
        $this->pdo->exec(
            'INSERT INTO detail_transaksi (id_transaksi, id_produk, qty, harga_satuan, subtotal) VALUES
             (1, 1, 3, 2500, 7500), (1, 2, 1, 4500, 4500), (2, 1, 2, 2500, 5000)'
        );

        $hasil = $this->transaksi->terlaris(2);

        $this->assertSame('Pulpen', $hasil[0]['nama_produk']);
        $this->assertEquals(5, $hasil[0]['terjual']);
        $this->assertSame('Buku', $hasil[1]['nama_produk']);
    }
}
