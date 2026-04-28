<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$site_name_admin = function_exists('getSetting') ? getSetting($pdo, 'library_name', 'Perpustakaan') : 'Perpustakaan';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?= htmlspecialchars($site_name_admin) ?></title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>uploads/logo/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        baby_pink: { DEFAULT: '#fb8500', 100: '#FFF3E0', 200: '#FFE0B2', 300: '#FFCC80', 400: '#FFB74D', 500: '#FB8500', 600: '#E27600', 700: '#C76600', 800: '#A95600', 900: '#6B3800' },
                        lemon_chiffon: { DEFAULT: '#ffb703', 100: '#FFF7E0', 200: '#FFE8A3', 300: '#FFD66B', 400: '#FFC53A', 500: '#FFB703', 600: '#E6A402', 700: '#CC9302', 800: '#A67702', 900: '#5E4501' },
                        frosted_mint: { DEFAULT: '#219ebc', 100: '#E6F5FA', 200: '#CDECF6', 300: '#A3DBED', 400: '#6EC3E0', 500: '#219EBC', 600: '#1C8AA4', 700: '#176F86', 800: '#12586A', 900: '#0B3642' },
                        icy_blue: { DEFAULT: '#8ecae6', 100: '#EFF7FC', 200: '#D9EEF9', 300: '#BFE3F5', 400: '#A5D7EF', 500: '#8ECAE6', 600: '#6FB7DA', 700: '#559FC6', 800: '#3D84AC', 900: '#245570' },
                        mauve: { DEFAULT: '#023047', 100: '#E6EEF2', 200: '#CCDDE5', 300: '#9EBECE', 400: '#6E96AB', 500: '#023047', 600: '#02283A', 700: '#011F2D', 800: '#011723', 900: '#000E16' }
                    }
                }
            }
        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 flex min-h-screen">
    <!-- Sidebar -->
    <aside class="bg-mauve-900 text-white w-64 hidden md:flex flex-col">
        <div class="p-6 text-center border-b border-mauve-800">
            <h2 class="text-2xl font-bold">Admin Panel</h2>
        </div>
        <nav class="flex-grow p-4 space-y-2">
            <a href="dashboard.php" class="block px-4 py-3 rounded hover:bg-mauve-800 transition <?= $currentPage == 'dashboard.php' ? 'bg-mauve-800' : '' ?>">
                Dashboard
            </a>
            <a href="books.php" class="block px-4 py-3 rounded hover:bg-mauve-800 transition <?= strpos($currentPage, 'book') !== false ? 'bg-mauve-800' : '' ?>">
                Kelola Buku
            </a>
            <a href="openlibrary.php" class="block px-4 py-3 rounded hover:bg-mauve-800 transition <?= strpos($currentPage, 'openlibrary') !== false ? 'bg-mauve-800' : '' ?>">
                Cari OpenLibrary
            </a>
            <a href="categories.php" class="block px-4 py-3 rounded hover:bg-mauve-800 transition <?= $currentPage == 'categories.php' ? 'bg-mauve-800' : '' ?>">
                Kategori Buku
            </a>
            <div class="pt-2 pb-1 text-xs text-gray-400 uppercase px-4">Keanggotaan</div>
            <a href="members.php" class="block px-4 py-3 rounded hover:bg-mauve-800 transition <?= strpos($currentPage, 'member') !== false ? 'bg-mauve-800' : '' ?>">
                Kelola Anggota
            </a>
            <a href="loans.php" class="block px-4 py-3 rounded hover:bg-mauve-800 transition <?= strpos($currentPage, 'loan') !== false ? 'bg-mauve-800' : '' ?>">
                Sirkulasi Peminjaman
            </a>
            <div class="pt-2 pb-1 text-xs text-gray-400 uppercase px-4">Berita</div>
            <a href="news.php" class="block px-4 py-3 rounded hover:bg-mauve-800 transition <?= strpos($currentPage, 'news.php') !== false || strpos($currentPage, 'news_form.php') !== false ? 'bg-mauve-800' : '' ?>">
                Kelola Berita
            </a>
            <a href="news_categories.php" class="block px-4 py-3 rounded hover:bg-mauve-800 transition <?= strpos($currentPage, 'news_categories.php') !== false ? 'bg-mauve-800' : '' ?>">
                Kategori Berita
            </a>
            <div class="pt-2 pb-1 text-xs text-gray-400 uppercase px-4">Pengaturan</div>
            <a href="settings.php" class="block px-4 py-3 rounded hover:bg-mauve-800 transition <?= strpos($currentPage, 'settings') !== false ? 'bg-mauve-800' : '' ?>">
                Pengaturan Tampilan
            </a>
        </nav>
        <div class="p-4 border-t border-mauve-800">
            <a href="logout.php" class="block px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-center transition">Logout</a>
        </div>
    </aside>

    <!-- Mobile Header -->
    <div class="flex-grow flex flex-col">
        <header class="bg-white shadow p-4 flex justify-between items-center md:hidden">
            <span class="font-bold text-xl">Admin Panel</span>
            <button onclick="document.querySelector('aside').classList.toggle('hidden'); document.querySelector('aside').classList.toggle('absolute'); document.querySelector('aside').classList.toggle('z-50'); document.querySelector('aside').classList.toggle('h-full');" class="text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </header>

        <main class="flex-grow p-6 overflow-y-auto">
