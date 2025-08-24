# Sistem Informasi Pengelolaan HHK dan HHBK

Sistem informasi untuk mengelola data produksi Hasil Hutan Kayu (HHK) dan Hasil Hutan Bukan Kayu (HHBK).

## Fitur Utama

### 1. Manajemen Wilayah
- Data Kabupaten, Kecamatan, Desa
- Data Kelompok Tani Hutan (KTH)
- Data Penyuluh

### 2. Manajemen Komoditas
- Data jenis komoditas dengan kategori HHK (Hasil Hutan Kayu) dan HHBK (Hasil Hutan Bukan Kayu)
- Pengelompokan berdasarkan kategori

### 3. Manajemen Produksi
- Input data produksi manual melalui form
- Import data dari file Excel/CSV
- Validasi data otomatis
- Pencegahan data duplikat
- Tracking sumber data (form input atau excel import)

### 4. Laporan
- Laporan produksi berdasarkan periode
- Laporan berdasarkan wilayah
- Grafik dan statistik produksi

## Struktur Database

### Tabel Utama
- `kabupaten` - Data kabupaten
- `kecamatan` - Data kecamatan
- `desa` - Data desa
- `kth` - Data Kelompok Tani Hutan
- `penyuluh` - Data penyuluh
- `komoditas` - Data jenis komoditas dengan kategori HHK (Hasil Hutan Kayu) dan HHBK (Hasil Hutan Bukan Kayu)
- `satuan` - Data satuan ukuran
- `produksi` - Data produksi HHBK
- `pengguna` - Data pengguna sistem

## Struktur File

```
├── index.php                    # Dashboard utama
├── produksi.php                # Manajemen produksi (updated with DataTable + Import)
├── import_produksi.php         # Import data produksi dari Excel
├── download_template.php        # Download template Excel
├── template_produksi_hhbk.csv  # Template CSV untuk import
├── komoditas.php               # Manajemen komoditas (updated with DataTable)
├── wilayah.php                 # Manajemen wilayah (updated with DataTable)
├── laporan.php                 # Laporan produksi
├── demo-datatable.php          # Demo fitur DataTable
├── index_import.php            # Halaman utama fitur import
├── config/
│   └── database.php            # Konfigurasi database
├── components/
│   └── DataTable.php           # Class DataTable untuk server-side processing
├── assets/
│   └── js/
│       └── datatable.js        # JavaScript untuk DataTable
├── api/
│   ├── get_kabupaten.php       # API untuk data kabupaten
│   ├── get_kecamatan.php       # API untuk data kecamatan
│   ├── get_desa.php            # API untuk data desa
│   └── get_kth.php             # API untuk data KTH
├── hhbk.sql                    # Database schema
├── update_komoditas_kategori.sql # Script update kategori komoditas
├── README.md                   # Dokumentasi utama
├── DATATABLE_README.md         # Dokumentasi fitur DataTable
├── WILAYAH_DATATABLE_README.md # Dokumentasi DataTable wilayah
└── IMPORT_PRODUKSI_README.md   # Dokumentasi fitur import dan validasi
```

## Teknologi

- **Backend**: PHP 7.4+
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, CSS3 (Tailwind CSS), JavaScript
- **DataTable**: Server-side processing dengan AJAX
- **Import**: Support untuk file CSV/Excel

## Instalasi

1. Clone repository ini ke web server
2. Import database dari file `hhbk.sql`
3. Konfigurasi database di `config/database.php`
4. Akses aplikasi melalui browser

## Penggunaan

### Input Manual
1. Buka menu "Manajemen Produksi"
2. Klik "Tambah Produksi"
3. Isi form dengan data yang diperlukan
4. Klik "Simpan"

### Import Excel
1. Buka menu "Import Excel"
2. Download template yang tersedia
3. Isi data sesuai format template
4. Upload file dan klik "Import"
5. Sistem akan memvalidasi dan memproses data

## Validasi Data

- **Duplikasi**: Sistem mencegah input data yang sama untuk periode, wilayah, dan komoditas yang identik
- **Referensial**: Data wilayah dan komoditas harus sesuai dengan master data
- **Format**: Tanggal, angka, dan kategori harus sesuai format yang ditentukan
- **Kategori**: Komoditas harus sesuai dengan kategori HHK atau HHBK yang didefinisikan

## Kontribusi

Silakan berkontribusi dengan membuat pull request atau melaporkan bug melalui issues.

## Lisensi

Proyek ini dikembangkan untuk keperluan internal pengelolaan data HHBK. 