<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Fetch Data
try {
    $stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 8");
    $recentBooks = $stmt->fetchAll();
} catch (PDOException $e) {
    $recentBooks = [];
}

// SAMPLE DATA GENERATOR (Fallback if no books exist)
// Penting untuk preview jika database kosong
if (empty($recentBooks)) {
    for ($i = 1; $i <= 4; $i++) {
        $recentBooks[] = [
            'id' => '#',
            'title' => "The Art of Library " . $i,
            'author' => "Sample Author",
            'cover' => null,
            'created_at' => date('Y-m-d')
        ];
    }
}

try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name LIMIT 6");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

// Settings
$hero_title = $pdo->query("SELECT value FROM settings WHERE key_name = 'hero_title'")->fetchColumn() ?: "Expand Your Mind";
$hero_subtitle = $pdo->query("SELECT value FROM settings WHERE key_name = 'hero_subtitle'")->fetchColumn() ?: "Discover a world of knowledge with our premium collection of digital and physical books.";
$hero_image = $pdo->query("SELECT value FROM settings WHERE key_name = 'hero_image'")->fetchColumn();

// If hero_image is not set, use default background
$bg_image_url = $hero_image ? "uploads/hero/$hero_image" : "uploads/background.jpg";
if (!file_exists($bg_image_url)) {
    // Fallback if local file missing
    $bg_image_url = "https://images.unsplash.com/photo-1507842217343-583bb7270b66?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80"; 
}
?>

<!-- Modern Dark/Sleek Theme CSS Overrides -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');
    
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        /* Remove default background color to let image show */
        color: #f8fafc;
    }
    
    .glass-nav {
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(16px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .bg-fixed-cover {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -2;
        background-image: url('<?= htmlspecialchars($bg_image_url) ?>');
        background-size: cover;
        background-position: center;
    }
    
    .bg-overlay-dark {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        background: linear-gradient(to bottom, rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.95));
    }
    
    /* Ensure cards have contrast against image */
    .glass-card {
        background: rgba(30, 41, 59, 0.6);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    }

    /* Hide Default Header/Nav since we build a custom immersive one */
    nav.bg-mauve-900 { display: none !important; }
    footer.bg-mauve-900 { background-color: #020617 !important; border-top: 1px solid #1e293b; }
</style>

<!-- Fixed Backgrounds -->
<div class="bg-fixed-cover"></div>
<div class="bg-overlay-dark"></div>

<!-- Custom Immersive Header -->
<header class="fixed w-full top-0 z-50 glass-nav transition-all duration-300">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="index2.php" class="text-2xl font-extrabold tracking-tight flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <span class="text-white">Lib<span class="text-blue-400">rary</span>.</span>
        </a>
        
        <div class="hidden md:flex items-center space-x-8">
            <a href="index2.php" class="text-sm font-medium text-white hover:text-blue-400 transition shadow-sm">Home</a>
            <a href="catalog.php" class="text-sm font-medium text-gray-300 hover:text-white transition">Catalog</a>
            <a href="news.php" class="text-sm font-medium text-gray-300 hover:text-white transition">News</a>
            
            <?php if (isset($_SESSION['member_id'])): ?>
                <a href="member/index.php" class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-full hover:bg-blue-500 transition shadow-lg shadow-blue-500/30">Dashboard</a>
            <?php else: ?>
                <a href="login.php" class="text-sm font-medium text-gray-300 hover:text-white transition">Login</a>
                <a href="register.php" class="px-5 py-2.5 text-sm font-bold text-slate-900 bg-white rounded-full hover:bg-gray-100 transition shadow-lg shadow-white/10">Join Now</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="pt-24 min-h-screen overflow-hidden relative">
    
    <!-- Hero Section -->
    <section class="relative container mx-auto px-6 py-12 lg:py-24 flex flex-col items-center text-center z-10">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 border border-white/20 text-sm font-medium text-blue-300 mb-8 backdrop-blur-sm">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            New Collection Available
        </div>
        
        <h1 class="text-5xl md:text-7xl lg:text-8xl font-extrabold tracking-tight mb-8 leading-tight max-w-5xl text-white drop-shadow-2xl">
            <?= htmlspecialchars($hero_title) ?>
        </h1>
        
        <p class="text-lg md:text-xl text-gray-200 mb-12 max-w-2xl leading-relaxed drop-shadow-md">
            <?= htmlspecialchars($hero_subtitle) ?>
        </p>

        <!-- Modern Search Input -->
        <div class="w-full max-w-2xl relative group">
            <!-- Glow Effect -->
            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl blur opacity-30 group-hover:opacity-80 transition duration-1000 group-hover:duration-200"></div>
            
            <form action="catalog.php" class="relative flex bg-slate-900/90 rounded-xl p-2 border border-white/20 backdrop-blur-xl shadow-2xl">
                <div class="flex-grow relative">
                    <svg class="w-6 h-6 absolute left-4 top-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="q" placeholder="Search books, authors, or ISBN..." class="w-full bg-transparent text-white pl-12 pr-4 py-3 focus:outline-none placeholder-gray-400 text-lg">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-2 rounded-lg font-bold transition shadow-lg shadow-blue-900/50 border border-white/10">
                    Search
                </button>
            </form>
        </div>

        <!-- 3D Book Showcase (Visual Only) -->
        <div class="mt-20 w-full max-w-6xl relative perspective-1000">
            <!-- Grid of floating covers -->
            <div class="flex justify-center gap-4 md:gap-8 flex-wrap">
                <?php foreach (array_slice($recentBooks, 0, 5) as $index => $book): ?>
                    <div class="w-24 md:w-32 lg:w-40 aspect-[2/3] rounded-lg shadow-2xl transform hover:-translate-y-4 transition duration-300 border border-white/20 overflow-hidden bg-slate-800 relative group" style="margin-top: <?= $index % 2 == 0 ? '0' : '40px' ?>;">
                        <?php if (!empty($book['cover']) && file_exists("uploads/covers/" . $book['cover'])): ?>
                            <img src="uploads/covers/<?= htmlspecialchars($book['cover']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-slate-700 to-slate-800 flex items-center justify-center">
                                <span class="text-xs text-center p-2 text-gray-400"><?= htmlspecialchars($book['title']) ?></span>
                            </div>
                        <?php endif; ?>
                        <!-- Reflection/Gloss -->
                        <div class="absolute inset-0 bg-gradient-to-tr from-white/10 to-transparent pointer-events-none"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Categories Marquee -->
    <div class="w-full bg-black/30 border-y border-white/10 py-8 overflow-hidden backdrop-blur-sm">
        <div class="flex space-x-12 animate-marquee whitespace-nowrap justify-center">
            <?php foreach ($categories as $cat): ?>
                <a href="catalog.php?category=<?= $cat['id'] ?>" class="flex items-center gap-3 text-gray-300 hover:text-white transition group cursor-pointer">
                    <span class="w-2 h-2 rounded-full bg-blue-500 group-hover:scale-150 transition shadow-[0_0_10px_rgba(59,130,246,0.5)]"></span>
                    <span class="text-xl font-bold tracking-tight uppercase"><?= htmlspecialchars($cat['name']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Featured Section (Horizontal Cards) -->
    <section class="container mx-auto px-6 py-24">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold mb-2 text-white">Curated for You</h2>
                <p class="text-gray-300">Handpicked selections just added to our library.</p>
            </div>
            <a href="catalog.php" class="group flex items-center gap-2 text-blue-400 font-bold mt-4 md:mt-0 hover:text-blue-300 transition">
                View All Books 
                <svg class="w-4 h-4 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach (array_slice($recentBooks, 0, 4) as $book): ?>
                <div class="group relative glass-card rounded-2xl p-4 hover:bg-slate-800/80 transition duration-300 hover:border-blue-500/50 flex flex-col h-full">
                    <div class="flex gap-4 flex-grow">
                        <!-- Tiny Cover -->
                        <div class="w-24 aspect-[2/3] rounded-lg overflow-hidden shadow-lg flex-shrink-0 border border-white/10 relative">
                             <?php if (!empty($book['cover']) && file_exists("uploads/covers/" . $book['cover'])): ?>
                                <img src="uploads/covers/<?= htmlspecialchars($book['cover']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            <?php else: ?>
                                <div class="w-full h-full bg-slate-700 flex items-center justify-center">
                                    <span class="text-[10px] text-gray-400">No Image</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Info -->
                        <div class="flex flex-col py-1">
                            <h3 class="font-bold text-lg leading-tight mb-1 text-white group-hover:text-blue-400 transition line-clamp-2">
                                <?= htmlspecialchars($book['title']) ?>
                            </h3>
                            <p class="text-sm text-gray-400 mb-2"><?= htmlspecialchars($book['author']) ?></p>
                            
                            <div class="mt-auto">
                                <span class="text-xs bg-white/10 text-blue-200 px-2 py-1 rounded">New Arrival</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Button -->
                    <a href="detail.php?id=<?= $book['id'] ?>" class="mt-4 w-full block text-center py-2 rounded-lg bg-blue-600/20 text-blue-300 font-bold hover:bg-blue-600 hover:text-white transition text-sm">
                        View Details
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>
