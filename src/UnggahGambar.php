<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

/**
 * Memeriksa dan menyimpan gambar yang diunggah lewat form.
 */
class UnggahGambar
{
    /** Tipe file yang diizinkan beserta ekstensi yang dipakai. */
    private const MIME_DIIZINKAN = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    /** Ukuran maksimal gambar: 2 MB. */
    private const UKURAN_MAKS = 2 * 1024 * 1024;

    private string $folder;

    /**
     * @param string $folder Folder tujuan penyimpanan gambar
     */
    public function __construct(string $folder)
    {
        $this->folder = rtrim($folder, '/\\');
    }

    /**
     * Mengecek apakah pengguna memilih file pada form.
     *
     * @param array<string, mixed> $file Isi $_FILES['nama_input']
     */
    public function adaFile(array $file): bool
    {
        return ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * Memvalidasi file yang diunggah.
     *
     * @param array<string, mixed> $file Isi $_FILES['nama_input']
     * @return string|null Pesan error, atau null jika valid atau tidak ada file
     */
    public function validasi(array $file): ?string
    {
        if (!$this->adaFile($file)) {
            return null;
        }

        $error = $file['error'];

        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            return 'Ukuran gambar maksimal 2 MB.';
        }
        if ($error !== UPLOAD_ERR_OK) {
            return 'Gambar gagal diunggah. Coba lagi.';
        }
        if (($file['size'] ?? 0) > self::UKURAN_MAKS) {
            return 'Ukuran gambar maksimal 2 MB.';
        }
        if ($this->deteksiMime((string) $file['tmp_name']) === null) {
            return 'Format gambar harus JPG, PNG, atau WEBP.';
        }

        return null;
    }

    /**
     * Menyimpan gambar ke folder tujuan dengan nama acak.
     * Nama asli dari pengguna tidak dipakai agar aman.
     *
     * @param array<string, mixed> $file Isi $_FILES['nama_input']
     * @return string|null Nama file baru, atau null jika tidak ada file valid
     * @throws RuntimeException Jika file gagal dipindahkan
     */
    public function simpan(array $file): ?string
    {
        if (!$this->adaFile($file) || $this->validasi($file) !== null) {
            return null;
        }

        $mime = $this->deteksiMime((string) $file['tmp_name']);
        $nama = bin2hex(random_bytes(8)) . '.' . self::MIME_DIIZINKAN[$mime];

        if (!is_dir($this->folder)) {
            mkdir($this->folder, 0775, true);
        }

        if (!move_uploaded_file((string) $file['tmp_name'], $this->folder . DIRECTORY_SEPARATOR . $nama)) {
            throw new RuntimeException('Gagal menyimpan gambar.');
        }

        return $nama;
    }

    /**
     * Menghapus file gambar dari folder. Aman jika file tidak ada.
     *
     * @param string|null $nama Nama file yang tersimpan di database
     */
    public function hapus(?string $nama): void
    {
        if ($nama === null || $nama === '') {
            return;
        }

        $path = $this->folder . DIRECTORY_SEPARATOR . basename($nama);

        if (is_file($path)) {
            unlink($path);
        }
    }

    /**
     * Mendeteksi tipe file dari isinya, bukan dari ekstensi.
     *
     * @return string|null Tipe MIME yang diizinkan, atau null jika bukan gambar yang diizinkan
     */
    private function deteksiMime(string $path): ?string
    {
        if (!is_file($path)) {
            return null;
        }

        $info = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($info, $path);
        finfo_close($info);

        return isset(self::MIME_DIIZINKAN[$mime]) ? $mime : null;
    }
}