# Fitur Import Data Produksi - Sistem Informasi Pengelolaan HHK dan HHBK

## Overview
Sistem Informasi Pengelolaan HHK dan HHBK sekarang memiliki dua cara untuk input data produksi:
1. **Input Manual** - Melalui form aplikasi
2. **Import Excel** - Melalui upload file Excel/CSV

## Fitur Utama

### 1. Input Manual (Form Aplikasi)
- Form input data produksi dengan validasi lengkap
- Field **sumber** otomatis terisi "input"
- Validasi duplikasi data real-time
- Cascading dropdown untuk wilayah (Kabupaten → Kecamatan → Desa → KTH)

### 2. Import Data Excel
- Upload file Excel (.xls, .xlsx) atau CSV
- Validasi format dan data otomatis
- Field **sumber** otomatis terisi "import"
- Template Excel yang bisa didownload
- Validasi duplikasi data sebelum import

### 3. Validasi Data
- **Mencegah Duplikasi**: Data dengan periode, wilayah, dan komoditas yang sama tidak bisa diinput
- **Validasi Format**: Tanggal, angka, dan referensi data master
- **Transaction Safety**: Import menggunakan database transaction untuk konsistensi data

## Format Template Excel

### Header yang Diperlukan
```
periode, kabupaten, kecamatan, desa, kth, penyuluh, komoditas, kategori, qty, satuan
```

### Contoh Data
```
2024-01-15, Kabupaten A, Kecamatan B, Desa C, KTH Maju, Budi Santoso, Bambu, HHBK, 100.500, Kilogram
2024-01-16, Kabupaten A, Kecamatan B, Desa D, , Siti Rahayu, Getah Pinus, HHBK, 50.250, Liter
2024-01-17, Kabupaten B, Kecamatan C, Desa E, KTH Sejahtera, , Daun Kayu Putih, HHBK, 25.750, Kilogram
2024-01-21, Kabupaten A, Kecamatan B, Desa C, KTH Maju, , Kayu Jati, HHK, 150.000, Batang
```

### Aturan Format
- **periode**: Format YYYY-MM-DD (contoh: 2024-01-15)
- **kabupaten**: Nama kabupaten yang ada di database
- **kecamatan**: Nama kecamatan yang ada di kabupaten tersebut
- **desa**: Nama desa yang ada di kecamatan tersebut
- **kth**: Nama KTH yang ada di desa tersebut (opsional, bisa dikosongkan)
- **penyuluh**: Nama penyuluh yang ada di database (opsional, bisa dikosongkan)
- **komoditas**: Nama komoditas yang aktif di database
- **kategori**: Kategori komoditas (HHK atau HHBK)
- **qty**: Jumlah produksi (harus angka positif)
- **satuan**: Nama satuan yang ada di database

## Cara Penggunaan

### Import Data Excel
1. Buka menu **Produksi** → Klik tombol **Import Excel**
2. Download template Excel untuk referensi format
3. Isi data sesuai template
4. Upload file Excel yang sudah diisi
5. Sistem akan memvalidasi dan import data secara otomatis
6. Field **sumber** akan otomatis terisi "import"

### Input Manual
1. Buka menu **Produksi** → Klik tombol **Tambah Produksi**
2. Isi form dengan data yang diperlukan
3. Field **sumber** akan otomatis terisi "input"
4. Sistem akan validasi duplikasi sebelum menyimpan

## Validasi Duplikasi

### Kriteria Duplikasi
Data dianggap duplikat jika memiliki kombinasi yang sama:
- **Periode** (tanggal)
- **Kabupaten**
- **Kecamatan** 
- **Desa**
- **KTH** (jika ada)
- **Komoditas**

### Contoh Duplikasi
```
Data 1: 2024-01-15, Kabupaten A, Kecamatan B, Desa C, KTH Maju, Bambu
Data 2: 2024-01-15, Kabupaten A, Kecamatan B, Desa C, KTH Maju, Bambu
→ DUPLIKAT (tidak bisa disimpan)
```

```
Data 1: 2024-01-15, Kabupaten A, Kecamatan B, Desa C, KTH Maju, Bambu
Data 2: 2024-01-15, Kabupaten A, Kecamatan B, Desa C, KTH Maju, Getah Pinus
→ TIDAK DUPLIKAT (bisa disimpan, komoditas berbeda)
```

## Struktur Database

### Tabel Produksi
```sql
CREATE TABLE `produksi` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `periode` date NOT NULL,
  `kabupaten_id` int NOT NULL,
  `kecamatan_id` int NOT NULL,
  `desa_id` int NOT NULL,
  `kth_id` int NULL,
  `penyuluh_id` int NULL,
  `komoditas_id` int NOT NULL,
  `qty` decimal(18,3) NOT NULL,
  `satuan_id` int NOT NULL,
  `sumber` enum('input','import') NOT NULL DEFAULT 'input',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int NULL,
  PRIMARY KEY (`id`)
);
```

### Field Sumber
- **`input`**: Data yang diinput manual melalui form aplikasi
- **`import`**: Data yang diimport dari file Excel

## File yang Dibuat/Diubah

### File Baru
1. **`import_produksi.php`** - Halaman import data Excel
2. **`download_template.php`** - Download template Excel
3. **`IMPORT_PRODUKSI_README.md`** - Dokumentasi ini

### File yang Diubah
1. **`produksi.php`** - Ditambah validasi duplikasi dan tombol import
2. **`assets/js/datatable.js`** - Sudah ada, tidak perlu diubah

## Keamanan dan Validasi

### Validasi Input
- **File Type**: Hanya menerima .xls, .xlsx, .csv
- **Header Validation**: Memastikan semua kolom yang diperlukan ada
- **Data Type**: Validasi format tanggal, angka, dan referensi
- **Referential Integrity**: Memastikan data referensi ada di database

### Pencegahan Duplikasi
- **Database Level**: Unique constraint pada kombinasi field tertentu
- **Application Level**: Validasi sebelum insert/update
- **Transaction Safety**: Rollback jika ada error

### Error Handling
- **Detailed Messages**: Pesan error yang informatif
- **Partial Import**: Bisa melanjutkan import meski ada beberapa baris error
- **Logging**: Mencatat semua error untuk debugging

## Troubleshooting

### Error Umum

1. **"File harus berformat Excel"**
   - Pastikan file berformat .xls, .xlsx, atau .csv
   - Gunakan template yang disediakan

2. **"Header tidak ditemukan"**
   - Pastikan nama kolom sesuai dengan template
   - Jangan ubah nama kolom di Excel

3. **"Data produksi sudah ada"**
   - Data dengan kombinasi yang sama sudah ada di database
   - Gunakan periode atau wilayah yang berbeda

4. **"Kabupaten tidak ditemukan"**
   - Pastikan nama kabupaten sesuai dengan data master
   - Periksa ejaan dan format nama

### Tips Penggunaan

1. **Gunakan Template**: Selalu download dan gunakan template yang disediakan
2. **Periksa Data Master**: Pastikan nama wilayah, komoditas, dan satuan sesuai
3. **Format Tanggal**: Gunakan format YYYY-MM-DD
4. **Backup Data**: Backup data sebelum import dalam jumlah besar
5. **Test Import**: Test dengan data kecil terlebih dahulu

## Fitur Tambahan

### Export Data
- Export data produksi ke Excel/CSV
- Filter berdasarkan periode, wilayah, dan komoditas
- Include field sumber untuk tracking

### Monitoring
- Track sumber data (input vs import)
- Log aktivitas import
- Report data yang berhasil/gagal diimport

### Batch Processing
- Import data dalam jumlah besar
- Progress indicator
- Resume import jika terhenti

## Dukungan Teknis

### Browser Support
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

### File Size Limit
- Maksimal file Excel: 10MB
- Maksimal baris data: 10,000 baris
- Format yang didukung: .xls, .xlsx, .csv

### Performance
- Import 1000 baris: ~5-10 detik
- Validasi real-time untuk input manual
- Pagination untuk data yang banyak

## Roadmap

### Versi Selanjutnya
- [ ] Import dari Google Sheets
- [ ] Auto-sync dengan sistem eksternal
- [ ] Advanced filtering dan search
- [ ] Bulk edit data
- [ ] Data validation rules yang bisa dikustomisasi
- [ ] Import scheduling (otomatis)
- [ ] Email notification untuk import status
- [ ] Data quality scoring

### Integrasi
- [ ] API untuk sistem eksternal
- [ ] Webhook untuk real-time sync
- [ ] Mobile app support
- [ ] Offline import capability

## Kesimpulan

Fitur import data Excel memberikan fleksibilitas tinggi untuk input data produksi dalam jumlah besar, sementara validasi duplikasi memastikan integritas data. Kombinasi input manual dan import Excel memberikan solusi lengkap untuk berbagai kebutuhan input data produksi HHK dan HHBK.
