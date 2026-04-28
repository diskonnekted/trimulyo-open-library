<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Filter
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Build Query
$query = "SELECT books.*, categories.name as category_name FROM books LEFT JOIN categories ON books.category_id = categories.id WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (title LIKE ? OR author LIKE ? OR publisher LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category) {
    $query .= " AND category_id = ?";
    $params[] = $category;
}

if ($type && in_array($type, ['physical', 'digital'])) {
    $query .= " AND type = ?";
    $params[] = $type;
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll();

// Get Categories for filter
$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Katalog Buku</h1>
    </div>

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Filter -->
        <div class="w-full md:w-1/4">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="font-bold text-lg mb-4 border-b pb-2">Filter Pencarian</h3>
                <form action="" method="GET">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci</label>
                        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" class="w-full border-gray-300 border rounded-md px-3 py-2 focus:ring-icy_blue-200 focus:border-frosted_mint-600">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="cat" class="w-full border-gray-300 border rounded-md px-3 py-2 focus:ring-icy_blue-200 focus:border-frosted_mint-600">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($cats as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $category == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Koleksi</label>
                        <select name="type" class="w-full border-gray-300 border rounded-md px-3 py-2 focus:ring-icy_blue-200 focus:border-frosted_mint-600">
                            <option value="">Semua</option>
                            <option value="physical" <?= $type == 'physical' ? 'selected' : '' ?>>Buku Fisik</option>
                            <option value="digital" <?= $type == 'digital' ? 'selected' : '' ?>>Digital (PDF)</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-baby_pink-600 text-white font-bold py-2 rounded hover:bg-baby_pink-700 transition">Terapkan Filter</button>
                    <a href="catalog.php" class="block text-center text-sm text-gray-500 mt-2 hover:text-gray-700">Reset Filter</a>
                </form>
            </div>
        </div>

        <!-- Book Grid -->
        <div class="w-full md:w-3/4">
            <?php if (count($books) > 0): ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <?php foreach ($books as $book): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition flex flex-col h-full group">
                        <div class="aspect-[2/3] bg-gray-200 overflow-hidden relative">
                            <?php if ($book['cover_image']): ?>
                                <img src="uploads/covers/<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Status Ribbons/Badges -->
                            <?php if ($book['type'] == 'physical' && $book['stock'] <= 0): ?>
                                <div class="absolute top-0 left-0 z-10">
                                    <div class="bg-red-600 text-white text-[10px] font-bold px-6 py-1 shadow-md transform -rotate-45 -translate-x-7 translate-y-4 w-32 text-center border-2 border-white">
                                        DIPINJAM
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($book['type'] == 'digital'): ?>
                                <span class="absolute top-2 right-2 bg-baby_pink-600 text-white text-xs font-bold px-2 py-1 rounded shadow">E-Book</span>
                            <?php else: ?>
                                <span class="absolute top-2 right-2 bg-icy_blue-600 text-white text-xs font-bold px-2 py-1 rounded shadow">Fisik</span>
                            <?php endif; ?>
                        </div>
                        <div class="p-3 flex-grow flex flex-col">
                            <div class="text-xs text-mauve-600 font-semibold mb-1 uppercase tracking-wide truncate">
                                <?= $book['category_name'] ? htmlspecialchars($book['category_name']) : 'Umum' ?>
                            </div>
                            <h3 class="font-bold text-sm mb-1 text-gray-900 leading-tight line-clamp-2 group-hover:text-baby_pink-600 transition" title="<?= htmlspecialchars($book['title']) ?>"><?= htmlspecialchars($book['title']) ?></h3>
                            <p class="text-xs text-gray-600 mb-2 line-clamp-1" title="<?= htmlspecialchars($book['author']) ?>"><?= htmlspecialchars($book['author']) ?></p>
                            
                            <div class="mt-auto pt-2 border-t border-gray-100">
                                <a href="detail.php?id=<?= $book['id'] ?>" class="block w-full text-center bg-white border border-icy_blue-600 text-icy_blue-600 hover:bg-icy_blue-600 hover:text-white text-xs font-bold py-2 rounded transition">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="bg-white p-12 rounded-lg shadow-sm text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="text-lg font-medium text-gray-900">Tidak ada buku ditemukan</h3>
                    <p class="text-gray-500 mt-1">Coba ubah kata kunci atau filter pencarian Anda.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
