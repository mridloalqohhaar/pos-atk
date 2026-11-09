<?php

require __DIR__ . '/../bootstrap.php';

use App\Pengguna;

$pengguna = new Pengguna($db);

if ($pengguna->cariByUsername('admin') !== null) {
    echo "Akun admin sudah ada.\n";
    exit;
}

$pengguna->buat('admin', 'admin123', 'Administrator');
echo "Berhasil. Login dengan username: admin, password: admin123\n";