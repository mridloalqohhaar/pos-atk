<?php

declare(strict_types=1);

namespace Tests;

use App\UnggahGambar;
use PHPUnit\Framework\TestCase;

class UnggahGambarTest extends TestCase
{
    private UnggahGambar $unggah;

    /** @var array<int, string> */
    private array $berkasSementara = [];

    protected function setUp(): void
    {
        $this->unggah = new UnggahGambar(sys_get_temp_dir());
    }

    protected function tearDown(): void
    {
        foreach ($this->berkasSementara as $berkas) {
            if (is_file($berkas)) {
                unlink($berkas);
            }
        }
    }

    private function buatBerkas(string $isi): string
    {
        $path = (string) tempnam(sys_get_temp_dir(), 'tes');
        file_put_contents($path, $isi);
        $this->berkasSementara[] = $path;

        return $path;
    }

    private function pngKecil(): string
    {
        return (string) base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        );
    }

    public function testTanpaFileDianggapValid(): void
    {
        $this->assertNull($this->unggah->validasi(['error' => UPLOAD_ERR_NO_FILE]));
    }

    public function testGambarPngValidDiterima(): void
    {
        $path = $this->buatBerkas($this->pngKecil());

        $pesan = $this->unggah->validasi([
            'error' => UPLOAD_ERR_OK, 'size' => filesize($path), 'tmp_name' => $path, 'name' => 'a.png',
        ]);

        $this->assertNull($pesan);
    }

    public function testFileTeksYangMenyamarJadiJpgDitolak(): void
    {
        $path = $this->buatBerkas('ini bukan gambar');

        $pesan = $this->unggah->validasi([
            'error' => UPLOAD_ERR_OK, 'size' => filesize($path), 'tmp_name' => $path, 'name' => 'palsu.jpg',
        ]);

        $this->assertNotNull($pesan);
        $this->assertStringContainsString('JPG', $pesan);
    }

    public function testUkuranLebihDari2MbDitolak(): void
    {
        $path = $this->buatBerkas($this->pngKecil());

        $pesan = $this->unggah->validasi([
            'error' => UPLOAD_ERR_OK, 'size' => 3 * 1024 * 1024, 'tmp_name' => $path, 'name' => 'besar.png',
        ]);

        $this->assertStringContainsString('2 MB', (string) $pesan);
    }

    public function testErrorUkuranDariPhpDitolak(): void
    {
        $pesan = $this->unggah->validasi(['error' => UPLOAD_ERR_INI_SIZE]);

        $this->assertStringContainsString('2 MB', (string) $pesan);
    }

    public function testHapusMenghilangkanFileDanAmanJikaNull(): void
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tes_hapus_gambar.png';
        file_put_contents($path, $this->pngKecil());

        $this->unggah->hapus('tes_hapus_gambar.png');
        $this->unggah->hapus(null);

        $this->assertFileDoesNotExist($path);
    }
}