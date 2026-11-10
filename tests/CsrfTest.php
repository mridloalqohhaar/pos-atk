<?php

declare(strict_types=1);

namespace Tests;

use App\Csrf;
use PHPUnit\Framework\TestCase;

class CsrfTest extends TestCase
{
    public function testTokenKonsistenDalamSatuSession(): void
    {
        $session = [];
        $csrf = new Csrf($session);

        $this->assertSame($csrf->token(), $csrf->token());
        $this->assertSame(64, strlen($csrf->token()));
    }

    public function testTokenBenarDiterima(): void
    {
        $session = [];
        $csrf = new Csrf($session);

        $this->assertTrue($csrf->cek($csrf->token()));
    }

    public function testTokenSalahDitolak(): void
    {
        $session = [];
        $csrf = new Csrf($session);
        $csrf->token();

        $this->assertFalse($csrf->cek('token-palsu'));
    }

    public function testTokenKosongAtauNullDitolak(): void
    {
        $session = [];
        $csrf = new Csrf($session);
        $csrf->token();

        $this->assertFalse($csrf->cek(null));
        $this->assertFalse($csrf->cek(''));
    }

    public function testTanpaTokenDiSessionSemuaDitolak(): void
    {
        $session = [];
        $csrf = new Csrf($session);

        $this->assertFalse($csrf->cek('apa-saja'));
    }
}