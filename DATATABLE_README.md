# DataTable Features - Sistem Informasi Pengelolaan HHK dan HHBK

Sistem Informasi Pengelolaan HHK dan HHBK telah ditingkatkan dengan fitur DataTable yang lengkap, menyediakan pengalaman pengguna yang lebih baik dalam mengelola dan menampilkan data.

## Fitur Utama

### 1. Searching (Pencarian)
- **Real-time Search**: Pencarian otomatis dengan delay 500ms
- **Multi-column Search**: Mencari di beberapa kolom sekaligus
- **Case-insensitive**: Pencarian tidak membedakan huruf besar/kecil
- **Auto-submit**: Form otomatis tersubmit setelah user berhenti mengetik

### 2. Pagination (Paginasi)
- **Flexible Page Size**: Opsi 10, 25, 50, 100 data per halaman
- **Smart Navigation**: Tombol Previous/Next dengan state disabled
- **Page Numbers**: Navigasi langsung ke halaman tertentu
- **Info Display**: Menampilkan informasi "Menampilkan X - Y dari Z data"

### 3. Sorting (Pengurutan)
- **Column Sorting**: Klik header kolom untuk mengurutkan
- **Visual Indicators**: Icon panah menunjukkan arah pengurutan
- **Toggle Direction**: ASC/DESC bergantian setiap klik
- **Hover Effects**: Efek visual saat hover di header yang bisa diurutkan

### 4. Advanced Features
- **Bulk Actions**: Pilih multiple rows untuk operasi massal
- **Export Data**: Export ke CSV dan Excel
- **Row Selection**: Checkbox untuk setiap baris
- **Keyboard Shortcuts**: Ctrl+F untuk search, Ctrl+A untuk select all

## Implementasi

### File Structure
```
components/
├── DataTable.php          # Core DataTable class
assets/
├── js/
│   └── datatable.js      # Enhanced JavaScript functionality
pages/
├── produksi.php          # Updated with DataTable
├── komoditas.php         # Updated with DataTable
└── demo-datatable.php    # Demo page showcasing all features
```

### DataTable Class
```php
$dataTable = new DataTable(
    $db,                    // Database connection
    'table_name',          // Table name
    ['col1', 'col2'],     // All columns
    ['col1', 'col2'],     // Searchable columns
    ['col1', 'col2']      // Sortable columns
);
```

### Basic Usage
```php
// Get data with pagination and search
$result = $dataTable->getData();
$data = $result['data'];

// Render search box
echo $dataTable->renderSearchBox();

// Render sortable headers
echo $dataTable->renderSortableHeader('column_name', 'Display Name', 'page.php');

// Render pagination
echo $dataTable->renderPagination('page.php');
```

## Fitur JavaScript

### Auto-search
```javascript
// Auto-submit form after 500ms of no typing
searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        this.form.submit();
    }, 500);
});
```

### Export Functionality
```javascript
// Export to CSV
exportTableData('csv');

// Export to Excel
exportTableData('excel');
```

### Bulk Actions
```javascript
// Select all rows
selectAllRows();

// Bulk delete
bulkDelete();

// Update bulk actions visibility
updateBulkActions();
```

## Konfigurasi

### Searchable Columns
```php
$searchableColumns = ['nama', 'email', 'alamat'];
// Kolom-kolom yang akan dicari saat user mengetik di search box
```

### Sortable Columns
```php
$orderableColumns = ['nama', 'created_at', 'status'];
// Kolom-kolom yang bisa diurutkan dengan mengklik header
```

### Page Size Options
```php
$allowedPerPage = [10, 25, 50, 100];
// Opsi jumlah data yang ditampilkan per halaman
```

## Styling dan UI

### Tailwind CSS Classes
- **Search Box**: `bg-white`, `border-gray-300`, `focus:ring-green-500`
- **Table Headers**: `