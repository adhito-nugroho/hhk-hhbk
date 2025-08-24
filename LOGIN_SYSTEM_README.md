# Sistem Login - Sistem Informasi Pengelolaan HHK dan HHBK

## Overview
Sistem ini telah dilengkapi dengan sistem autentikasi yang lengkap untuk mengamankan akses ke aplikasi dan melacak siapa yang melakukan entri data.

## Fitur Sistem Login

### 1. Halaman Login (`login.php`)
- Form login yang modern dan responsif
- Validasi input username dan password
- Pesan error yang informatif
- Redirect otomatis jika sudah login

### 2. Sistem Autentikasi (`auth_check.php`)
- Pengecekan session user di setiap halaman
- Fungsi untuk mengecek role user
- Fungsi untuk mendapatkan informasi user yang sedang login

### 3. Manajemen User (`users.php`)
- Hanya dapat diakses oleh admin (admin-prov, admin-kab)
- CRUD operasi untuk user
- Manajemen role dan password
- Validasi untuk mencegah user menghapus akun sendiri

### 4. Logout (`logout.php`)
- Mengakhiri session user
- Redirect ke halaman login

## Struktur Database

### Tabel `pengguna`
```sql
CREATE TABLE `pengguna` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('admin-prov','admin-kab','penyuluh','viewer') NOT NULL DEFAULT 'viewer',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
);
```

### Tabel `produksi` (sudah ada)
- Field `created_by` terhubung ke tabel `pengguna`
- Otomatis terisi dengan ID user yang sedang login

## Role dan Hak Akses

### 1. Admin Provinsi (`admin-prov`)
- Akses penuh ke semua fitur
- Manajemen user
- Manajemen data produksi

### 2. Admin Kabupaten (`admin-kab`)
- Akses ke data wilayah tertentu
- Manajemen user
- Manajemen data produksi

### 3. Penyuluh (`penyuluh`)
- Input data produksi
- Melihat data produksi
- Tidak dapat mengelola user

### 4. Viewer (`viewer`)
- Hanya dapat melihat data
- Tidak dapat mengubah atau menambah data

## Cara Penggunaan

### 1. Setup Awal
1. Import database `hhbk.sql`
2. Jalankan script `create_default_user.sql` untuk membuat user default
3. Login dengan username: `admin`, password: `admin123`

### 2. Login
1. Buka `login.php`
2. Masukkan username dan password
3. Sistem akan redirect ke dashboard jika berhasil

### 3. Manajemen User
1. Login sebagai admin
2. Akses menu "Users"
3. Tambah, edit, atau hapus user sesuai kebutuhan

### 4. Input Data Produksi
1. Login dengan user yang memiliki role penyuluh atau admin
2. Akses menu "Produksi"
3. Data akan otomatis tercatat dengan `created_by` sesuai user yang login

## Keamanan

### 1. Password Hashing
- Menggunakan `password_hash()` dengan `PASSWORD_DEFAULT`
- Password tidak tersimpan dalam bentuk plain text

### 2. Session Management
- Session dimulai di setiap halaman
- Session dihancurkan saat logout
- Redirect otomatis jika tidak login

### 3. Role-based Access Control
- Setiap halaman mengecek role user
- Menu dan fitur dibatasi berdasarkan role

### 4. SQL Injection Prevention
- Menggunakan prepared statements
- Input validation dan sanitization

## File yang Telah Dibuat/Dimodifikasi

### File Baru
- `login.php` - Halaman login
- `logout.php` - Fungsi logout
- `auth_check.php` - Pengecekan autentikasi
- `users.php` - Manajemen user
- `create_default_user.sql` - Script untuk user default

### File yang Dimodifikasi
- `index.php` - Ditambahkan autentikasi dan menu user
- `produksi.php` - Ditambahkan autentikasi dan menu user

## Troubleshooting

### 1. User tidak bisa login
- Pastikan tabel `pengguna` sudah dibuat
- Pastikan script `create_default_user.sql` sudah dijalankan
- Cek konfigurasi database

### 2. Session tidak tersimpan
- Pastikan `session_start()` ada di awal setiap file
- Cek konfigurasi PHP session

### 3. Menu Users tidak muncul
- Pastikan user memiliki role admin-prov atau admin-kab
- Cek file `auth_check.php` sudah di-include

### 4. Field created_by kosong
- Pastikan user sudah login
- Cek session `user_id` tersimpan dengan benar

## Pengembangan Selanjutnya

### 1. Fitur Tambahan
- Reset password
- Remember me
- Two-factor authentication
- Audit log untuk aktivitas user

### 2. Peningkatan Keamanan
- Rate limiting untuk login
- Captcha untuk mencegah brute force
- Password complexity requirements
- Session timeout

### 3. User Experience
- Dashboard yang lebih informatif
- Notifikasi real-time
- Mobile app support

## Kontak
Untuk pertanyaan atau bantuan teknis, hubungi administrator sistem.
