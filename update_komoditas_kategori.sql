-- Update komoditas kategori untuk database yang sudah ada
-- Jalankan script ini jika database sudah ada dan ingin menambah kategori

-- Update struktur tabel komoditas
ALTER TABLE `komoditas` MODIFY COLUMN `kategori` enum('HHK','HHBK') NOT NULL DEFAULT 'HHBK';

-- Update data komoditas yang sudah ada
UPDATE `komoditas` SET `kategori` = 'HHBK' WHERE `nama` IN ('Bambu', 'Getah Pinus', 'Daun Kayu Putih', 'Porang', 'Alpukat', 'Jahe', 'Kunyit', 'HHBK Lainnya');

-- Tambah komoditas HHK baru
INSERT INTO `komoditas` (`nama`, `kategori`, `aktif`) VALUES 
('Kayu Jati', 'HHK', 1),
('Kayu Mahoni', 'HHK', 1),
('Kayu Sengon', 'HHK', 1),
('Kayu Akasia', 'HHK', 1),
('Kayu Pinus', 'HHK', 1),
('HHK Lainnya', 'HHK', 1);

-- Tambah komoditas HHBK tambahan jika diperlukan
INSERT INTO `komoditas` (`nama`, `kategori`, `aktif`) VALUES 
('Madu Hutan', 'HHBK', 1),
('Rotan', 'HHBK', 1),
('Damar', 'HHBK', 1),
('Kemenyan', 'HHBK', 1),
('Gaharu', 'HHBK', 1);

-- Verifikasi hasil update
SELECT `nama`, `kategori`, `aktif` FROM `komoditas` ORDER BY `kategori`, `nama`;
