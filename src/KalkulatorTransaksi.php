<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

/**
 * Menghitung subtotal, diskon bertingkat, pajak, dan total transaksi.
 * Class ini murni logika: tanpa database dan tanpa HTML, sehingga mudah diuji.
 */
class KalkulatorTransaksi
{
    private const PAJAK = 0.10;

    /**
     * Tier diskon, diurutkan dari batas terbesar ke terkecil.
     *
     * @var array<int, array{min: int, persen: float}>
     */
    private array $tierDiskon = [
        ['min' => 100000, 'persen' => 0.15],
        ['min' => 50000,  'persen' => 0.10],
        ['min' => 25000,  'persen' => 0.05],
    ];

    /**
     * Menjumlahkan harga x qty dari semua item keranjang.
     *
     * @param array<int, array{harga: int|float, qty: int}> $items Isi keranjang
     * @return float Subtotal sebelum diskon
     * @throws InvalidArgumentException Jika harga atau qty negatif
     */
    public function hitungSubtotal(array $items): float
    {
        $subtotal = 0.0;

        foreach ($items as $item) {
            if ($item['harga'] < 0 || $item['qty'] < 0) {
                throw new InvalidArgumentException('Harga dan qty tidak boleh negatif.');
            }
            $subtotal += $item['harga'] * $item['qty'];
        }

        return round($subtotal, 2);
    }

    /**
     * Menghitung diskon bertingkat berdasarkan subtotal.
     *
     * @param float $subtotal Total harga sebelum diskon
     * @return float Nilai diskon dalam rupiah
     */
    public function hitungDiskon(float $subtotal): float
    {
        foreach ($this->tierDiskon as $tier) {
            if ($subtotal >= $tier['min']) {
                return round($subtotal * $tier['persen'], 2);
            }
        }

        return 0.0;
    }

    /**
     * Menghitung pajak 10% dari subtotal setelah diskon.
     *
     * @param float $subtotal Total harga sebelum diskon
     * @param float $diskon   Nilai diskon dalam rupiah
     * @return float Nilai pajak dalam rupiah
     */
    public function hitungPajak(float $subtotal, float $diskon): float
    {
        return round(($subtotal - $diskon) * self::PAJAK, 2);
    }

    /**
     * Menghitung seluruh komponen transaksi sekaligus.
     *
     * @param array<int, array{harga: int|float, qty: int}> $items Isi keranjang
     * @return array{subtotal: float, diskon: float, pajak: float, total: float}
     */
    public function hitung(array $items): array
    {
        $subtotal = $this->hitungSubtotal($items);
        $diskon = $this->hitungDiskon($subtotal);
        $pajak = $this->hitungPajak($subtotal, $diskon);

        return [
            'subtotal' => $subtotal,
            'diskon'   => $diskon,
            'pajak'    => $pajak,
            'total'    => round($subtotal - $diskon + $pajak, 2),
        ];
    }
}