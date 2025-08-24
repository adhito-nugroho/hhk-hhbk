<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/components/DataTable.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Sistem Informasi Pengelolaan HHK dan HHBK'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Select2 CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        /* Custom scrollbar for sidebar */
        .sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: #4B5563 #1F2937;
        }
        .sidebar-scroll::-webkit-scrollbar {
            width: 8px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: #1F2937;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: #4B5563;
            border-radius: 10px;
            border: 2px solid #1F2937;
        }
        
        /* Adjust Select2 for Tailwind */
        .select2-container--default .select2-selection--single {
            @apply border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500;
            height: auto !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            @apply py-0.5;
            line-height: normal;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
        }
        .select2-dropdown {
            @apply border border-gray-300 rounded-md shadow-lg;
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            @apply bg-green-600 text-white;
        }
        .select2-container--default .select2-results__option--selectable {
            @apply px-3 py-2;
        }
        .select2-search--dropdown .select2-search__field {
            @apply border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500;
        }
        
        /* Sidebar transition */
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        
        /* Mobile sidebar overlay */
        .sidebar-overlay {
            transition: opacity 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 bg-gray-800 text-white w-64 px-4 py-6 space-y-6 z-50 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-200 ease-in-out sidebar-scroll">
            <div class="flex items-center justify-between px-2">
                <h2 class="text-2xl font-bold text-green-400">HHK & HHBK</h2>
                <button id="sidebar-close" class="md:hidden text-gray-400 hover:text-white focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <nav class="space-y-2">
                <a href="index.php" class="flex items-center px-4 py-2 rounded-lg <?php echo ($current_page == 'index') ? 'bg-green-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?>">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                
                <div class="relative">
                    <button type="button" class="flex items-center justify-between w-full px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white focus:outline-none" onclick="toggleDropdown('masterDataDropdown')">
                        <span class="flex items-center">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10l2 2h10l2-2V7m-2 0H6m12 0V5a2 2 0 00-2-2H6a2 2 0 00-2 2v2m12 0h2a2 2 0 012 2v4m-10 4H6m4 0v-2m6 2v-2"></path></svg>
                            Data Master
                        </span>
                        <svg class="h-4 w-4 transform transition-transform duration-200" id="masterDataArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="masterDataDropdown" class="hidden mt-2 space-y-2 bg-gray-700 rounded-md">
                        <a href="kth.php" class="flex items-center px-6 py-2 text-gray-300 hover:bg-gray-600 hover:text-white rounded-lg <?php echo ($current_page == 'kth') ? 'bg-green-700 text-white' : ''; ?>">
                            <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            KTH
                        </a>
                        <a href="penyuluh.php" class="flex items-center px-6 py-2 text-gray-300 hover:bg-gray-600 hover:text-white rounded-lg <?php echo ($current_page == 'penyuluh') ? 'bg-green-700 text-white' : ''; ?>">
                            <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>
                            Penyuluh
                        </a>
                        <a href="komoditas.php" class="flex items-center px-6 py-2 text-gray-300 hover:bg-gray-600 hover:text-white rounded-lg <?php echo ($current_page == 'komoditas') ? 'bg-green-700 text-white' : ''; ?>">
                            <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            Komoditas
                        </a>
                    </div>
                </div>
                
                <a href="produksi.php" class="flex items-center px-4 py-2 rounded-lg <?php echo ($current_page == 'produksi') ? 'bg-green-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?>">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Data Produksi
                </a>
                
                <a href="laporan.php" class="flex items-center px-4 py-2 rounded-lg <?php echo ($current_page == 'laporan') ? 'bg-green-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?>">
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Laporan
                </a>
                
                <?php if (in_array($_SESSION['role'], ['admin-prov', 'admin-kab'])): ?>
                    <a href="users.php" class="flex items-center px-4 py-2 rounded-lg <?php echo ($current_page == 'users') ? 'bg-green-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?>">
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>
                        Manajemen Pengguna
                    </a>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="flex justify-between items-center bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center">
                    <button id="sidebar-toggle" class="text-gray-500 focus:outline-none md:hidden">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-2xl font-semibold text-gray-800 ml-3"><?php echo $page_title ?? 'Dashboard'; ?></h1>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="user-menu-button" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                            <span class="font-medium"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="user-menu-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden z-50">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <?php if (isset($success)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Sukses!</strong>
                        <span class="block sm:inline"><?php echo $success; ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline"><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>
