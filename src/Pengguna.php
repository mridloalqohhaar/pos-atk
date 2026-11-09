<?php

declare(strict_types=1);

namespace App;

use PDO;

/**
 * Mengelola data pengguna dan pemeriksaan login.
 */
class Pengguna
{
    private PDO $pdo;

    /**
     * Objek Database diterima lewat constructor (dependency injection).
     */
    public function __construct(Database $db)
    {
        $this->pdo = $db->getConnection();
    }

    /**
     * Mencari pengguna berdasarkan username.
     *
     * @return array<string, mixed>|null Null jika tidak ditemukan
     */
    public function cariByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM pengguna WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $baris = $stmt->fetch();

        return $baris === false ? null : $baris;
    }

    /**
     * Membuat pengguna baru. Password disimpan dalam bentuk hash, bukan teks asli.
     */
    public function buat(string $username, string $password, string $nama): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO pengguna (username, password, nama) VALUES (:username, :password, :nama)'
        );
        $stmt->execute([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'nama'     => $nama,
        ]);
    }

    /**
     * Memeriksa username dan password.
     *
     * @return array<string, mixed>|null Data pengguna jika cocok, null jika salah
     */
    public function login(string $username, string $password): ?array
    {
        $pengguna = $this->cariByUsername($username);

        if ($pengguna === null || !password_verify($password, $pengguna['password'])) {
            return null;
        }

        return $pengguna;
    }
}