# Struktur Aplikasi Baru - Sistem Informasi Pengelolaan HHK dan HHBK

## Ringkasan Perubahan

Aplikasi telah direstrukturisasi dengan layout yang lebih modern, konsisten, dan terorganisir. Berikut adalah perubahan utama yang telah dilakukan:

## 1. Struktur File Baru

### File Layout (Header & Footer)
- **`includes/header.php`** - Header konsisten dengan sidebar dan navigasi
- **`includes/footer.php`** - Footer dengan JavaScript untuk interaksi

### File Halaman Utama
- **`index.php`** - Dashboard dengan layout baru
- **`komoditas.php`** - Manajemen komoditas dengan layout baru
- **`kth.php`** - Manajemen KTH (baru)
- **`penyuluh.php`** - Manajemen penyuluh (baru)
- **`produksi.php`** - Data produksi dengan layout baru
- **`laporan.php`** - Laporan dengan layout baru
- **`users.php`** - Manajemen pengguna dengan layout baru

### File yang Dihapus
- **`wilayah.php`** - Dihapus sesuai permintaan user

## 2. Menu Baru

### Menu Utama (Sidebar)
1. **Dashboard** - Ringkasan data dan grafik
2. **Data Master**
   - KTH (Kelompok Tani Hutan)
   - Penyuluh
   - Komoditas
3. **Data Produksi** - Input dan kelola data produksi
4. **Laporan** - Laporan dan analisis data
5. **Manajemen Pengguna** (Admin only)

### Menu yang Dihapus
- **Wilayah** - Dihapus sesuai permintaan user

## 3. Fitur Layout Baru

### Header
- Logo dan nama aplikasi
- Sidebar toggle untuk mobile
- User menu dengan dropdown
- Notifikasi dan alert

### Sidebar
- Menu navigasi yang konsisten
- Icon untuk setiap menu
- Highlight menu aktif
- Responsive design

### Footer
- Copyright information
- Version number
- JavaScript untuk interaksi

### Komponen UI
- **Cards** - Untuk statistik dan ringkasan
- **Tables** - Dengan pagination dan search
- **Modals** - Untuk form input/edit
- **Charts** - Grafik interaktif
- **Alerts** - Notifikasi sukses/error

## 4. Teknologi yang Digunakan

### Frontend
- **Tailwind CSS** - Framework CSS utility-first
- **Chart.js** - Library untuk grafik
- **Select2** - Enhanced dropdown dengan search
- **JavaScript** - Interaksi dan AJAX

### Backend
- **PHP Procedural** - Sesuai permintaan user
- **PDO** - Database connection
- **Session Management** - Authentication
- **Role-based Access Control** - RBAC

## 5. Database Structure

### Tabel Utama
- **`pengguna`** - User management
- **`komoditas`** - Data komoditas HHK/HHBK
- **`kth`** - Kelompok Tani Hutan
- **`penyuluh`** - Data penyuluh
- **`produksi`** - Data produksi dengan tracking user
- **`kabupaten`** - Data wilayah (existing)
- **`kecamatan`** - Data wilayah (existing)
- **`desa`** - Data wilayah (existing)
- **`satuan`** - Unit of measurement

## 6. Fitur Baru

### Dashboard
- Statistik real-time
- Grafik produksi per bulan
- Grafik produksi per kategori
- Data produksi terbaru
- Quick actions

### Data Master
- **KTH Management** - CRUD untuk Kelompok Tani Hutan
- **Penyuluh Management** - CRUD untuk penyuluh
- **Komoditas Management** - CRUD untuk komoditas dengan kategori

### Laporan
- Filter berdasarkan periode, wilayah, komoditas
- Export ke Excel
- Grafik analisis
- Summary cards

### User Management
- Role-based access control
- Password management
- User activity tracking

## 7. Responsive Design

### Mobile First
- Sidebar collapse pada mobile
- Responsive tables
- Touch-friendly buttons
- Optimized forms

### Desktop
- Full sidebar visible
- Multi-column layouts
- Hover effects
- Enhanced interactions

## 8. Security Features

### Authentication
- Session-based login
- Password hashing
- Role-based access
- Session timeout

### Data Protection
- SQL injection prevention
- XSS protection
- CSRF protection
- Input validation

## 9. Performance Optimizations

### Database
- Indexed queries
- Efficient joins
- Pagination
- Search optimization

### Frontend
- Minified CSS/JS
- Optimized images
- Lazy loading
- Caching strategies

## 10. File Structure

```
hhbk/
├── includes/
│   ├── header.php
│   └── footer.php
├── config/
│   └── database.php
├── components/
│   └── DataTable.php
├── api/
│   ├── search_kabupaten.php
│   ├── search_kecamatan.php
│   ├── search_desa.php
│   ├── get_kecamatan.php
│   ├── get_desa.php
│   └── get_kth.php
├── index.php
├── login.php
├── logout.php
├── komoditas.php
├── kth.php
├── penyuluh.php
├── produksi.php
├── laporan.php
├── users.php
└── hhbk.sql
```

## 11. Login Credentials

### Default Users
- **Username:** admin
- **Password:** admin123
- **Role:** admin-prov

- **Username:** test
- **Password:** admin123
- **Role:** viewer

## 12. Cara Penggunaan

### Login
1. Akses `login.php`
2. Masukkan username dan password
3. Sistem akan redirect ke dashboard

### Navigasi
1. Gunakan sidebar untuk navigasi
2. Menu aktif akan di-highlight
3. Responsive design untuk mobile

### Data Entry
1. Pilih menu yang sesuai
2. Klik "Tambah" untuk input data baru
3. Gunakan modal form untuk input
4. Data akan tersimpan dengan tracking user

### Laporan
1. Akses menu "Laporan"
2. Gunakan filter untuk data spesifik
3. Export data jika diperlukan
4. Analisis melalui grafik

## 13. Maintenance

### Backup Database
- Backup file `hhbk.sql` secara berkala
- Backup folder aplikasi

### Updates
- Monitor error logs
- Update dependencies
- Security patches

### Monitoring
- Check user activity
- Monitor database performance
- Review access logs

---

**Catatan:** Aplikasi ini menggunakan paradigma **procedural programming** sesuai permintaan user dan telah dioptimasi untuk kemudahan maintenance dan pengembangan.
