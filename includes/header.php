<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$site_name = function_exists('getSetting') && isset($pdo) ? getSetting($pdo, 'library_name', 'Perpustakaan Hybrid') : 'Perpustakaan Hybrid';
$site_logo = function_exists('getSetting') && isset($pdo) ? getSetting($pdo, 'library_logo') : '';
$logo_mode = function_exists('getSetting') && isset($pdo) ? getSetting($pdo, 'logo_display_mode', 'logo_text') : 'logo_text';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($site_name) ?></title>
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-frosted_mint-100 text-gray-800 flex flex-col min-h-screen pb-16 md:pb-0">
    <nav class="bg-mauve-900 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="<?= BASE_URL ?>index.php" class="text-2xl font-bold flex items-center space-x-2">
                    <?php if ($logo_mode !== 'text_only'): ?>
                        <?php if ($site_logo): ?>
                            <img src="<?= BASE_URL ?>uploads/logo/<?= htmlspecialchars($site_logo) ?>" class="h-10 w-auto object-contain" alt="Logo">
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-baby_pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if ($logo_mode !== 'logo_only'): ?>
                        <span><?= htmlspecialchars($site_name) ?></span>
                    <?php endif; ?>
                </a>
                <div class="hidden md:flex space-x-6 items-center">
                    <a href="<?= BASE_URL ?>index.php" class="hover:text-lemon_chiffon-400 transition">Beranda</a>
                    <a href="<?= BASE_URL ?>catalog.php" class="hover:text-lemon_chiffon-400 transition">Katalog Lokal</a>
                    <a href="<?= BASE_URL ?>news.php" class="hover:text-lemon_chiffon-400 transition">Berita</a>
                    <a href="<?= BASE_URL ?>search_global.php" class="hover:text-lemon_chiffon-400 transition">Pencarian Global</a>
                    <a href="<?= BASE_URL ?>info.php" class="hover:text-lemon_chiffon-400 transition">Info</a>
                    
                    <?php if (isset($_SESSION['member_id'])): ?>
                        <a href="<?= BASE_URL ?>member/index.php" class="hover:text-lemon_chiffon-400 transition font-bold">Dashboard</a>
                        <a href="<?= BASE_URL ?>logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full transition shadow-md">Keluar</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>login.php" class="hover:text-lemon_chiffon-400 transition">Masuk</a>
                        <a href="<?= BASE_URL ?>register.php" class="bg-baby_pink-600 hover:bg-baby_pink-700 text-white px-4 py-2 rounded-full transition shadow-md">Keanggotaan</a>
                    <?php endif; ?>
                </div>
                <!-- Mobile Menu Button -->
                <div class="md:hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div x-show="open" class="absolute top-16 right-0 bg-mauve-900 w-full shadow-lg z-50 text-white">
                        <div class="flex flex-col p-4 space-y-2">
                            <a href="<?= BASE_URL ?>index.php" class="block hover:text-lemon_chiffon-400">Beranda</a>
                            <a href="<?= BASE_URL ?>catalog.php" class="block hover:text-lemon_chiffon-400">Katalog Lokal</a>
                            <a href="<?= BASE_URL ?>news.php" class="block hover:text-lemon_chiffon-400">Berita</a>
                            <a href="<?= BASE_URL ?>search_global.php" class="block hover:text-lemon_chiffon-400">Pencarian Global</a>
                            <a href="<?= BASE_URL ?>info.php" class="block hover:text-lemon_chiffon-400">Info</a>
                            
                            <?php if (isset($_SESSION['member_id'])): ?>
                                <a href="<?= BASE_URL ?>member/index.php" class="block text-baby_pink-500 font-bold">Dashboard</a>
                                <a href="<?= BASE_URL ?>logout.php" class="block text-red-400">Keluar</a>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>login.php" class="block hover:text-lemon_chiffon-400">Masuk</a>
                                <a href="<?= BASE_URL ?>register.php" class="block text-baby_pink-500 font-bold">Keanggotaan</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <main class="flex-grow">
