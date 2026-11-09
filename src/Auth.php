<?php

declare(strict_types=1);

namespace App;

/**
 * Mengelola status login pengguna di dalam session.
 * Array session diberikan dari luar lewat reference, sehingga mudah diuji.
 */
class Auth
{
    /** @var array<string, mixed> */
    private array $session;

    /**
     * @param array<string, mixed> $session Biasanya $_SESSION
     */
    public function __construct(array &$session)
    {
        $this->session = &$session;
    }

    /**
     * Menandai pengguna sebagai sudah login. Password tidak ikut disimpan.
     *
     * @param array<string, mixed> $pengguna Baris dari tabel pengguna
     */
    public function masuk(array $pengguna): void
    {
        $this->session['pengguna'] = [
            'id_pengguna' => (int) $pengguna['id_pengguna'],
            'username'    => (string) $pengguna['username'],
            'nama'        => (string) $pengguna['nama'],
        ];
    }

    /**
     * Mengecek apakah ada pengguna yang sedang login.
     */
    public function cek(): bool
    {
        return isset($this->session['pengguna']);
    }

    /**
     * Mengambil data pengguna yang sedang login.
     *
     * @return array{id_pengguna: int, username: string, nama: string}|null
     */
    public function pengguna(): ?array
    {
        return $this->session['pengguna'] ?? null;
    }

    /**
     * Mengeluarkan pengguna (logout).
     */
    public function keluar(): void
    {
        unset($this->session['pengguna']);
    }
}