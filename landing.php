<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Pengelolaan HHK dan HHBK</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="antialiased bg-white text-gray-800">
    <!-- Navigation -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="text-2xl font-bold text-green-600">HHK &amp; HHBK</div>
                <nav class="space-x-6">
                    <a href="#features" class="text-gray-600 hover:text-gray-900">Fitur</a>
                    <a href="#contact" class="text-gray-600 hover:text-gray-900">Kontak</a>
                    <a href="login.php"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Masuk</a>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-green-50 to-green-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <div class="text-center">
                    <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 mb-6">
                        Sistem Informasi Pengelolaan HHK &amp; HHBK
                    </h1>
                    <p class="max-w-2xl mx-auto text-lg text-gray-600 mb-8">
                        Kelola data hasil hutan kayu dan bukan kayu secara efisien dan terintegrasi.
                    </p>
                    <a href="login.php"
                        class="inline-block bg-green-600 text-white px-8 py-3 rounded-lg text-lg font-medium hover:bg-green-700">
                        Mulai Sekarang
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-center mb-12">Fitur Unggulan</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center p-6 bg-white rounded-xl shadow">
                        <div
                            class="mx-auto h-12 w-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Monitoring Produksi</h3>
                        <p class="text-gray-600">Pantau produksi HHK dan HHBK secara realtime dalam satu dashboard.</p>
                    </div>
                    <div class="text-center p-6 bg-white rounded-xl shadow">
                        <div
                            class="mx-auto h-12 w-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Manajemen Pengguna</h3>
                        <p class="text-gray-600">Kelola peran dan akses pengguna sesuai kebutuhan organisasi.</p>
                    </div>
                    <div class="text-center p-6 bg-white rounded-xl shadow">
                        <div
                            class="mx-auto h-12 w-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Laporan Komprehensif</h3>
                        <p class="text-gray-600">Dapatkan laporan produksi terperinci dan siap cetak.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section id="contact" class="bg-green-600 text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold mb-4">Siap memulai?</h2>
                <p class="mb-8">Masuk sekarang untuk mengelola data produksi HHK &amp; HHBK.</p>
                <a href="login.php"
                    class="bg-white text-green-600 px-8 py-3 rounded-lg font-medium hover:bg-gray-100">Login</a>
            </div>
        </section>
    </main>

    <footer class="bg-gray-100 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
            &copy; <?php echo date('Y'); ?> Sistem Informasi Pengelolaan HHK &amp; HHBK. All rights reserved.
        </div>
    </footer>
</body>

</html>