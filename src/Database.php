<?php

declare(strict_types=1);

namespace App;

use PDO;

/**
 * Membungkus koneksi PDO ke MySQL.
 */
class Database
{
    private PDO $pdo;

    /**
     * @param array{host: string, dbname: string, user: string, pass: string, charset: string} $config
     */
    public function __construct(array $config)
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['dbname'],
            $config['charset']
        );

        $this->pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    /**
     * Mengambil objek koneksi PDO.
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}