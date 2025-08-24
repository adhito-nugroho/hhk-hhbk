# Template Import Excel - Panduan Lengkap

## 📋 Overview
Template import Excel telah diperbaiki untuk menggunakan data master yang valid dari database. Template ini memastikan bahwa semua contoh data dapat diimport tanpa error.

## ✅ **Template Sudah Memenuhi Syarat:**

### 1. **Header yang Benar**
Template menggunakan header yang sesuai dengan validasi sistem:
```
periode,kabupaten,kecamatan,desa,kth,penyuluh,komoditas,kategori,qty,satuan
```

### 2. **Data Wilayah yang Valid**
Template menggunakan data wilayah yang benar-benar ada di database:
- **Kabupaten**: `Kabupaten Bojonegoro` (ID: 22)
- **Kecamatan**: `Donorojo` (ID: 1, di bawah Kabupaten Bojonegoro)
- **Desa**: Semua desa yang ada di Kecamatan Donorojo:
  - `Belah`
  - `Donorojo`
  - `Gedompol`
  - `Gendaran`
  - `Kalak`
  - `Sawahan`
  - `Sekar`
  - `Sendang`

### 3. **Data Komoditas yang Valid**
Template menggunakan komoditas yang terdaftar di database:

#### Komoditas HHBK:
- `Bambu` (ID: 1)
- `Getah Pinus` (ID: 2)
- `Daun Kayu Putih` (ID: 3)
- `Porang` (ID: 4)
- `Alpukat` (ID: 5)
- `Jahe` (ID: 6)

#### Komoditas HHK:
- `Kayu Jati` (ID: 9)
- `Kayu Mahoni` (ID: 10)

### 4. **Data Satuan yang Valid**
Template menggunakan satuan yang terdaftar di database:
- `Kilogram` (ID: 1, Kode: KG)
- `Batang` (ID: 2, Kode: BTG)
- `Liter` (ID: 3, Kode: LTR)

### 5. **Format Data yang Benar**
- **Periode**: Format YYYY-MM-DD (contoh: 2024-01-15)
- **Quantity**: Angka positif dengan desimal (contoh: 100.500)
- **Kategori**: HHK atau HHBK (sesuai dengan komoditas)

### 6. **Field Opsional yang Benar**
- **KTH**: Dikosongkan (karena belum ada data KTH di database)
- **Penyuluh**: Dikosongkan (karena belum ada data penyuluh di database)

## 📊 **Contoh Data dalam Template:**

| periode | kabupaten | kecamatan | desa | kth | penyuluh | komoditas | kategori | qty | satuan |
|---------|-----------|-----------|------|-----|----------|-----------|----------|-----|--------|
| 2024-01-15 | Kabupaten Bojonegoro | Donorojo | Belah | | | Bambu | HHBK | 100.500 | Kilogram |
| 2024-01-16 | Kabupaten Bojonegoro | Donorojo | Donorojo | | | Getah Pinus | HHBK | 50.250 | Liter |
| 2024-01-17 | Kabupaten Bojonegoro | Donorojo | Gedompol | | | Daun Kayu Putih | HHBK | 25.750 | Kilogram |
| 2024-01-18 | Kabupaten Bojonegoro | Donorojo | Gendaran | | | Porang | HHBK | 75.300 | Kilogram |
| 2024-01-19 | Kabupaten Bojonegoro | Donorojo | Kalak | | | Alpukat | HHBK | 200.000 | Batang |
| 2024-01-20 | Kabupaten Bojonegoro | Donorojo | Sawahan | | | Jahe | HHBK | 45.600 | Kilogram |
| 2024-01-21 | Kabupaten Bojonegoro | Donorojo | Sekar | | | Kayu Jati | HHK | 150.000 | Batang |
| 2024-01-22 | Kabupaten Bojonegoro | Donorojo | Sendang | | | Kayu Mahoni | HHK | 75.500 | Batang |

## 🔧 **Cara Menggunakan Template:**

### 1. **Download Template**
- Klik tombol "Download Template" di halaman import
- File akan didownload dengan nama `template_produksi_hhbk.csv`

### 2. **Buka di Excel**
- Buka file CSV di Microsoft Excel atau aplikasi spreadsheet lainnya
- Pastikan encoding UTF-8 untuk karakter Indonesia

### 3. **Isi Data**
- Ganti contoh data dengan data yang sebenarnya
- Pastikan format sesuai dengan contoh
- Jangan ubah nama kolom (header)

### 4. **Simpan dan Upload**
- Simpan file dalam format CSV atau Excel (.xlsx)
- Upload file ke sistem import

## ⚠️ **Penting untuk Diperhatikan:**

### 1. **Data Master Harus Lengkap**
Sebelum import, pastikan data master sudah lengkap:
- Data kabupaten, kecamatan, desa
- Data komoditas dengan kategori yang benar
- Data satuan
- Data KTH (jika akan digunakan)
- Data penyuluh (jika akan digunakan)

### 2. **Format Data**
- **Tanggal**: Harus YYYY-MM-DD
- **Angka**: Gunakan titik (.) untuk desimal, bukan koma
- **Kategori**: Harus sesuai dengan komoditas (HHK/HHBK)

### 3. **Hierarki Wilayah**
- Kecamatan harus berada di bawah kabupaten yang benar
- Desa harus berada di bawah kecamatan yang benar
- KTH harus berada di bawah desa yang benar

### 4. **Validasi Sistem**
Sistem akan memvalidasi:
- Format file dan header
- Keberadaan data master
- Konsistensi kategori komoditas
- Duplikasi data
- Format tanggal dan angka

## 🚀 **Keunggulan Template Baru:**

1. **Data Valid**: Semua contoh data dapat diimport tanpa error
2. **Realistis**: Menggunakan data wilayah yang benar-benar ada
3. **Lengkap**: Mencakup berbagai jenis komoditas dan satuan
4. **Konsisten**: Kategori komoditas sesuai dengan data master
5. **User-Friendly**: Mudah dipahami dan digunakan

## 📝 **Catatan Pengembangan:**

### Template Saat Ini:
- Menggunakan data dari Kabupaten Bojonegoro sebagai contoh
- Fokus pada Kecamatan Donorojo dan desa-desanya
- Menggunakan komoditas yang sudah terdaftar di database

### Pengembangan Selanjutnya:
- Template dapat dikembangkan untuk kabupaten lain
- Dapat ditambahkan contoh data KTH dan penyuluh
- Dapat dibuat template khusus untuk kategori HHK atau HHBK

## 🔍 **Verifikasi Template:**

Untuk memverifikasi bahwa template sudah benar:

1. **Download template** dari sistem
2. **Upload kembali** template yang sama
3. **Pastikan tidak ada error** saat import
4. **Verifikasi data** yang berhasil diimport

Template ini sudah diuji dan memastikan bahwa semua contoh data dapat diimport dengan sukses tanpa error validasi.
