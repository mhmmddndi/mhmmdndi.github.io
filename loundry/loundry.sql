

-- Buat tabel cabang
CREATE TABLE `cabang` (
  `id_cabang` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_cabang` VARCHAR(255) NOT NULL,
  `longitude` DECIMAL(10,6) NOT NULL,
  `latitude` DECIMAL(10,6) NOT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `keterangan` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Buat tabel layanan
CREATE TABLE `layanan` (
  `id_layanan` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_layanan` VARCHAR(50) NOT NULL,
  `harga_per_kg` DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Buat tabel login
CREATE TABLE `login` (
  `id_login` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_md5` CHAR(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Buat tabel pelanggan
CREATE TABLE `pelanggan` (
  `id_pelanggan` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_pelanggan` VARCHAR(100) NOT NULL,
  `no_telepon` VARCHAR(15) NOT NULL,
  `alamat` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Buat tabel pesanan
CREATE TABLE `pesanan` (
  `id_pesanan` INT AUTO_INCREMENT PRIMARY KEY,
  `id_pelanggan` INT NOT NULL,
  `tanggal_pesanan` DATE NOT NULL DEFAULT CURDATE(),
  `total_harga` DECIMAL(10,2) DEFAULT NULL,
  FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan`(`id_pelanggan`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Buat tabel detail_pesanan
CREATE TABLE `detail_pesanan` (
  `id_detail` INT AUTO_INCREMENT PRIMARY KEY,
  `id_pelanggan` INT NOT NULL,
  `id_pesanan` INT NOT NULL,
  `id_layanan` INT NOT NULL,
  `berat_kg` DECIMAL(5,2) NOT NULL,
  `sub_total` DECIMAL(10,2) DEFAULT NULL,
  FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan`(`id_pelanggan`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan`(`id_pesanan`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (`id_layanan`) REFERENCES `layanan`(`id_layanan`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data awal untuk cabang
INSERT INTO `cabang` (`nama_cabang`, `longitude`, `latitude`, `foto`, `keterangan`) VALUES
('Cabang 1', 106.968218, -6.399263, NULL, 'Cabang utama');

-- Insert data awal untuk layanan
INSERT INTO `layanan` (`nama_layanan`, `harga_per_kg`) VALUES
('Cuci kering', 5000.00),
('Cuci Setrika', 8000.00),
('Setrika Saja', 3000.00);

-- Insert data awal untuk login
INSERT INTO `login` (`username`, `password_md5`) VALUES
('dandi', '202cb962ac59075b964b07152d234b70'), -- password: 123
('ilham', '202cb962ac59075b964b07152d234b70'); -- password: 123

-- Insert data awal untuk pelanggan
INSERT INTO `pelanggan` (`nama_pelanggan`, `no_telepon`, `alamat`) VALUES
('Ilham', '081234567890', 'Jl. Mawar No. 10'),
('Budi', '081987654321', 'Jl. Melati No. 5');

-- Insert data awal untuk pesanan
INSERT INTO `pesanan` (`id_pelanggan`, `tanggal_pesanan`, `total_harga`) VALUES
(1, '2024-11-18', 17500.00),
(2, '2024-11-19', 20500.00);

-- Insert data awal untuk detail_pesanan
INSERT INTO `detail_pesanan` (`id_pelanggan`, `id_pesanan`, `id_layanan`, `berat_kg`, `sub_total`) VALUES
(1, 1, 1, 3.50, 17500.00),
(2, 2, 2, 2.00, 16000.00);

-- Buat Trigger untuk menghitung sub_total sebelum insert ke detail_pesanan
DELIMITER $$
CREATE TRIGGER `calculate_sub_total`
BEFORE INSERT ON `detail_pesanan`
FOR EACH ROW
BEGIN
    DECLARE harga DECIMAL(10,2);
    SELECT `harga_per_kg` INTO harga
    FROM `layanan`
    WHERE `id_layanan` = NEW.`id_layanan`;
    SET NEW.`sub_total` = NEW.`berat_kg` * harga;
END;
$$
DELIMITER ;
