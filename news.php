<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Pagination Setup
$limit = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Filter
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$category_slug = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$tag_slug = isset($_GET['tag']) ? trim($_GET['tag']) : '';

// Build Query
$where = "WHERE status = 'published'";
$params = [];

if ($search) {
    $where .= " AND (title LIKE ? OR content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_slug) {
    // Get category id from slug
    $stmt = $pdo->prepare("SELECT id FROM news_categories WHERE slug = ?");
    $stmt->execute([$category_slug]);
    $cat_id = $stmt->fetchColumn();
    
    if ($cat_id) {
        $where .= " AND category_id = ?";
        $params[] = $cat_id;
    }
}

if ($tag_slug) {
    // Get tag id from slug
    $stmt = $pdo->prepare("SELECT id FROM news_tags WHERE slug = ?");
    $stmt->execute([$tag_slug]);
    $tag_id = $stmt->fetchColumn();
    
    if ($tag_id) {
        $where .= " AND id IN (SELECT news_id FROM news_tag_map WHERE tag_id = ?)";
        $params[] = $tag_id;
    }
}

// Get Total Records
$stmt = $pdo->prepare("SELECT COUNT(*) FROM news $where");
$stmt->execute($params);
$total_results = $stmt->fetchColumn();
$total_pages = ceil($total_results / $limit);

// Get News
$query = "SELECT news.*, news_categories.name as category_name, news_categories.slug as category_slug FROM news LEFT JOIN news_categories ON news.category_id = news_categories.id $where ORDER BY created_at DESC LIMIT $start, $limit";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$news_list = $stmt->fetchAll();

// Get Categories for Sidebar
$cats = $pdo->query("SELECT * FROM news_categories ORDER BY name")->fetchAll();

// Get Popular Tags for Sidebar
$tags = $pdo->query("SELECT t.*, COUNT(m.news_id) as count FROM news_tags t JOIN news_tag_map m ON t.id = m.tag_id GROUP BY t.id ORDER BY count DESC LIMIT 10")->fetchAll();
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Berita & Artikel</h1>
    </div>

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        <div class="w-full md:w-1/4 order-2 md:order-1">
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h3 class="font-bold text-lg mb-4 border-b pb-2">Pencarian</h3>
                <form action="" method="GET">
                    <div class="flex">
                        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari berita..." class="w-full border-gray-300 border rounded-l-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-icy_blue-200 focus:border-frosted_mint-600">
                        <button type="submit" class="bg-baby_pink-600 text-white px-4 rounded-r-md hover:bg-baby_pink-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h3 class="font-bold text-lg mb-4 border-b pb-2">Kategori</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="news.php" class="flex items-center justify-between text-gray-700 hover:text-mauve-900 <?= empty($category_slug) ? 'font-bold text-mauve-900' : '' ?>">
                            <span>Semua Berita</span>
                        </a>
                    </li>
                    <?php foreach ($cats as $c): ?>
                        <li>
                            <a href="news.php?cat=<?= $c['slug'] ?>" class="flex items-center justify-between text-gray-700 hover:text-mauve-900 <?= $category_slug == $c['slug'] ? 'font-bold text-mauve-900' : '' ?>">
                                <span><?= htmlspecialchars($c['name']) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <?php if (count($tags) > 0): ?>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="font-bold text-lg mb-4 border-b pb-2">Tag Populer</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($tags as $t): ?>
                        <a href="news.php?tag=<?= $t['slug'] ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded transition <?= $tag_slug == $t['slug'] ? 'bg-icy_blue-100 text-mauve-900' : '' ?>">
                            #<?= htmlspecialchars($t['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- News Grid -->
        <div class="w-full md:w-3/4 order-1 md:order-2">
            <?php if (count($news_list) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($news_list as $item): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition flex flex-col h-full">
                        <a href="news_detail.php?slug=<?= $item['slug'] ?>" class="block aspect-video bg-gray-200 overflow-hidden relative group">
                            <?php if ($item['image']): ?>
                                <img src="uploads/news/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            <?php endif; ?>
                            <div class="absolute top-2 left-2 bg-mauve-900 text-white text-xs font-bold px-2 py-1 rounded opacity-90">
                                <?= htmlspecialchars($item['category_name'] ?? 'Umum') ?>
                            </div>
                        </a>
                        
                        <div class="p-4 flex flex-col flex-grow">
                            <div class="text-xs text-gray-500 mb-2 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <?= date('d M Y', strtotime($item['created_at'])) ?>
                            </div>
                            
                            <h2 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2 hover:text-mauve-900 transition">
                                <a href="news_detail.php?slug=<?= $item['slug'] ?>"><?= htmlspecialchars($item['title']) ?></a>
                            </h2>
                            
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3 flex-grow">
                                <?= htmlspecialchars(substr(strip_tags($item['content']), 0, 150)) ?>...
                            </p>
                            
                            <a href="news_detail.php?slug=<?= $item['slug'] ?>" class="text-frosted_mint-700 font-semibold text-sm hover:underline mt-auto inline-flex items-center">
                                Baca Selengkapnya
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="mt-8 flex justify-center">
                    <nav class="inline-flex rounded-md shadow-sm">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&q=<?= urlencode($search) ?>&cat=<?= urlencode($category_slug) ?>&tag=<?= urlencode($tag_slug) ?>" class="px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>&q=<?= urlencode($search) ?>&cat=<?= urlencode($category_slug) ?>&tag=<?= urlencode($tag_slug) ?>" class="px-3 py-2 border-t border-b border-gray-300 bg-white text-sm font-medium <?= $i == $page ? 'text-mauve-900 bg-icy_blue-100' : 'text-gray-700 hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>&cat=<?= urlencode($category_slug) ?>&tag=<?= urlencode($tag_slug) ?>" class="px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Next
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="bg-white p-8 rounded-lg shadow-md text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    <h3 class="text-xl font-medium text-gray-900">Tidak ada berita ditemukan</h3>
                    <p class="text-gray-500 mt-2">Coba kata kunci lain atau ubah filter pencarian.</p>
                    <a href="news.php" class="inline-block mt-4 text-mauve-900 font-semibold hover:underline">Lihat Semua Berita</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
