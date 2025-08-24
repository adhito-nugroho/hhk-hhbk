<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitur Import Data - Sistem Informasi Pengelolaan HHK dan HHBK</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-green-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold">Sistem Informasi Pengelolaan HHK dan HHBK</h1>
                    <span class="text-green-200">Hasil Hutan Kayu dan Bukan Kayu</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="hover:text-green-200">Dashboard</a>
                    <a href="produksi.php" class="hover:text-green-200">Produksi</a>
                    <a href="komoditas.php" class="hover:text-green-200">Komoditas</a>
                    <a href="wilayah.php" class="hover:text-green-200">Wilayah</a>
                    <a href="laporan.php" class="hover:text-green-200">Laporan</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Fitur Import Data Produksi</h1>
            <p class="text-xl text-gray-600">Pilih metode input data yang sesuai dengan kebutuhan Anda</p>
        </div>

        <!-- Feature Cards -->
        <div class="grid md:grid-cols-2 gap-8 mb-8">
            <!-- Input Manual Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center mb-4">
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 ml-4">Input Manual</h3>
                </div>
                
                <p class="text-gray-600 mb-4">
                    Input data produksi satu per satu melalui form aplikasi yang user-friendly dengan validasi real-time.
                </p>
                
                <ul class="text-gray-600 mb-6 space-y-2">
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Form input yang lengkap dan mudah digunakan
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Validasi duplikasi data otomatis
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Field sumber otomatis terisi "input"
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Cocok untuk input data dalam jumlah kecil
                    </li>
                </ul>
                
                <a href="produksi.php" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                    Mulai Input Manual
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>

            <!-- Import Excel Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 ml-4">Import Excel</h3>
                </div>
                
                <p class="text-gray-600 mb-4">
                    Import data produksi dalam jumlah besar dari file Excel atau CSV dengan validasi otomatis.
                </p>
                
                <ul class="text-gray-600 mb-6 space-y-2">
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Support format .xls, .xlsx, dan .csv
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Template Excel yang bisa didownload
                    </li>
                                             <li class="flex items-center">
                             <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                             </svg>
                             Validasi data, kategori (HHK/HHBK), dan duplikasi otomatis
                         </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Field sumber otomatis terisi "import"
                    </li>
                </ul>
                
                <a href="import_produksi.php" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Mulai Import Excel
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Aksi Cepat</h3>
            <div class="grid md:grid-cols-3 gap-4">
                <a href="download_template.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <h4 class="font-medium text-gray-900">Download Template</h4>
                        <p class="text-sm text-gray-600">Template Excel untuk import data</p>
                    </div>
                </a>
                
                <a href="template_produksi_hhbk.csv" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <h4 class="font-medium text-gray-900">Lihat Contoh CSV</h4>
                        <p class="text-sm text-gray-600">Format data yang benar</p>
                    </div>
                </a>
                
                <a href="IMPORT_PRODUKSI_README.md" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <h4 class="font-medium text-gray-900">Dokumentasi</h4>
                        <p class="text-sm text-gray-600">Panduan lengkap fitur import</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Info Section -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-blue-900 mb-3">Informasi Penting</h3>
            <div class="grid md:grid-cols-2 gap-6 text-sm text-blue-800">
                <div>
                    <h4 class="font-medium mb-2">Validasi Duplikasi</h4>
                    <p>Sistem akan mencegah input data dengan kombinasi periode, wilayah, dan komoditas yang sama untuk menjaga integritas data.</p>
                </div>
                <div>
                    <h4 class="font-medium mb-2">Field Sumber Otomatis</h4>
                    <p>Field "sumber" akan otomatis terisi "input" untuk form manual dan "import" untuk data dari Excel, memudahkan tracking sumber data.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
