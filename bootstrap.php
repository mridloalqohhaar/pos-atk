<?php

declare(strict_types=1);

use App\Auth;
use App\Csrf;
use App\Database;

require __DIR__ . '/vendor/autoload.php';

/**
 * Gambar latar untuk login dan dashboard.
 * Bisa file lokal ('img/latar.svg') atau URL online foto.
 * Kosongkan ('') untuk memakai gradasi + ikon saja.
 */
const GAMBAR_LATAR = 'img/latar.svg';

date_default_timezone_set('Asia/Jakarta');

if (PHP_SAPI !== 'cli') {
    session_start();
}
$_SESSION ??= [];

$config = require __DIR__ . '/config/database.php';
$db = new Database($config);
$auth = new Auth($_SESSION);
$csrf = new Csrf($_SESSION);

// Semua permintaan POST wajib membawa token CSRF yang cocok.
if (PHP_SAPI !== 'cli' && $_SERVER['REQUEST_METHOD'] === 'POST' && !$csrf->cek($_POST['_token'] ?? null)) {
    http_response_code(419);
    exit('Sesi formulir tidak valid atau sudah kedaluwarsa. Kembali, muat ulang halaman, lalu coba lagi.');
}

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

/**
 * Menjaga halaman: jika belum login, arahkan ke halaman login.
 */
function wajibLogin(Auth $auth): void
{
    if (!$auth->cek()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Membuat isi atribut style untuk gambar latar.
 * Hasilnya kosong jika GAMBAR_LATAR belum diisi.
 */
function gayaLatar(): string
{
    if (GAMBAR_LATAR === '') {
        return '';
    }

    return "--gambar: url('" . str_replace("'", '%27', GAMBAR_LATAR) . "')";
}