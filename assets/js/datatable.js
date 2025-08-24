// DataTable Enhancement Scripts
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    initializeSearch();
    
    // Initialize sort indicators
    initializeSortIndicators();
    
    // Initialize pagination enhancement
    initializePagination();
});

function initializeSearch() {
    const searchInput = document.getElementById('search');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Auto-submit form after 500ms of no typing
                this.form.submit();
            }, 500);
        });
        
        // Add search icon click handler
        const searchButton = searchInput.parentElement.nextElementSibling.querySelector('button');
        if (searchButton) {
            searchButton.addEventListener('click', function() {
                searchInput.form.submit();
            });
        }
    }
}

function initializeSortIndicators() {
    // Add hover effects to sortable headers
    const sortableHeaders = document.querySelectorAll('th[onclick]');
    sortableHeaders.forEach(header => {
        header.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f3f4f6';
        });
        
        header.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
}

function initializePagination() {
    // Add loading state to pagination links
    const paginationLinks = document.querySelectorAll('.pagination a');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Add loading indicator
            const originalText = this.textContent;
            this.textContent = 'Loading...';
            this.style.pointerEvents = 'none';
            
            // Reset after a short delay (in case of error)
            setTimeout(() => {
                this.textContent = originalText;
                this.style.pointerEvents = '';
            }, 3000);
        });
    });
}

// Enhanced search with debouncing
function debounceSearch(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Export data functionality
function exportTableData(format = 'csv') {
    const table = document.querySelector('table');
    if (!table) return;
    
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    let data = [];
    
    // Get headers
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
    
    // Get data rows
    rows.forEach(row => {
        const rowData = [];
        const cells = row.querySelectorAll('td');
        cells.forEach((cell, index) => {
            // Skip action column
            if (index < cells.length - 1) {
                rowData.push(cell.textContent.trim());
            }
        });
        data.push(rowData);
    });
    
    if (format === 'csv') {
        exportToCSV(headers, data);
    } else if (format === 'excel') {
        exportToExcel(headers, data);
    }
}

function exportToCSV(headers, data) {
    let csvContent = headers.join(',') + '\n';
    data.forEach(row => {
        csvContent += row.map(cell => `"${cell}"`).join(',') + '\n';
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'data_export.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToExcel(headers, data) {
    // Simple Excel-like export using HTML table
    let htmlContent = '<table border="1">';
    
    // Add headers
    htmlContent += '<tr>';
    headers.forEach(header => {
        htmlContent += `<th>${header}</th>`;
    });
    htmlContent += '</tr>';
    
    // Add data
    data.forEach(row => {
        htmlContent += '<tr>';
        row.forEach(cell => {
            htmlContent += `<td>${cell}</td>`;
        });
        htmlContent += '</tr>';
    });
    
    htmlContent += '</table>';
    
    const blob = new Blob([htmlContent], { type: 'application/vnd.ms-excel' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'data_export.xls');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Bulk actions functionality
function selectAllRows() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    const selectAllCheckbox = document.getElementById('select-all');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const selectedRows = document.querySelectorAll('tbody input[type="checkbox"]:checked');
    const bulkActionsContainer = document.getElementById('bulk-actions');
    
    if (bulkActionsContainer) {
        if (selectedRows.length > 0) {
            bulkActionsContainer.classList.remove('hidden');
            document.getElementById('selected-count').textContent = selectedRows.length;
        } else {
            bulkActionsContainer.classList.add('hidden');
        }
    }
}

function bulkDelete() {
    const selectedRows = document.querySelectorAll('tbody input[type="checkbox"]:checked');
    if (selectedRows.length === 0) return;
    
    if (confirm(`Apakah Anda yakin ingin menghapus ${selectedRows.length} data yang dipilih?`)) {
        const ids = Array.from(selectedRows).map(checkbox => checkbox.value);
        
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="bulk_delete">
            <input type="hidden" name="ids" value="${ids.join(',')}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Column visibility toggle
function toggleColumnVisibility(columnIndex) {
    const table = document.querySelector('table');
    const cells = table.querySelectorAll(`td:nth-child(${columnIndex + 1}), th:nth-child(${columnIndex + 1})`);
    
    cells.forEach(cell => {
        if (cell.style.display === 'none') {
            cell.style.display = '';
        } else {
            cell.style.display = 'none';
        }
    });
}

// Row highlighting
function highlightRow(row) {
    row.style.backgroundColor = '#fef3c7';
    setTimeout(() => {
        row.style.backgroundColor = '';
    }, 2000);
}

// Keyboard navigation
function initializeKeyboardNavigation() {
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.getElementById('search');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        
        if (e.ctrlKey && e.key === 'a') {
            e.preventDefault();
            const selectAllCheckbox = document.getElementById('select-all');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = !selectAllCheckbox.checked;
                selectAllRows();
            }
        }
    });
}

// Initialize keyboard navigation
initializeKeyboardNavigation(); 