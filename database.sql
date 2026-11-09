CREATE DATABASE IF NOT EXISTS pos_atk
    CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE pos_atk;

-- Hati-hati: import ulang akan menghapus semua data lama.
DROP TABLE IF EXISTS detail_transaksi, transaksi, produk, pengguna;

CREATE TABLE pengguna (
    id_pengguna INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    nama        VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE produk (
    id_produk   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(100) NOT NULL,
    harga       INT UNSIGNED NOT NULL,
    stok        INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE transaksi (
    id_transaksi INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tanggal      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    subtotal     DECIMAL(12,2) NOT NULL,
    diskon       DECIMAL(12,2) NOT NULL DEFAULT 0,
    pajak        DECIMAL(12,2) NOT NULL DEFAULT 0,
    total        DECIMAL(12,2) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE detail_transaksi (
    id_detail    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT UNSIGNED NOT NULL,
    id_produk    INT UNSIGNED NOT NULL,
    qty          INT UNSIGNED NOT NULL,
    harga_satuan INT UNSIGNED NOT NULL,
    subtotal     DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi (id_transaksi) ON DELETE CASCADE,
    FOREIGN KEY (id_produk)    REFERENCES produk (id_produk)
) ENGINE=InnoDB;

INSERT INTO produk (nama_produk, harga, stok) VALUES
('Pulpen Standard AE7 Hitam', 2500, 100),
('Pensil 2B Faber-Castell', 3000, 80),
('Penghapus Putih', 1500, 60),
('Buku Tulis Sidu 38 Lembar', 4500, 120),
('Kertas HVS A4 70gr (Rim)', 52000, 30),
('Stapler Kenko HD-10', 15000, 25),
('Lakban Bening 2 inch', 9000, 40),
('Map Plastik Folio', 2000, 90);