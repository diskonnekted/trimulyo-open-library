<?php
require_once 'includes/header.php';

// Pagination Setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$where = "WHERE 1=1";
$params = [];

if ($search) {
    $where .= " AND (title LIKE ?)";
    $params[] = "%$search%";
}

// Get Total Records
$stmt = $pdo->prepare("SELECT COUNT(*) FROM news $where");
$stmt->execute($params);
$total_results = $stmt->fetchColumn();
$total_pages = ceil($total_results / $limit);

// Get News
$query = "SELECT news.*, news_categories.name as category_name FROM news LEFT JOIN news_categories ON news.category_id = news_categories.id $where ORDER BY created_at DESC LIMIT $start, $limit";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$news_list = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Kelola Berita</h1>
    <a href="news_form.php" class="bg-baby_pink-600 hover:bg-baby_pink-700 text-white font-bold py-2 px-4 rounded flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Berita
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b border-gray-200">
        <form action="" method="GET" class="flex">
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari judul berita..." class="flex-grow border rounded-l px-4 py-2 focus:outline-none focus:ring-2 focus:ring-icy_blue-200 focus:border-frosted_mint-600">
            <button type="submit" class="bg-gray-100 border border-l-0 rounded-r px-4 hover:bg-gray-200">Cari</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 uppercase text-xs leading-normal">
                    <th class="py-3 px-6 text-left">Judul</th>
                    <th class="py-3 px-6 text-left">Kategori</th>
                    <th class="py-3 px-6 text-center">Status</th>
                    <th class="py-3 px-6 text-center">Tanggal</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php if (count($news_list) > 0): ?>
                    <?php foreach ($news_list as $news): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">
                            <div class="flex items-center">
                                <?php if ($news['image']): ?>
                                    <img class="w-12 h-8 object-cover mr-3 rounded" src="../uploads/news/<?= htmlspecialchars($news['image']) ?>">
                                <?php else: ?>
                                    <div class="w-12 h-8 bg-gray-200 mr-3 rounded flex items-center justify-center text-xs">No Img</div>
                                <?php endif; ?>
                                <span class="font-medium"><?= htmlspecialchars($news['title']) ?></span>
                            </div>
                        </td>
                        <td class="py-3 px-6 text-left">
                            <span class="bg-gray-200 text-gray-600 py-1 px-3 rounded-full text-xs"><?= htmlspecialchars($news['category_name'] ?? '-') ?></span>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <?php if ($news['status'] == 'published'): ?>
                                <span class="bg-green-200 text-green-600 py-1 px-3 rounded-full text-xs">Published</span>
                            <?php else: ?>
                                <span class="bg-yellow-200 text-yellow-600 py-1 px-3 rounded-full text-xs">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <?= date('d M Y', strtotime($news['created_at'])) ?>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center">
                                <a href="news_form.php?id=<?= $news['id'] ?>" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                                <a href="news_action.php?delete=<?= $news['id'] ?>" onclick="return confirm('Yakin ingin menghapus berita ini?')" class="w-4 transform hover:text-red-500 hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="py-3 px-6 text-center">Tidak ada berita ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
        <div class="inline-flex mt-2 xs:mt-0">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&q=<?= urlencode($search) ?>" class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-l">Prev</a>
            <?php endif; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>" class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-r">Next</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
