<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Fetch Recent Books (Limit 12 for grid)
try {
    $stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 12");
    $recentBooks = $stmt->fetchAll();
} catch (PDOException $e) {
    $recentBooks = [];
}

// SAMPLE DATA GENERATOR (Fallback if no books exist)
// Ini memastikan layout tetap terlihat meskipun database kosong
if (empty($recentBooks)) {
    for ($i = 1; $i <= 6; $i++) {
        $recentBooks[] = [
            'id' => '#',
            'title' => "Buku Contoh " . $i,
            'author' => "Penulis Simulasi",
            'cover' => null,
            'created_at' => date('Y-m-d')
        ];
    }
}

// Fetch Categories
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name LIMIT 12");
    $featuredCategories = $stmt->fetchAll();
} catch (PDOException $e) {
    $featuredCategories = [];
}

// Fallback Categories if empty
if (empty($featuredCategories)) {
    $featuredCategories = [
        ['id' => 1, 'name' => 'Teknologi'],
        ['id' => 2, 'name' => 'Sains'],
        ['id' => 3, 'name' => 'Fiksi'],
        ['id' => 4, 'name' => 'Sejarah'],
        ['id' => 5, 'name' => 'Bisnis'],
    ];
}

// Fetch Settings
$hero_title = $pdo->query("SELECT value FROM settings WHERE key_name = 'hero_title'")->fetchColumn();
$hero_subtitle = $pdo->query("SELECT value FROM settings WHERE key_name = 'hero_subtitle'")->fetchColumn();
$hero_image = $pdo->query("SELECT value FROM settings WHERE key_name = 'hero_image'")->fetchColumn();

// Fallbacks
$hero_title = $hero_title ?: "Temukan Pengetahuan Tanpa Batas";
$hero_subtitle = $hero_subtitle ?: "Akses ribuan buku digital dan fisik dari perpustakaan kami. Belajar kapan saja, di mana saja.";
?>

<!-- Custom CSS for Index1 (Minimalist/Clean Theme) -->
<style>
    .font-serif-display { font-family: 'Playfair Display', serif; }
    /* Book Card Hover Effect */
    .book-card { transition: all 0.3s ease; }
    .book-card:hover { transform: translateY(-5px); }
</style>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">

<div class="bg-gray-50 min-h-screen font-sans">
    
    <!-- Hero Section (Split Layout) -->
    <section class="relative pt-12 pb-20 lg:pt-24 lg:pb-32 overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                
                <!-- Left: Text Content -->
                <div class="w-full lg:w-1/2 z-10">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-bold uppercase tracking-wide mb-6">
                        <span class="w-2 h-2 rounded-full bg-blue-600 mr-2"></span>
                        Perpustakaan Digital Masa Depan
                    </div>
                    <h1 class="text-4xl lg:text-6xl font-serif-display font-bold text-gray-900 leading-tight mb-6">
                        <?= htmlspecialchars($hero_title) ?>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed max-w-lg">
                        <?= htmlspecialchars($hero_subtitle) ?>
                    </p>
                    
                    <!-- Search Box -->
                    <div class="bg-white p-2 rounded-lg shadow-lg border border-gray-100 max-w-md">
                        <form action="catalog.php" method="GET" class="flex">
                            <input type="text" name="q" placeholder="Cari judul, penulis, ISBN..." 
                                class="flex-grow px-4 py-3 text-gray-700 focus:outline-none rounded-l-lg" required>
                            <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-6 py-3 rounded-md font-medium transition duration-200">
                                Cari
                            </button>
                        </form>
                    </div>

                    <div class="mt-8 flex items-center gap-4 text-sm text-gray-500">
                        <span>Populer:</span>
                        <div class="flex gap-2">
                            <a href="catalog.php?q=novel" class="px-3 py-1 bg-white border border-gray-200 rounded-full hover:bg-gray-50 transition">Novel</a>
                            <a href="catalog.php?q=sains" class="px-3 py-1 bg-white border border-gray-200 rounded-full hover:bg-gray-50 transition">Sains</a>
                        </div>
                    </div>
                </div>

                <!-- Right: Visual/Image -->
                <div class="w-full lg:w-1/2 relative">
                    <?php if ($hero_image && file_exists("uploads/hero/$hero_image")): ?>
                         <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                            <img src="uploads/hero/<?= htmlspecialchars($hero_image) ?>" alt="Hero Image" class="w-full h-auto object-cover transform hover:scale-105 transition duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        </div>
                    <?php else: ?>
                        <!-- Fallback Abstract Composition -->
                        <div class="relative h-[400px] lg:h-[500px] w-full">
                            <div class="absolute top-10 right-10 w-64 h-80 bg-gray-200 rounded-lg transform rotate-6 z-0"></div>
                            <div class="absolute top-0 right-20 w-64 h-80 bg-gray-900 rounded-lg transform -rotate-3 z-10 overflow-hidden shadow-2xl">
                                <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover opacity-80" alt="Book 1">
                            </div>
                            <div class="absolute bottom-10 left-10 w-56 h-72 bg-white p-4 rounded-lg shadow-xl z-20 transform -rotate-6 border border-gray-100">
                                <div class="w-full h-full bg-gray-100 rounded overflow-hidden">
                                     <img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover" alt="Book 2">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-12 bg-white border-y border-gray-100">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-serif-display font-bold text-gray-900 mb-6">Jelajahi Kategori</h2>
            <div class="flex flex-wrap gap-3">
                <?php foreach ($featuredCategories as $cat): ?>
                    <a href="catalog.php?category=<?= $cat['id'] ?>" class="group bg-gray-50 border border-gray-200 hover:border-gray-900 px-6 py-3 rounded-full transition duration-300 flex items-center">
                        <span class="text-gray-700 group-hover:text-gray-900 font-medium"><?= htmlspecialchars($cat['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Books (Grid Card Layout) -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-12">
                <h2 class="text-3xl font-serif-display font-bold text-gray-900">Buku Terbaru</h2>
                <a href="catalog.php" class="text-gray-900 font-medium hover:text-blue-600 transition flex items-center">
                    Lihat Semua <span class="ml-2">&rarr;</span>
                </a>
            </div>

            <!-- BOOK CARD GRID -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 lg:gap-8">
                <?php foreach ($recentBooks as $book): ?>
                    <div class="book-card group cursor-pointer">
                        <!-- Cover Image Container -->
                        <div class="relative aspect-[2/3] bg-white rounded-lg overflow-hidden mb-4 shadow-sm group-hover:shadow-xl transition duration-300 border border-gray-100">
                            <?php if (!empty($book['cover']) && file_exists("uploads/covers/" . $book['cover'])): ?>
                                <img src="uploads/covers/<?= htmlspecialchars($book['cover']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <!-- Fallback Placeholder if no cover -->
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gray-100 text-gray-400 p-4 text-center">
                                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    <span class="text-xs">No Cover</span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-4">
                                <a href="detail.php?id=<?= $book['id'] ?>" class="w-full bg-white text-black text-center py-2 rounded text-sm font-bold hover:bg-gray-100">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                        
                        <!-- Book Info -->
                        <h3 class="font-bold text-gray-900 text-sm leading-tight mb-1 truncate group-hover:text-blue-600 transition" title="<?= htmlspecialchars($book['title']) ?>">
                            <?= htmlspecialchars($book['title']) ?>
                        </h3>
                        <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($book['author']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-12">
                <a href="catalog.php" class="inline-block border-2 border-gray-900 text-gray-900 px-8 py-3 rounded-full font-bold hover:bg-gray-900 hover:text-white transition duration-300">
                    Jelajahi Koleksi Lengkap
                </a>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20 bg-gray-900 text-white overflow-hidden relative">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-gray-800 opacity-50 blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h2 class="text-3xl md:text-4xl font-serif-display font-bold mb-6">Mulai Membaca Hari Ini</h2>
            <p class="text-gray-400 max-w-2xl mx-auto mb-10 text-lg">
                Daftar menjadi anggota sekarang dan nikmati akses tak terbatas ke koleksi buku fisik dan digital kami.
            </p>
            <div class="flex flex-col md:flex-row justify-center gap-4">
                <?php if (!isset($_SESSION['member_id'])): ?>
                    <a href="register.php" class="bg-white text-gray-900 px-8 py-4 rounded-lg font-bold hover:bg-gray-100 transition shadow-lg">
                        Daftar Anggota
                    </a>
                    <a href="login.php" class="px-8 py-4 rounded-lg font-bold border border-gray-700 hover:bg-gray-800 transition">
                        Masuk Akun
                    </a>
                <?php else: ?>
                    <a href="member/index.php" class="bg-white text-gray-900 px-8 py-4 rounded-lg font-bold hover:bg-gray-100 transition shadow-lg">
                        Ke Dashboard Saya
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?php require_once 'includes/footer.php'; ?>
