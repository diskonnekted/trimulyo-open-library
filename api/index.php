<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - Perpustakaan Trimulyo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
    <div class="max-w-4xl mx-auto px-6 py-12">
        <header class="mb-12 border-b pb-8">
            <h1 class="text-4xl font-extrabold text-blue-900 mb-2">API Documentation</h1>
            <p class="text-lg text-gray-600">Perpustakaan Kalurahan Trimulyo - Open Data Portal</p>
        </header>

        <section class="space-y-12">
            <!-- Info API -->
            <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <span class="bg-green-100 text-green-700 font-bold px-3 py-1 rounded-lg text-sm">GET</span>
                    <h2 class="text-2xl font-bold">Info Perpustakaan</h2>
                </div>
                <p class="text-gray-600 mb-4">Mengambil data profil, alamat, dan kontak resmi perpustakaan.</p>
                <div class="bg-gray-900 rounded-xl p-4 text-sm text-blue-300 font-mono mb-4 overflow-x-auto">
                    /api/info.php
                </div>
                <a href="info.php" target="_blank" class="text-blue-600 hover:underline font-semibold">Cek Endpoint &rarr;</a>
            </div>

            <!-- Books API -->
            <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <span class="bg-green-100 text-green-700 font-bold px-3 py-1 rounded-lg text-sm">GET</span>
                    <h2 class="text-2xl font-bold">Katalog Buku</h2>
                </div>
                <p class="text-gray-600 mb-4">Mengambil daftar koleksi buku dengan dukungan pencarian dan halaman.</p>
                <div class="bg-gray-900 rounded-xl p-4 text-sm text-blue-300 font-mono mb-4 overflow-x-auto">
                    /api/books.php?q={query}&limit={10}&page={1}
                </div>
                <div class="space-y-2 mb-6 text-sm text-gray-600">
                    <p><strong>q</strong>: Kata kunci pencarian (Opsional)</p>
                    <p><strong>limit</strong>: Jumlah data (Default: 10)</p>
                    <p><strong>page</strong>: Nomor halaman (Default: 1)</p>
                </div>
                <a href="books.php" target="_blank" class="text-blue-600 hover:underline font-semibold">Cek Endpoint &rarr;</a>
            </div>
        </section>

        <footer class="mt-16 pt-8 border-t text-center text-gray-400 text-sm">
            &copy; 2026 Perpustakaan Kalurahan Trimulyo. Built with PHP Native API.
        </footer>
    </div>
</body>
</html>
