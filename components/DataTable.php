<?php
// DataTable procedural functions
// Global variables for DataTable functionality
$dataTable_db = null;
$dataTable_table = '';
$dataTable_columns = [];
$dataTable_searchable = [];
$dataTable_orderable = [];

/**
 * Initialize DataTable
 * @param PDO $db
 * @param string $table
 * @param array $columns
 * @param array $searchable
 * @param array $orderable
 */
function initDataTable($db, $table, $columns, $searchable, $orderable) {
    global $dataTable_db, $dataTable_table, $dataTable_columns, $dataTable_searchable, $dataTable_orderable;
    
    $dataTable_db = $db;
    $dataTable_table = $table;
    $dataTable_columns = $columns;
    $dataTable_searchable = $searchable;
    $dataTable_orderable = $orderable;
}

/**
 * Get DataTable data with pagination and search
 * @return array
 */
function getDataTableData() {
    global $dataTable_db, $dataTable_table, $dataTable_columns, $dataTable_searchable, $dataTable_orderable;
    
    if (!$dataTable_db) {
        return ['data' => [], 'total' => 0, 'total_filtered' => 0];
    }
    
    // Get pagination parameters
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
    $offset = ($page - 1) * $limit;
    
    // Get search parameter
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Get sort parameters
    $sort_column = isset($_GET['sort']) ? $_GET['sort'] : '';
    $sort_order = isset($_GET['order']) ? strtoupper($_GET['order']) : 'ASC';
    
    if (!in_array($sort_order, ['ASC', 'DESC'])) {
        $sort_order = 'ASC';
    }
    
    // Build WHERE clause for search
    $where_clause = '';
    $search_params = [];
    
    if (!empty($search) && !empty($dataTable_searchable)) {
        $search_conditions = [];
        foreach ($dataTable_searchable as $searchable_col) {
            if (in_array($searchable_col, $dataTable_columns)) {
                $search_conditions[] = "`{$searchable_col}` LIKE :search_{$searchable_col}";
                $search_params[":search_{$searchable_col}"] = "%{$search}%";
            }
        }
        
        if (!empty($search_conditions)) {
            $where_clause = 'WHERE ' . implode(' OR ', $search_conditions);
        }
    }
    
    // Build ORDER BY clause
    $order_clause = '';
    if (!empty($sort_column) && in_array($sort_column, $dataTable_orderable)) {
        $order_clause = "ORDER BY `{$sort_column}` {$sort_order}";
    } else {
        $order_clause = "ORDER BY id DESC";
    }
    
    // Get total count
    $count_query = "SELECT COUNT(*) FROM `{$dataTable_table}` {$where_clause}";
    $count_stmt = $dataTable_db->prepare($count_query);
    $count_stmt->execute($search_params);
    $total_filtered = $count_stmt->fetchColumn();
    
    // Get total count without search
    $total_query = "SELECT COUNT(*) FROM `{$dataTable_table}`";
    $total_stmt = $dataTable_db->prepare($total_query);
    $total_stmt->execute();
    $total = $total_stmt->fetchColumn();
    
    // Get data with pagination
    $data_query = "SELECT * FROM `{$dataTable_table}` {$where_clause} {$order_clause} LIMIT {$limit} OFFSET {$offset}";
    $data_stmt = $dataTable_db->prepare($data_query);
    $data_stmt->execute($search_params);
    $data = $data_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'data' => $data,
        'total' => $total,
        'total_filtered' => $total_filtered,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => ceil($total_filtered / $limit)
    ];
}

/**
 * Render search box for DataTable
 * @return string
 */
function renderDataTableSearchBox() {
    $search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
    
    $html = '<div class="flex items-center space-x-4 mb-4">';
    $html .= '<div class="flex-1">';
    $html .= '<input type="text" name="search" value="' . $search . '" ';
    $html .= 'placeholder="Cari data..." ';
    $html .= 'class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">';
    $html .= '</div>';
    $html .= '<button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">';
    $html .= 'Cari';
    $html .= '</button>';
    $html .= '<a href="' . $_SERVER['PHP_SELF'] . '" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">';
    $html .= 'Reset';
    $html .= '</a>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Render sortable header for DataTable
 * @param string $column
 * @param string $label
 * @param string $current_page
 * @return string
 */
function renderDataTableSortableHeader($column, $label, $current_page) {
    global $dataTable_orderable;
    
    if (!in_array($column, $dataTable_orderable)) {
        return '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">' . $label . '</th>';
    }
    
    $current_sort = isset($_GET['sort']) ? $_GET['sort'] : '';
    $current_order = isset($_GET['order']) ? $_GET['order'] : 'asc';
    
    $new_order = ($current_sort === $column && $current_order === 'asc') ? 'desc' : 'asc';
    $sort_url = $current_page . '?';
    
    // Preserve existing parameters
    $params = $_GET;
    $params['sort'] = $column;
    $params['order'] = $new_order;
    $params['page'] = 1; // Reset to first page when sorting
    
    $sort_url .= http_build_query($params);
    
    $sort_icon = '';
    if ($current_sort === $column) {
        $sort_icon = $current_order === 'asc' ? ' ↑' : ' ↓';
    }
    
    $html = '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">';
    $html .= '<a href="' . $sort_url . '" class="hover:text-green-600">';
    $html .= $label . $sort_icon;
    $html .= '</a>';
    $html .= '</th>';
    
    return $html;
}

/**
 * Render pagination for DataTable
 * @param string $current_page
 * @return string
 */
function renderDataTablePagination($current_page) {
    $result = getDataTableData();
    $total_pages = $result['total_pages'];
    $current_page_num = $result['page'];
    
    if ($total_pages <= 1) {
        return '';
    }
    
    $html = '<div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">';
    $html .= '<div class="flex-1 flex justify-between sm:hidden">';
    
    // Previous button for mobile
    if ($current_page_num > 1) {
        $prev_params = $_GET;
        $prev_params['page'] = $current_page_num - 1;
        $prev_url = $current_page . '?' . http_build_query($prev_params);
        $html .= '<a href="' . $prev_url . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">';
        $html .= 'Previous';
        $html .= '</a>';
    }
    
    // Next button for mobile
    if ($current_page_num < $total_pages) {
        $next_params = $_GET;
        $next_params['page'] = $current_page_num + 1;
        $next_url = $current_page . '?' . http_build_query($next_params);
        $html .= '<a href="' . $next_url . '" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">';
        $html .= 'Next';
        $html .= '</a>';
    }
    
    $html .= '</div>';
    
    // Desktop pagination
    $html .= '<div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">';
    $html .= '<div>';
    $html .= '<p class="text-sm text-gray-700">';
    $html .= 'Showing page <span class="font-medium">' . $current_page_num . '</span> of <span class="font-medium">' . $total_pages . '</span>';
    $html .= '</p>';
    $html .= '</div>';
    
    $html .= '<div>';
    $html .= '<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">';
    
    // Previous button
    if ($current_page_num > 1) {
        $prev_params = $_GET;
        $prev_params['page'] = $current_page_num - 1;
        $prev_url = $current_page . '?' . http_build_query($prev_params);
        $html .= '<a href="' . $prev_url . '" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">';
        $html .= '←';
        $html .= '</a>';
    }
    
    // Page numbers
    $start_page = max(1, $current_page_num - 2);
    $end_page = min($total_pages, $current_page_num + 2);
    
    for ($i = $start_page; $i <= $end_page; $i++) {
        $page_params = $_GET;
        $page_params['page'] = $i;
        $page_url = $current_page . '?' . http_build_query($page_params);
        
        $active_class = ($i === $current_page_num) ? 'bg-green-50 border-green-500 text-green-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50';
        
        $html .= '<a href="' . $page_url . '" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium ' . $active_class . '">';
        $html .= $i;
        $html .= '</a>';
    }
    
    // Next button
    if ($current_page_num < $total_pages) {
        $next_params = $_GET;
        $next_params['page'] = $current_page_num + 1;
        $next_url = $current_page . '?' . http_build_query($next_params);
        $html .= '<a href="' . $next_url . '" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">';
        $html .= '→';
        $html .= '</a>';
    }
    
    $html .= '</nav>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}
?> 