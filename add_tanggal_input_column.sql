-- Menambahkan kolom tanggal_input ke tabel produksi
ALTER TABLE `produksi` 
ADD COLUMN `tanggal_input` date NULL DEFAULT NULL AFTER `sumber`;

-- Update data yang sudah ada dengan tanggal input default
UPDATE `produksi` SET `tanggal_input` = `created_at` WHERE `tanggal_input` IS NULL;

-- Menambahkan index untuk kolom tanggal_input
ALTER TABLE `produksi` 
ADD INDEX `idx_produksi_tanggal_input` (`tanggal_input`);
