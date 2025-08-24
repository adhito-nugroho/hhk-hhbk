# DataTable Features pada wilayah.php - Sistem Informasi Pengelolaan HHK dan HHBK

## Overview
File `wilayah.php` telah ditingkatkan dengan fitur DataTable yang lengkap untuk semua tab wilayah (Kabupaten, Kecamatan, Desa, dan KTH). Setiap tab sekarang memiliki fitur searching, pagination, dan sorting yang terintegrasi.

## Fitur yang Diterapkan

### 1. **Searching (Pencarian)**
- **Real-time Search**: Pencarian otomatis dengan delay 500ms
- **Multi-column Search**: Mencari di kolom yang dapat dicari
- **Case-insensitive**: Pencarian tidak membedakan huruf besar/kecil
- **Auto-submit**: Form otomatis tersubmit setelah user berhenti mengetik

### 2. **Pagination (Paginasi)**
- **Configurable Page Size**: Opsi 10, 25, 50, 100 data per halaman
- **Navigation Controls**: Tombol Previous/Next dan nomor halaman
- **Page Info**: Informasi jumlah data yang ditampilkan
- **Smart Pagination**: Menampilkan maksimal 5 nomor halaman dengan ellipsis

### 3. **Sorting (Pengurutan)**
- **Sortable Headers**: Header kolom yang dapat diklik untuk sorting
- **Visual Indicators**: Icon panah menunjukkan arah sorting
- **Toggle Direction**: ASC/DESC dapat diubah dengan mengklik header
- **Persistent State**: Status sorting disimpan dalam URL

## Implementasi per Tab

### **Tab Kabupaten**
- **Searchable Columns**: `nama`
- **Orderable Columns**: `nama`
- **Data Source**: Tabel `kabupaten`
- **Features**: Search, Sort, Pagination

### **Tab Kecamatan**
- **Searchable Columns**: `nama`
- **Orderable Columns**: `nama`
- **Data Source**: Tabel `kecamatan` dengan JOIN ke `kabupaten`
- **Features**: Search, Sort, Pagination
- **Special**: Menampilkan nama kabupaten untuk setiap kecamatan

### **Tab Desa**
- **Searchable Columns**: `nama`
- **Orderable Columns**: `nama`
- **Data Source**: Tabel `desa` dengan JOIN ke `kecamatan` dan `kabupaten`
- **Features**: Search, Sort, Pagination
- **Special**: Menampilkan hierarki wilayah lengkap (Kabupaten > Kecamatan > Desa)

### **Tab KTH**
- **Searchable Columns**: `nama`, `kode`
- **Orderable Columns**: `nama`, `kode`
- **Data Source**: Tabel `kth` dengan JOIN ke `desa`, `kecamatan`, dan `kabupaten`
- **Features**: Search, Sort, Pagination
- **Special**: Menampilkan lokasi lengkap KTH dan kode KTH

## Struktur DataTable

### **Inisialisasi DataTable**
```php
// DataTable untuk setiap tab
$dataTableKabupaten = new DataTable($db, 'kabupaten', ['id', 'nama'], ['nama'], ['nama']);
$dataTableKecamatan = new DataTable($db, 'kecamatan', ['id', 'kabupaten_id', 'nama'], ['nama'], ['nama']);
$dataTableDesa = new DataTable($db, 'desa', ['id', 'kecamatan_id', 'nama'], ['nama'], ['nama']);
$dataTableKTH = new DataTable($db, 'kth', ['id', 'desa_id', 'nama', 'kode'], ['nama', 'kode'], ['nama', 'kode']);
```

### **Rendering UI Components**
```php
// Search Box
<?php echo $dataTableKabupaten->renderSearchBox(); ?>

// Sortable Headers
<?php echo $dataTableKabupaten->renderSortableHeader('nama', 'Nama Kabupaten', 'wilayah.php'); ?>

// Pagination
<?php echo $dataTableKabupaten->renderPagination('wilayah.php'); ?>
```

## Fitur Tambahan

### **JavaScript Enhancement**
- **Tab Switching**: Fungsi JavaScript untuk beralih antar tab
- **Modal Management**: Fungsi untuk membuka/menutup modal form
- **Delete Confirmation**: Konfirmasi sebelum menghapus data
- **DataTable Integration**: Script `datatable.js` untuk fitur tambahan

### **Responsive Design**
- **Mobile Friendly**: Layout responsif dengan Tailwind CSS
- **Table Overflow**: Horizontal scroll untuk tabel yang lebar
- **Touch Friendly**: Tombol dan input yang mudah digunakan di perangkat mobile

## Cara Penggunaan

### **1. Pencarian Data**
1. Masukkan kata kunci di kotak pencarian
2. Sistem akan otomatis mencari setelah 500ms
3. Hasil pencarian ditampilkan dengan pagination

### **2. Pengurutan Data**
1. Klik header kolom untuk mengurutkan
2. Klik lagi untuk mengubah arah (ASC/DESC)
3. Status sorting ditampilkan dengan icon panah

### **3. Navigasi Halaman**
1. Gunakan dropdown untuk memilih jumlah data per halaman
2. Gunakan tombol Previous/Next untuk navigasi
3. Klik nomor halaman untuk langsung ke halaman tertentu

### **4. Beralih Antar Tab**
1. Klik tab yang diinginkan (Kabupaten, Kecamatan, Desa, KTH)
2. Setiap tab memiliki fitur DataTable yang independen
3. State pencarian dan pagination disimpan per tab

## Keunggulan Implementasi

### **1. Performance**
- **Server-side Processing**: Search, sort, dan pagination diproses di server
- **Efficient Queries**: Menggunakan prepared statements dan JOIN yang optimal
- **Lazy Loading**: Data dimuat sesuai kebutuhan (pagination)

### **2. User Experience**
- **Intuitive Interface**: UI yang mudah dipahami dan digunakan
- **Real-time Feedback**: Pencarian dan sorting yang responsif
- **Consistent Design**: Konsistensi visual dengan sistem Sistem Informasi Pengelolaan HHK dan HHBK

### **3. Maintainability**
- **Reusable Components**: Class DataTable dapat digunakan di file lain
- **Clean Code**: Struktur kode yang rapi dan mudah dipahami
- **Modular Design**: Fitur yang terpisah dan mudah dimodifikasi

## Troubleshooting

### **Masalah Umum**

#### **1. Data Tidak Muncul**
- Periksa koneksi database
- Pastikan tabel memiliki data
- Periksa error log PHP

#### **2. Search Tidak Berfungsi**
- Pastikan kolom searchable sudah didefinisikan
- Periksa JavaScript console untuk error
- Pastikan file `datatable.js` ter-load

#### **3. Pagination Error**
- Periksa parameter URL (page, per_page)
- Pastikan method `getData()` mengembalikan data yang benar
- Periksa error log untuk detail masalah

### **Debug Mode**
Untuk debugging, tambahkan kode berikut:
```php
// Debug DataTable
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug query
echo "<!-- Debug: " . $query . " -->";
```

## Kesimpulan

Implementasi DataTable pada `wilayah.php` telah berhasil memberikan pengalaman pengguna yang lebih baik dalam mengelola data wilayah. Fitur searching, pagination, dan sorting yang terintegrasi membuat navigasi data menjadi lebih efisien dan user-friendly.

Semua tab wilayah sekarang memiliki fitur DataTable yang konsisten, dengan kemampuan untuk mencari, mengurutkan, dan menavigasi data dengan mudah. Implementasi ini juga mempertahankan fungsionalitas CRUD yang sudah ada sambil menambahkan fitur DataTable yang powerful. 