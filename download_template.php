<?php
// Set headers for Excel download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="template_produksi_hhbk.csv"');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Create file pointer connected to the output stream
$output = fopen('php://output', 'w');

// NO BOM - Excel can handle UTF-8 without BOM
// fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write headers manually to avoid any CSV formatting issues
fwrite($output, "periode,kabupaten,kecamatan,desa,kth,penyuluh,komoditas,kategori,qty,satuan\n");

// Add sample data with proper CSV formatting - NO QUOTES for simple data
$sampleData = [
    ['2024-01-15', 'Kabupaten Bojonegoro', 'Donorojo', 'Belah', '', '', 'Bambu', 'HHBK', '100.500', 'Kilogram'],
    ['2024-01-16', 'Kabupaten Bojonegoro', 'Donorojo', 'Donorojo', '', '', 'Getah Pinus', 'HHBK', '50.250', 'Liter'],
    ['2024-01-17', 'Kabupaten Bojonegoro', 'Donorojo', 'Gedompol', '', '', 'Daun Kayu Putih', 'HHBK', '25.750', 'Kilogram'],
    ['2024-01-18', 'Kabupaten Bojonegoro', 'Donorojo', 'Gendaran', '', '', 'Porang', 'HHBK', '75.300', 'Kilogram'],
    ['2024-01-19', 'Kabupaten Bojonegoro', 'Donorojo', 'Kalak', '', '', 'Alpukat', 'HHBK', '200.000', 'Batang'],
    ['2024-01-20', 'Kabupaten Bojonegoro', 'Donorojo', 'Sawahan', '', '', 'Jahe', 'HHBK', '45.600', 'Kilogram'],
    ['2024-01-21', 'Kabupaten Bojonegoro', 'Donorojo', 'Sekar', '', '', 'Kayu Jati', 'HHK', '150.000', 'Batang'],
    ['2024-01-22', 'Kabupaten Bojonegoro', 'Donorojo', 'Sendang', '', '', 'Kayu Mahoni', 'HHK', '75.500', 'Batang']
];

// Write data rows manually to ensure clean format
foreach ($sampleData as $row) {
    fwrite($output, implode(',', $row) . "\n");
}

// Close the file pointer
fclose($output);
exit;
?>
