<?php

declare(strict_types=1);

use App\Database;

require __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/config/database.php';
$db = new Database($config);

/**
 * Mengamankan teks sebelum ditampilkan di HTML (mencegah XSS).
 */
function e(mixed $nilai): string
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

/**
 * Memformat angka menjadi Rupiah, misalnya "Rp 58.000".
 */
function rupiah(float|int $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}