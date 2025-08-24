<?php
require_once 'config/database.php';

try {
    $db = getDatabaseConnection();
    
    // Menambahkan kolom tanggal_input ke tabel produksi
    $sql1 = "ALTER TABLE `produksi` ADD COLUMN `tanggal_input` date NULL DEFAULT NULL AFTER `sumber`";
    $result1 = $db->exec($sql1);
    
    if ($result1 !== false) {
        echo "✅ Kolom tanggal_input berhasil ditambahkan<br>";
    } else {
        echo "❌ Gagal menambahkan kolom tanggal_input<br>";
    }
    
    // Update data yang sudah ada dengan tanggal input default
    $sql2 = "UPDATE `produksi` SET `tanggal_input` = `created_at` WHERE `tanggal_input` IS NULL";
    $result2 = $db->exec($sql2);
    
    if ($result2 !== false) {
        echo "✅ Data existing berhasil diupdate dengan tanggal input default<br>";
    } else {
        echo "❌ Gagal mengupdate data existing<br>";
    }
    
    // Menambahkan index untuk kolom tanggal_input
    $sql3 = "ALTER TABLE `produksi` ADD INDEX `idx_produksi_tanggal_input` (`tanggal_input`)";
    $result3 = $db->exec($sql3);
    
    if ($result3 !== false) {
        echo "✅ Index untuk tanggal_input berhasil ditambahkan<br>";
    } else {
        echo "❌ Gagal menambahkan index (mungkin sudah ada)<br>";
    }
    
    echo "<br>🎉 Update database selesai!<br>";
    echo "<a href='produksi.php'>Kembali ke halaman Produksi</a>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
