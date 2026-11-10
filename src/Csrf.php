<?php

declare(strict_types=1);

namespace App;

/**
 * Membuat dan memeriksa token CSRF yang disimpan di session.
 * Array session diberikan dari luar lewat reference, sehingga mudah diuji.
 */
class Csrf
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
     * Mengambil token. Jika belum ada, dibuat token acak baru.
     */
    public function token(): string
    {
        if (empty($this->session['csrf_token'])) {
            $this->session['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $this->session['csrf_token'];
    }

    /**
     * Memeriksa apakah token kiriman form cocok dengan token di session.
     *
     * @param string|null $token Token dari form
     */
    public function cek(?string $token): bool
    {
        $asli = $this->session['csrf_token'] ?? null;

        return is_string($asli) && is_string($token) && $token !== '' && hash_equals($asli, $token);
    }
}