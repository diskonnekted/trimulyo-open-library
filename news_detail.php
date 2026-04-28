<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

$stmt = $pdo->prepare("SELECT news.*, news_categories.name as category_name, news_categories.slug as category_slug FROM news LEFT JOIN news_categories ON news.category_id = news_categories.id WHERE news.slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$news = $stmt->fetch();

if (!$news) {
    echo "<div class='container mx-auto px-4 py-12 text-center'><h2 class='text-2xl font-bold'>Berita tidak ditemukan</h2><a href='news.php' class='text-mauve-900 mt-4 inline-block'>Kembali ke Berita</a></div>";
    require_once 'includes/footer.php';
    exit;
}

// Update Views
$pdo->prepare("UPDATE news SET views = views + 1 WHERE id = ?")->execute([$news['id']]);

// Get Tags
$stmt = $pdo->prepare("SELECT t.* FROM news_tags t JOIN news_tag_map m ON t.id = m.tag_id WHERE m.news_id = ?");
$stmt->execute([$news['id']]);
$tags = $stmt->fetchAll();

// Get Related Books
$stmt = $pdo->prepare("SELECT b.* FROM books b JOIN news_book_map m ON b.id = m.book_id WHERE m.news_id = ?");
$stmt->execute([$news['id']]);
$attached_books = $stmt->fetchAll();

// Get Related News
$stmt = $pdo->prepare("SELECT title, slug, image, created_at FROM news WHERE category_id = ? AND id != ? AND status = 'published' ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$news['category_id'], $news['id']]);
$related_news = $stmt->fetchAll();
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Breadcrumb -->
        <div class="bg-gray-50 px-6 py-3 border-b text-sm text-gray-500">
            <a href="index.php" class="hover:text-mauve-900">Home</a> / 
            <a href="news.php" class="hover:text-mauve-900">Berita</a> / 
            <span class="text-gray-700"><?= htmlspecialchars($news['title']) ?></span>
        </div>

        <?php if ($news['image']): ?>
            <div class="w-full h-64 md:h-96 overflow-hidden">
                <img src="uploads/news/<?= htmlspecialchars($news['image']) ?>" alt="<?= htmlspecialchars($news['title']) ?>" class="w-full h-full object-cover">
            </div>
        <?php endif; ?>

        <div class="p-6 md:p-10">
            <div class="flex items-center space-x-4 mb-6">
                <a href="news.php?cat=<?= $news['category_slug'] ?>" class="bg-icy_blue-100 text-mauve-900 px-3 py-1 rounded-full text-sm font-semibold hover:bg-icy_blue-200">
                    <?= htmlspecialchars($news['category_name']) ?>
                </a>
                <span class="text-gray-500 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <?= date('d M Y', strtotime($news['created_at'])) ?>
                </span>
                <span class="text-gray-500 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <?= $news['views'] ?> views
                </span>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6 leading-tight"><?= htmlspecialchars($news['title']) ?></h1>

            <div class="prose max-w-none text-gray-800 leading-relaxed mb-8">
                <?= nl2br(htmlspecialchars($news['content'])) ?>
            </div>

            <?php if (count($attached_books) > 0): ?>
                <div class="mb-8 p-4 bg-icy_blue-100 rounded-lg border border-icy_blue-200">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Disebutkan dalam artikel ini
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($attached_books as $book): ?>
                            <a href="detail.php?id=<?= $book['id'] ?>" class="flex items-center bg-white p-2 rounded shadow-sm hover:shadow-md transition group">
                                <div class="w-12 h-16 flex-shrink-0 bg-gray-200 overflow-hidden rounded">
                                    <?php if ($book['cover_image']): ?>
                                        <img src="uploads/covers/<?= htmlspecialchars($book['cover_image']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Img</div>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-3">
                                    <h4 class="font-bold text-sm text-gray-900 group-hover:text-mauve-900 leading-tight"><?= htmlspecialchars($book['title']) ?></h4>
                                    <p class="text-xs text-gray-600"><?= htmlspecialchars($book['author']) ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (count($tags) > 0): ?>
                <div class="border-t pt-6 mb-8">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($tags as $t): ?>
                            <a href="news.php?tag=<?= $t['slug'] ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded transition text-sm">
                                #<?= htmlspecialchars($t['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Share Buttons (Static) -->
            <div class="flex items-center space-x-2 border-t pt-6">
                <span class="text-gray-600 font-medium mr-2">Bagikan:</span>
                <a href="#" class="bg-frosted_mint-600 text-white p-2 rounded-full hover:bg-frosted_mint-700"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></a>
                <a href="#" class="bg-mauve-800 text-white p-2 rounded-full hover:bg-mauve-900"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg></a>
                <a href="#" class="bg-green-500 text-white p-2 rounded-full hover:bg-green-600"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.894-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg></a>
            </div>
        </div>
    </div>

    <!-- Related News -->
    <?php if (count($related_news) > 0): ?>
    <div class="max-w-4xl mx-auto mt-12">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">Berita Terkait</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($related_news as $item): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <a href="news_detail.php?slug=<?= $item['slug'] ?>" class="block aspect-video bg-gray-200 overflow-hidden relative group">
                        <?php if ($item['image']): ?>
                            <img src="uploads/news/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        <?php endif; ?>
                    </a>
                    <div class="p-4">
                        <div class="text-xs text-gray-500 mb-1"><?= date('d M Y', strtotime($item['created_at'])) ?></div>
                        <h4 class="font-bold text-gray-800 leading-tight hover:text-mauve-900">
                            <a href="news_detail.php?slug=<?= $item['slug'] ?>"><?= htmlspecialchars($item['title']) ?></a>
                        </h4>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
