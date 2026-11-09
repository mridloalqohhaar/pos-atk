<?php

declare(strict_types=1);

namespace Tests;

use App\KalkulatorTransaksi;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class KalkulatorTransaksiTest extends TestCase
{
    private KalkulatorTransaksi $kalkulator;

    protected function setUp(): void
    {
        $this->kalkulator = new KalkulatorTransaksi();
    }

    public function testContohDariSlide(): void
    {
        $items = [
            ['harga' => 20000, 'qty' => 2],
            ['harga' => 6000, 'qty' => 3],
        ];

        $hasil = $this->kalkulator->hitung($items);

        $this->assertEqualsWithDelta(58000, $hasil['subtotal'], 0.01);
        $this->assertEqualsWithDelta(5800, $hasil['diskon'], 0.01);
        $this->assertEqualsWithDelta(5220, $hasil['pajak'], 0.01);
        $this->assertEqualsWithDelta(57420, $hasil['total'], 0.01);
    }

    public function testSubtotalBeberapaItem(): void
    {
        $items = [
            ['harga' => 2500, 'qty' => 4],
            ['harga' => 1500, 'qty' => 2],
        ];

        $this->assertEqualsWithDelta(13000, $this->kalkulator->hitungSubtotal($items), 0.01);
    }

    public function testDiskonDiBatasTier(): void
    {
        $this->assertEqualsWithDelta(0, $this->kalkulator->hitungDiskon(24999), 0.01);
        $this->assertEqualsWithDelta(1250, $this->kalkulator->hitungDiskon(25000), 0.01);
        $this->assertEqualsWithDelta(2499.95, $this->kalkulator->hitungDiskon(49999), 0.01);
        $this->assertEqualsWithDelta(5000, $this->kalkulator->hitungDiskon(50000), 0.01);
        $this->assertEqualsWithDelta(9999.9, $this->kalkulator->hitungDiskon(99999), 0.01);
        $this->assertEqualsWithDelta(15000, $this->kalkulator->hitungDiskon(100000), 0.01);
    }

    public function testKeranjangKosongSemuaNol(): void
    {
        $hasil = $this->kalkulator->hitung([]);

        $this->assertEqualsWithDelta(0, $hasil['subtotal'], 0.01);
        $this->assertEqualsWithDelta(0, $hasil['diskon'], 0.01);
        $this->assertEqualsWithDelta(0, $hasil['pajak'], 0.01);
        $this->assertEqualsWithDelta(0, $hasil['total'], 0.01);
    }

    public function testSubtotalBesar(): void
    {
        $hasil = $this->kalkulator->hitung([['harga' => 50000, 'qty' => 5]]);

        $this->assertEqualsWithDelta(250000, $hasil['subtotal'], 0.01);
        $this->assertEqualsWithDelta(37500, $hasil['diskon'], 0.01);
        $this->assertEqualsWithDelta(21250, $hasil['pajak'], 0.01);
        $this->assertEqualsWithDelta(233750, $hasil['total'], 0.01);
    }

    public function testQtyNegatifMelemparException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->kalkulator->hitungSubtotal([['harga' => 1000, 'qty' => -1]]);
    }
}