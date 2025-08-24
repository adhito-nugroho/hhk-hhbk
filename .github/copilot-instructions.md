# Copilot Instructions for HHBK Codebase

## Big Picture Architecture
- **Monolithic PHP Web App**: All logic is in PHP files, organized by feature (produksi, komoditas, wilayah, laporan, import, etc).
- **Database**: MySQL, schema in `hhbk.sql`. Key tables: `produksi`, `komoditas`, `kth`, `kabupaten`, `kecamatan`, `desa`, `penyuluh`, `pengguna`.
- **Frontend**: HTML5, Tailwind CSS, JavaScript (DataTable, Chart.js for grafik), AJAX for dynamic data.
- **API Layer**: Simple PHP endpoints in `api/` for wilayah and KTH data, consumed via AJAX.
- **Data Import**: Excel/CSV import via `import_produksi.php`, template in `template_produksi_hhbk.csv`.

## Developer Workflows
- **No build step**: Directly edit PHP/JS/CSS files, then refresh browser.
- **Database setup**: Import `hhbk.sql` into MySQL, configure connection in `config/database.php`.
- **Testing**: Manual via browser; no automated test suite present.
- **Debugging**: Use `echo`, `var_dump`, or browser dev tools for JS. No Xdebug or advanced tooling by default.

## Project-Specific Conventions
- **DataTable**: Use `components/DataTable.php` for server-side processing. JS config in `assets/js/datatable.js`.
- **Import Validation**: All import logic validates for duplicates, referential integrity, and format before insert.
- **Grafik/Chart**: Chart.js used for donut/bar charts in laporan. Always check for division by zero when calculating percentages to avoid NaN% (see `laporan.php`).
- **File Structure**: Each feature has a dedicated PHP file. Shared config in `config/`, shared components in `components/`.
- **Documentation**: Feature-specific docs in `*_README.md` files (see `DATATABLE_README.md`, `IMPORT_PRODUKSI_README.md`).

## Integration Points
- **AJAX**: Frontend JS fetches data from `api/` endpoints for wilayah/KTH/komoditas.
- **Excel/CSV Import**: Data validated and inserted via `import_produksi.php`.
- **Charts**: Data for Chart.js prepared in PHP, rendered in JS in `laporan.php`.

## Examples & Patterns
- **Server-side DataTable**: See `produksi.php`, `komoditas.php`, `wilayah.php` for usage.
- **Import Workflow**: See `import_produksi.php` and `IMPORT_PRODUKSI_README.md` for validation logic.
- **Grafik Donut**: Always check total > 0 before calculating percentage (see recent fix for NaN%).

## Key Files
- `index.php`: Dashboard
- `produksi.php`: Produksi management
- `import_produksi.php`: Import logic
- `laporan.php`: Reports & charts
- `config/database.php`: DB config
- `components/DataTable.php`: DataTable server-side logic
- `assets/js/datatable.js`: DataTable JS
- `api/`: API endpoints

---

For unclear or missing conventions, check the relevant `*_README.md` or ask the maintainer.
