# Cara Import Data Excel ke Sistem Produksi

## 🚀 Akses Fitur Import

### Opsi 1: Dari Halaman Produksi
1. Buka halaman **Data Produksi** (`produksi.php`)
2. Klik tombol **"Import Excel"** (warna orange) di bagian atas halaman
3. Anda akan diarahkan ke halaman import Excel

### Opsi 2: Dari Halaman Pilihan Input
1. Buka halaman **Fitur Import Data** (`index_import.php`)
2. Pilih card **"Import Excel"**
3. Klik tombol **"Mulai Import Excel"**

## 📋 Langkah-langkah Import

### 1. Download Template
- Klik **"Download Template Excel"** untuk mendapatkan format yang benar
- Template berisi contoh data dan header yang diperlukan

### 2. Siapkan File Excel
- Format yang didukung: `.xls`, `.xlsx`, `.csv`
- Pastikan header sesuai dengan template
- Isi data sesuai format yang ditentukan

### 3. Upload dan Import
- Klik **"Pilih File Excel"**
- Pilih file yang sudah disiapkan
- Klik **"Import Data"**
- Sistem akan memvalidasi dan mengimport data

## 📊 Format Data yang Diperlukan

| Kolom | Deskripsi | Contoh | Wajib |
|-------|-----------|--------|-------|
| periode | Tanggal produksi | 2024-01-15 | Ya |
| kabupaten | Nama kabupaten | Kabupaten A | Ya |
| kecamatan | Nama kecamatan | Kecamatan B | Ya |
| desa | Nama desa | Desa C | Ya |
| kth | Nama KTH | KTH Maju | Tidak |
| penyuluh | Nama penyuluh | Budi Santoso | Tidak |
| komoditas | Nama komoditas | Bambu | Ya |
| kategori | HHK atau HHBK | HHBK | Ya |
| qty | Jumlah produksi | 100.500 | Ya |
| satuan | Satuan ukuran | Kilogram | Ya |

## ⚠️ Validasi Otomatis

Sistem akan memvalidasi:
- ✅ Format tanggal (YYYY-MM-DD)
- ✅ Nama kabupaten, kecamatan, desa sesuai data master
- ✅ Nama komoditas dan kategori sesuai
- ✅ Quantity berupa angka positif
- ✅ Tidak ada data duplikat

## 🎯 Keunggulan Import Excel

1. **Efisien**: Import ratusan data sekaligus
2. **Validasi Otomatis**: Mencegah kesalahan input
3. **Template Lengkap**: Format yang sudah disiapkan
4. **Sumber Otomatis**: Field sumber terisi "import"
5. **Tanggal Input**: Otomatis terisi tanggal import

## 📞 Troubleshooting

### Jika ada error:
1. **Periksa format tanggal**: Harus YYYY-MM-DD
2. **Periksa nama wilayah**: Harus sesuai data master
3. **Periksa nama komoditas**: Harus sesuai data master
4. **Periksa kategori**: Hanya HHK atau HHBK
5. **Periksa quantity**: Harus angka positif

### Jika data tidak terimport:
1. **Periksa duplikasi**: Data dengan periode, wilayah, dan komoditas yang sama
2. **Periksa format file**: Pastikan format .xls, .xlsx, atau .csv
3. **Periksa header**: Pastikan header sesuai template

## 🔗 Link Terkait

- 📄 **Template Excel**: `download_template.php`
- 📋 **Contoh CSV**: `template_produksi_hhbk.csv`
- 📖 **Dokumentasi Lengkap**: `IMPORT_PRODUKSI_README.md`
- 🏠 **Halaman Pilihan**: `index_import.php`
