<?php

declare(strict_types=1);

namespace Tests;

use App\Keranjang;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class KeranjangTest extends TestCase
{
    public function testTambahProdukBaru(): void
    {
        $penyimpanan = [];
        $keranjang = new Keranjang($penyimpanan);

        $keranjang->tambah(1, 2);

        $this->assertSame([1 => 2], $keranjang->semua());
    }

    public function testTambahProdukSamaMenjumlahkanQty(): void
    {
        $penyimpanan = [];
        $keranjang = new Keranjang($penyimpanan);

        $keranjang->tambah(1, 2);
        $keranjang->tambah(1, 3);

        $this->assertSame(5, $keranjang->qty(1));
    }

    public function testQtyNolDitolak(): void
    {
        $penyimpanan = [];
        $keranjang = new Keranjang($penyimpanan);

        $this->expectException(InvalidArgumentException::class);
        $keranjang->tambah(1, 0);
    }

    public function testKurangiMengurangiQty(): void
    {
        $penyimpanan = [];
        $keranjang = new Keranjang($penyimpanan);
        $keranjang->tambah(1, 5);

        $keranjang->kurangi(1, 2);

        $this->assertSame(3, $keranjang->qty(1));
    }

    public function testKurangiDefaultSatu(): void
    {
        $penyimpanan = [];
        $keranjang = new Keranjang($penyimpanan);
        $keranjang->tambah(1, 2);

        $keranjang->kurangi(1);

        $this->assertSame(1, $keranjang->qty(1));
    }

    public function testKurangiSampaiHabisMenghapusProduk(): void
    {
        $penyimpanan = [];
        $keranjang = new Keranjang($penyimpanan);
        $keranjang->tambah(1, 2);

        $keranjang->kurangi(1, 2);

        $this->assertSame(0, $keranjang->qty(1));
        $this->assertSame([], $keranjang->semua());
    }

    public function testKurangiMelebihiQtyMenghapusProduk(): void
    {
        $penyimpanan = [];
        $keranjang = new Keranjang($penyimpanan);
        $keranjang->tambah(1, 2);

        $keranjang->kurangi(1, 5);

        $this->assertTrue($keranjang->kosong());
    }

    public function testKurangiProdukYangTidakAdaTidakError(): void
    {
        $penyimpanan = [];
        $keranjang = new Keranjang($penyimpanan);

        $keranjang->kurangi(99, 1);

        $this->assertTrue($keranjang->kosong());
    }

    public function testKurangiQtyNolDitolak(): void
    {
        $penyimpanan = [];
        $keranjang = new Keranjang($penyimpanan);
        $keranjang->tambah(1, 2);

        $this->expectException(InvalidArgumentException::class);
        $keranjang->kurangi(1, 0);
    }

    public function testHapusDanKosongkan(): void
    {
        $penyimpanan = [];
        $keranjang = new Keranjang($penyimpanan);
        $keranjang->tambah(1, 1);
        $keranjang->tambah(2, 1);

        $keranjang->hapus(1);
        $this->assertSame([2 => 1], $keranjang->semua());

        $keranjang->kosongkan();
        $this->assertTrue($keranjang->kosong());
    }

    public function testPenyimpananLuarIkutBerubah(): void
    {
        $penyimpanan = [];
        $keranjang = new Keranjang($penyimpanan);

        $keranjang->tambah(7, 4);

        $this->assertSame([7 => 4], $penyimpanan);
    }
        public function testUbahMenimpaQtyLama(): void
    {
        $penyimpanan = [];
        $keranjang = new Keranjang($penyimpanan);
        $keranjang->tambah(1, 2);

        $keranjang->ubah(1, 10);

        $this->assertSame(10, $keranjang->qty(1));
    }

    public function testUbahKeNolMenghapusItem(): void
    {
        $penyimpanan = [];
        $keranjang = new Keranjang($penyimpanan);
        $keranjang->tambah(1, 2);

        $keranjang->ubah(1, 0);

        $this->assertTrue($keranjang->kosong());
    }

    public function testUbahNegatifMenghapusItem(): void
    {
        $penyimpanan = [];
        $keranjang = new Keranjang($penyimpanan);
        $keranjang->tambah(1, 2);

        $keranjang->ubah(1, -3);

        $this->assertSame(0, $keranjang->qty(1));
    }
}