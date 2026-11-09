<?php

declare(strict_types=1);

namespace Tests;

use App\Auth;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    private function dataPengguna(): array
    {
        return [
            'id_pengguna' => 1,
            'username'    => 'admin',
            'password'    => 'hash-rahasia',
            'nama'        => 'Administrator',
        ];
    }

    public function testAwalnyaBelumLogin(): void
    {
        $session = [];
        $auth = new Auth($session);

        $this->assertFalse($auth->cek());
        $this->assertNull($auth->pengguna());
    }

    public function testMasukMenandaiLoginTanpaMenyimpanPassword(): void
    {
        $session = [];
        $auth = new Auth($session);

        $auth->masuk($this->dataPengguna());

        $this->assertTrue($auth->cek());
        $this->assertSame('Administrator', $auth->pengguna()['nama']);
        $this->assertArrayNotHasKey('password', $auth->pengguna());
    }

    public function testKeluarMenghapusLogin(): void
    {
        $session = [];
        $auth = new Auth($session);
        $auth->masuk($this->dataPengguna());

        $auth->keluar();

        $this->assertFalse($auth->cek());
    }
}