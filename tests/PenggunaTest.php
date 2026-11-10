<?php

declare(strict_types=1);

namespace Tests;

use App\Database;
use App\Pengguna;
use PDO;
use PHPUnit\Framework\TestCase;

class PenggunaTest extends TestCase
{
    private Pengguna $pengguna;

    protected function setUp(): void
    {
        $pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec(
            'CREATE TABLE pengguna (
                id_pengguna INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                nama TEXT NOT NULL
            )'
        );

        $db = $this->createStub(Database::class);
        $db->method('getConnection')->willReturn($pdo);

        $this->pengguna = new Pengguna($db);
    }

    public function testPasswordDisimpanSebagaiHash(): void
    {
        $this->pengguna->buat('kasir1', 'rahasia123', 'Kasir Satu');

        $baris = $this->pengguna->cariByUsername('kasir1');

        $this->assertNotSame('rahasia123', $baris['password']);
        $this->assertTrue(password_verify('rahasia123', $baris['password']));
    }

    public function testLoginBenar(): void
    {
        $this->pengguna->buat('kasir1', 'rahasia123', 'Kasir Satu');

        $hasil = $this->pengguna->login('kasir1', 'rahasia123');

        $this->assertNotNull($hasil);
        $this->assertSame('Kasir Satu', $hasil['nama']);
    }

    public function testLoginPasswordSalah(): void
    {
        $this->pengguna->buat('kasir1', 'rahasia123', 'Kasir Satu');

        $this->assertNull($this->pengguna->login('kasir1', 'salah'));
    }

    public function testLoginUsernameTidakAda(): void
    {
        $this->assertNull($this->pengguna->login('hantu', 'apa-saja'));
    }

    public function testCariUsernameTidakAda(): void
    {
        $this->assertNull($this->pengguna->cariByUsername('hantu'));
    }
}