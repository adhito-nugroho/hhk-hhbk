# Validasi Data Import Excel

## Overview
Sistem import Excel memiliki validasi yang ketat untuk memastikan data yang diimport sesuai dengan data master yang ada di database.

## 🔍 Jenis Validasi

### 1. Validasi Field Wajib
- **Periode**: Tidak boleh kosong, format YYYY-MM-DD
- **Kabupaten**: Tidak boleh kosong, harus ada di database
- **Kecamatan**: Tidak boleh kosong, harus ada di kabupaten yang dipilih
- **Desa**: Tidak boleh kosong, harus ada di kecamatan yang dipilih
- **Komoditas**: Tidak boleh kosong, harus ada di database
- **Kategori**: Tidak boleh kosong, harus HHK atau HHBK
- **Quantity**: Tidak boleh kosong, harus angka positif
- **Satuan**: Tidak boleh kosong, harus ada di database

### 2. Validasi Field Opsional
- **KTH**: Jika diisi, harus ada di desa yang dipilih
- **Penyuluh**: Jika diisi, harus terdaftar di database

### 3. Validasi Hierarki Wilayah
- Kecamatan harus berada di bawah kabupaten yang dipilih
- Desa harus berada di bawah kecamatan yang dipilih
- KTH harus berada di bawah desa yang dipilih

### 4. Validasi Konsistensi Data
- Kategori komoditas harus sesuai dengan data master
- Tidak boleh ada data duplikat (periode + wilayah + komoditas yang sama)

## ⚠️ Pesan Error yang Muncul

### Error Kabupaten
```
Kabupaten 'Nama Kabupaten' tidak ditemukan dalam database
```

### Error Kecamatan
```
Kecamatan 'Nama Kecamatan' tidak ditemukan dalam kabupaten 'Nama Kabupaten'
```

### Error Desa
```
Desa 'Nama Desa' tidak ditemukan dalam kecamatan 'Nama Kecamatan'
```

### Error KTH
```
KTH 'Nama KTH' tidak ditemukan dalam desa 'Nama Desa'
```

### Error Penyuluh
```
Penyuluh 'Nama Penyuluh' tidak ditemukan dalam database
```

### Error Komoditas
```
Komoditas 'Nama Komoditas' tidak ditemukan dalam database
```

### Error Kategori Komoditas
```
Kategori komoditas 'Nama Komoditas' tidak sesuai (harus HHK, bukan HHBK)
```

### Error Satuan
```
Satuan 'Nama Satuan' tidak ditemukan dalam database
```

### Error Format Tanggal
```
Format periode harus YYYY-MM-DD
```

### Error Quantity
```
Quantity harus lebih dari 0
```

### Error Kategori
```
Kategori harus HHK atau HHBK
```

### Error Data Duplikat
```
Data duplikat ditemukan
```

## 🛠️ Cara Mengatasi Error

### 1. Periksa Data Master
Sebelum import, pastikan semua data master sudah lengkap:
- Data kabupaten, kecamatan, desa
- Data KTH (jika digunakan)
- Data penyuluh (jika digunakan)
- Data komoditas dengan kategori yang benar
- Data satuan

### 2. Periksa Format Data
- Pastikan format tanggal YYYY-MM-DD
- Pastikan quantity berupa angka positif
- Pastikan kategori HHK atau HHBK

### 3. Periksa Hierarki Wilayah
- Kecamatan harus berada di bawah kabupaten yang benar
- Desa harus berada di bawah kecamatan yang benar
- KTH harus berada di bawah desa yang benar

### 4. Periksa Konsistensi
- Kategori komoditas harus sesuai dengan data master
- Pastikan tidak ada data duplikat

## 📊 Contoh Data yang Valid

| periode | kabupaten | kecamatan | desa | kth | penyuluh | komoditas | kategori | qty | satuan |
|---------|-----------|-----------|------|-----|----------|-----------|----------|-----|--------|
| 2024-01-15 | Kabupaten Bojonegoro | Donorojo | Belah | | | Bambu | HHBK | 100.500 | Kilogram |

## ❌ Contoh Data yang Tidak Valid

### 1. Desa Tidak Ada di Kecamatan
```
periode: 2024-01-15
kabupaten: Kabupaten Bojonegoro
kecamatan: Donorojo
desa: Desa X (tidak ada di Kecamatan Donorojo)
```

### 2. Penyuluh Tidak Terdaftar
```
penyuluh: John Doe (tidak terdaftar di database)
```

### 3. Kategori Komoditas Tidak Sesuai
```
komoditas: Bambu
kategori: HHK (padahal Bambu adalah HHBK)
```

## 🔄 Proses Import

1. **Validasi Header**: Memastikan semua kolom yang diperlukan ada
2. **Validasi Format**: Memastikan format data sesuai
3. **Validasi Data Master**: Memastikan semua referensi data ada
4. **Validasi Konsistensi**: Memastikan data konsisten
5. **Import Data**: Jika semua validasi berhasil, data diimport

## 📈 Hasil Import

Sistem akan menampilkan:
- Jumlah data berhasil diimport
- Jumlah data gagal diimport
- Detail error untuk data yang gagal (maksimal 5 error pertama)

## 💡 Tips

1. **Gunakan Template**: Download template Excel untuk format yang benar
2. **Periksa Data Master**: Pastikan semua data master sudah lengkap
3. **Test dengan Data Kecil**: Import data kecil dulu untuk memastikan format benar
4. **Periksa Error**: Baca pesan error dengan teliti untuk mengetahui masalah
5. **Backup Data**: Backup data sebelum import data dalam jumlah besar
