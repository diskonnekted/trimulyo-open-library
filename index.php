<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Ambil 20 buku terbaru
$stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 20");
$recentBooks = $stmt->fetchAll();

// Ambil 8 Kategori (untuk icon section)
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name LIMIT 8");
$featuredCategories = $stmt->fetchAll();

// Ambil Pengaturan
$hero_title = function_exists('getSetting') ? getSetting($pdo, 'hero_title', "Jelajahi Dunia Ilmu Pengetahuan") : "Jelajahi Dunia Ilmu Pengetahuan";
$hero_subtitle = function_exists('getSetting') ? getSetting($pdo, 'hero_subtitle', "Koleksi buku fisik dan digital lengkap untuk kebutuhan belajar Anda.") : "Koleksi buku fisik dan digital lengkap untuk kebutuhan belajar Anda.";
$hero_image = function_exists('getSetting') ? getSetting($pdo, 'hero_image', '') : '';
?>

<!-- New Premium Modern Layout -->
<style>
    .glass {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .text-gradient {
        background: linear-gradient(135deg, #fb8500 0%, #ffb703 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .hero-gradient {
        background: radial-gradient(circle at top right, rgba(33, 158, 188, 0.15), transparent),
                    radial-gradient(circle at bottom left, rgba(251, 133, 0, 0.1), transparent);
    }
    .card-hover:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
</style>

<div class="bg-mauve-900 min-h-[85vh] relative overflow-hidden flex items-center">
    <!-- Main Hero Background Image -->
    <?php if ($hero_image): ?>
        <div class="absolute inset-0 z-0">
            <img src="uploads/hero/<?= htmlspecialchars($hero_image) ?>" alt="Hero Background" class="w-full h-full object-cover opacity-40">
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 hero-gradient mix-blend-overlay"></div>
        </div>
    <?php else: ?>
        <div class="absolute inset-0 hero-gradient z-0"></div>
    <?php endif; ?>

    <!-- Animated background blobs -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-frosted_mint-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse z-0"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-baby_pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse z-0" style="animation-delay: 2s;"></div>

    <div class="container mx-auto px-6 relative z-10 py-20">
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <!-- Hero Text -->
            <div class="w-full lg:w-3/5 text-left">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-icy_blue-300 text-sm font-bold mb-6 backdrop-blur-sm">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-baby_pink-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-baby_pink-500"></span>
                    </span>
                    <span>Pusat Literasi Digital Trimulyo</span>
                </div>
                
                <h1 class="text-5xl md:text-7xl font-extrabold text-white leading-tight mb-6">
                    Temukan <span class="text-gradient">Pengetahuan</span> <br> di Ujung Jari Anda
                </h1>
                
                <p class="text-xl text-icy_blue-100 mb-10 max-w-2xl font-light leading-relaxed">
                    <?= htmlspecialchars($hero_subtitle) ?>
                </p>

                <!-- Search Container -->
                <div class="max-w-2xl glass p-2 rounded-2xl shadow-2xl">
                    <form action="catalog.php" method="GET" class="flex flex-col md:flex-row gap-2">
                        <div class="relative flex-grow">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-6 h-6 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="q" 
                                   class="w-full bg-white/10 border-none text-white placeholder-white/50 rounded-xl py-4 pl-12 pr-4 focus:ring-2 focus:ring-baby_pink-400 focus:bg-white/20 transition text-lg backdrop-blur-md" 
                                   placeholder="Cari judul, penulis, atau topik...">
                        </div>
                        <button type="submit" class="bg-baby_pink-600 hover:bg-baby_pink-700 text-white font-bold py-4 px-10 rounded-xl shadow-lg transition transform hover:scale-105 flex items-center justify-center whitespace-nowrap">
                            Cari Koleksi
                        </button>
                    </form>
                </div>

                <!-- Stats -->
                <div class="mt-12 flex flex-wrap gap-8">
                    <div class="flex flex-col">
                        <span class="text-3xl font-bold text-white">2000+</span>
                        <span class="text-icy_blue-300 text-sm uppercase tracking-wider">Koleksi Buku</span>
                    </div>
                    <div class="flex flex-col border-l border-white/20 pl-8">
                        <span class="text-3xl font-bold text-white">500+</span>
                        <span class="text-icy_blue-300 text-sm uppercase tracking-wider">E-Book Digital</span>
                    </div>
                    <div class="flex flex-col border-l border-white/20 pl-8">
                        <span class="text-3xl font-bold text-white">100%</span>
                        <span class="text-icy_blue-300 text-sm uppercase tracking-wider">Akses Gratis</span>
                    </div>
                </div>
            </div>

            <!-- Hero Visual -->
            <div class="w-full lg:w-2/5 hidden lg:block">
                <div class="relative">
                    <!-- Glass Card Visualization -->
                    <div class="w-full aspect-square rounded-3xl p-8 glass relative overflow-hidden animate-float">
                        <!-- baca.jpg Background for Card -->
                        <div class="absolute inset-0 z-0">
                            <img src="uploads/baca.jpg" alt="Reading Graphic" class="w-full h-full object-cover opacity-30">
                            <div class="absolute inset-0 bg-gradient-to-br from-icy_blue-500/40 to-mauve-500/40 mix-blend-overlay"></div>
                        </div>

                        <div class="absolute -top-20 -right-20 w-64 h-64 bg-baby_pink-500/30 rounded-full blur-3xl z-0"></div>
                        <div class="relative z-10 h-full flex flex-col justify-between">
                            <div class="flex justify-between items-start">
                                <div class="bg-white/20 p-4 rounded-2xl backdrop-blur-md">
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <div class="text-right">
                                    <p class="text-white/60 text-sm">Update Terakhir</p>
                                    <p class="text-white font-bold">Hari Ini</p>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="h-2 w-full bg-white/20 rounded-full overflow-hidden">
                                    <div class="h-full w-2/3 bg-baby_pink-500 rounded-full shadow-[0_0_15px_rgba(251,133,0,0.5)]"></div>
                                </div>
                                <p class="text-white text-lg font-medium">Progress Membaca Warga</p>
                                <div class="flex -space-x-4">
                                    <div class="w-10 h-10 rounded-full border-2 border-mauve-900 bg-icy_blue-400 shadow-lg"></div>
                                    <div class="w-10 h-10 rounded-full border-2 border-mauve-900 bg-lemon_chiffon-400 shadow-lg"></div>
                                    <div class="w-10 h-10 rounded-full border-2 border-mauve-900 bg-baby_pink-400 shadow-lg"></div>
                                    <div class="w-10 h-10 rounded-full border-2 border-mauve-900 bg-white/20 flex items-center justify-center text-xs text-white backdrop-blur-md">+50</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Badge -->
                    <div class="absolute -bottom-6 -left-6 glass p-6 rounded-2xl shadow-2xl animate-float" style="animation-delay: 1.5s;">
                        <div class="flex items-center gap-4">
                            <div class="bg-green-500 w-12 h-12 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/20">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="text-white font-bold">Open Library</p>
                                <p class="text-icy_blue-200 text-sm italic">Connected Successfully</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<section class="py-24 bg-white relative">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-mauve-900 text-sm font-bold uppercase tracking-[0.2em] mb-4">Layanan Unggulan</h2>
            <p class="text-4xl md:text-5xl font-extrabold text-gray-900">Segalanya Dalam Satu <span class="text-frosted_mint-600">Platform</span></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="group p-10 rounded-3xl bg-gray-50 hover:bg-mauve-900 transition-all duration-500 card-hover relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-frosted_mint-500/10 rounded-full group-hover:bg-white/5 transition-all duration-500"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-frosted_mint-100 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-white/20 group-hover:scale-110 transition-all duration-500">
                        <svg class="w-8 h-8 text-frosted_mint-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-mauve-900 mb-4 group-hover:text-white transition-all duration-500">Koleksi Fisik & Digital</h3>
                    <p class="text-gray-600 group-hover:text-icy_blue-100 leading-relaxed transition-all duration-500">Akses ribuan buku fisik di perpustakaan kami atau baca koleksi digital (PDF) kapan saja melalui smartphone Anda.</p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="group p-10 rounded-3xl bg-gray-50 hover:bg-baby_pink-600 transition-all duration-500 card-hover relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-baby_pink-500/10 rounded-full group-hover:bg-white/5 transition-all duration-500"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-baby_pink-100 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-white/20 group-hover:scale-110 transition-all duration-500">
                        <svg class="w-8 h-8 text-baby_pink-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-mauve-900 mb-4 group-hover:text-white transition-all duration-500">Integrasi Open Library</h3>
                    <p class="text-gray-600 group-hover:text-baby_pink-50 leading-relaxed transition-all duration-500">Terhubung dengan database Open Library Global. Cari dan temukan metadata lebih dari 20 juta buku di seluruh dunia.</p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="group p-10 rounded-3xl bg-gray-50 hover:bg-icy_blue-600 transition-all duration-500 card-hover relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-icy_blue-500/10 rounded-full group-hover:bg-white/5 transition-all duration-500"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-icy_blue-100 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-white/20 group-hover:scale-110 transition-all duration-500">
                        <svg class="w-8 h-8 text-icy_blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-mauve-900 mb-4 group-hover:text-white transition-all duration-500">Keanggotaan Mandiri</h3>
                    <p class="text-gray-600 group-hover:text-icy_blue-50 leading-relaxed transition-all duration-500">Daftar secara mandiri, dapatkan kartu anggota digital, dan kelola sirkulasi peminjaman Anda melalui dashboard pribadi.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trending/Recent Books -->
<section class="py-24 bg-frosted_mint-50/30 overflow-hidden">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16">
            <div class="max-w-2xl">
                <h2 class="text-frosted_mint-600 text-sm font-bold uppercase tracking-[0.2em] mb-4">Eksplorasi Koleksi</h2>
                <p class="text-4xl font-extrabold text-gray-900">Baru Saja Ditambahkan</p>
                <p class="text-gray-500 mt-4">Jelajahi koleksi terbaru kami mulai dari literatur fiksi hingga publikasi ilmiah terkini.</p>
            </div>
            <a href="catalog.php" class="mt-8 md:mt-0 flex items-center gap-3 bg-white border border-gray-200 text-mauve-900 font-bold py-4 px-8 rounded-2xl shadow-sm hover:shadow-md transition card-hover">
                Lihat Semua Katalog
                <svg class="w-5 h-5 text-baby_pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        <?php if (count($recentBooks) > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach (array_slice($recentBooks, 0, 8) as $book): ?>
            <div class="group bg-white rounded-[2rem] p-4 shadow-sm border border-gray-100 hover:shadow-2xl transition-all duration-500 flex flex-col h-full card-hover">
                <div class="aspect-[3/4] bg-gray-100 rounded-[1.5rem] overflow-hidden relative mb-6">
                    <?php if ($book['cover_image']): ?>
                        <img src="uploads/covers/<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                            <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Type Badge -->
                    <div class="absolute top-4 right-4">
                        <?php if ($book['type'] == 'digital'): ?>
                            <span class="bg-green-500/90 backdrop-blur-md text-white text-[10px] font-bold px-3 py-1.5 rounded-full shadow-lg border border-white/20">E-BOOK</span>
                        <?php else: ?>
                            <span class="bg-mauve-900/90 backdrop-blur-md text-white text-[10px] font-bold px-3 py-1.5 rounded-full shadow-lg border border-white/20">FISIK</span>
                        <?php endif; ?>
                    </div>

                    <!-- Overlay Action -->
                    <div class="absolute inset-0 bg-mauve-900/60 opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-center justify-center backdrop-blur-sm">
                        <a href="detail.php?id=<?= $book['id'] ?>" class="bg-white text-mauve-900 font-bold py-3 px-8 rounded-xl transform translate-y-4 group-hover:translate-y-0 transition-all duration-500 shadow-xl">
                            Detail Buku
                        </a>
                    </div>
                </div>

                <div class="px-2 pb-2">
                    <span class="text-[10px] font-bold text-frosted_mint-600 uppercase tracking-widest mb-2 block">Terbaru</span>
                    <h3 class="font-bold text-xl text-mauve-900 line-clamp-1 mb-1 group-hover:text-frosted_mint-600 transition" title="<?= htmlspecialchars($book['title']) ?>"><?= htmlspecialchars($book['title']) ?></h3>
                    <p class="text-sm text-gray-500 line-clamp-1 italic"><?= htmlspecialchars($book['author']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="text-center py-24 bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <p class="text-xl text-gray-400 font-medium">Belum ada koleksi yang ditambahkan.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Call to Action Modern -->
<section class="py-24 relative overflow-hidden">
    <div class="container mx-auto px-6">
        <div class="bg-mauve-900 rounded-[3rem] p-12 md:p-24 relative overflow-hidden shadow-[0_40px_80px_rgba(2,48,71,0.3)]">
            <!-- Background Elements -->
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-white/5 to-transparent"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-baby_pink-500 rounded-full filter blur-[100px] opacity-20"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="max-w-xl text-center md:text-left">
                    <h2 class="text-4xl md:text-6xl font-extrabold text-white mb-8 leading-tight">Mulai Perjalanan <br> <span class="text-gradient">Literasi</span> Anda</h2>
                    <p class="text-xl text-icy_blue-100 mb-10 leading-relaxed font-light">Jadilah bagian dari komunitas pembaca Kalurahan Trimulyo dan nikmati akses tak terbatas ke dunia ilmu pengetahuan.</p>
                    <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                        <a href="register.php" class="bg-baby_pink-600 hover:bg-baby_pink-700 text-white font-bold py-5 px-12 rounded-2xl shadow-2xl transition transform hover:scale-105">
                            Daftar Anggota
                        </a>
                        <a href="catalog.php" class="glass text-white font-bold py-5 px-12 rounded-2xl transition hover:bg-white/10">
                            Eksplorasi Katalog
                        </a>
                    </div>
                </div>
                
                <div class="hidden lg:block w-72 h-96 bg-white/5 border border-white/10 rounded-3xl p-6 glass rotate-6 relative card-hover">
                    <div class="w-full h-full bg-mauve-800 rounded-2xl flex flex-col items-center justify-center p-8 text-center">
                        <div class="w-20 h-20 bg-baby_pink-500 rounded-full flex items-center justify-center mb-6 shadow-xl shadow-baby_pink-500/20 animate-float">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <p class="text-white font-bold text-xl mb-2">Member Card</p>
                        <p class="text-icy_blue-200 text-sm">Akses Penuh Trimulyo Digital Library</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php require_once 'includes/footer.php'; ?>
