<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';
require_once 'classes/OpenLibraryConnector.php';

$connector = new OpenLibraryConnector();

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'q'; // q (keyword), title, author, isbn
$results = [];

if ($query) {
    $results = $connector->search($query, $type, 12);
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Pencarian Global (Open Library)</h1>
        <p class="text-gray-600 mb-8">Cari metadata buku dari 20+ juta koleksi global yang disediakan oleh Open Library.</p>

        <!-- Search Form -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-1/4">
                    <select name="type" class="w-full border-gray-300 rounded-lg focus:ring-icy_blue-200 focus:border-frosted_mint-600 p-3 bg-gray-50">
                        <option value="q" <?= $type == 'q' ? 'selected' : '' ?>>Semua</option>
                        <option value="title" <?= $type == 'title' ? 'selected' : '' ?>>Judul</option>
                        <option value="author" <?= $type == 'author' ? 'selected' : '' ?>>Penulis</option>
                        <option value="isbn" <?= $type == 'isbn' ? 'selected' : '' ?>>ISBN</option>
                    </select>
                </div>
                <div class="w-full md:w-3/4 flex gap-2">
                    <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Masukkan kata kunci (judul, penulis, atau ISBN)..." class="w-full border-gray-300 rounded-lg focus:ring-icy_blue-200 focus:border-frosted_mint-600 p-3" required>
                    <button type="submit" class="bg-baby_pink-600 text-white font-bold px-8 py-3 rounded-lg hover:bg-baby_pink-700 transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Results -->
        <?php if ($query): ?>
            <?php if (count($results) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($results as $book): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition border border-gray-100 flex h-full">
                        <!-- Cover Image -->
                        <div class="w-1/3 bg-gray-200 relative overflow-hidden group">
                            <?php if ($book['cover']): ?>
                                <img src="<?= htmlspecialchars($book['cover']) ?>" alt="Cover" class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400 flex-col p-2 text-center">
                                    <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-xs">No Cover</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="p-4 w-2/3 flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-lg text-gray-800 line-clamp-2 mb-1" title="<?= htmlspecialchars($book['title']) ?>"><?= htmlspecialchars($book['title']) ?></h3>
                                <p class="text-sm text-gray-600 mb-1 font-semibold"><?= htmlspecialchars($book['author']) ?></p>
                                <div class="text-xs text-gray-500 mb-2 space-y-1">
                                    <p>Tahun: <?= htmlspecialchars($book['year']) ?></p>
                                    <p>Penerbit: <?= htmlspecialchars($book['publisher']) ?></p>
                                    <?php if($book['isbn']): ?>
                                        <p class="font-mono bg-gray-100 inline-block px-1 rounded">ISBN: <?= htmlspecialchars($book['isbn']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <a href="<?= htmlspecialchars($book['link']) ?>" target="_blank" class="text-frosted_mint-700 hover:text-frosted_mint-800 text-sm font-semibold inline-flex items-center">
                                    Lihat di Open Library
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="bg-icy_blue-100 border-l-4 border-baby_pink-600 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-baby_pink-700" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-mauve-900">
                                Tidak ada buku ditemukan dengan kata kunci "<strong><?= htmlspecialchars($query) ?></strong>".
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
