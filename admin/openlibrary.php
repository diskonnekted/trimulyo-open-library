<?php
require_once 'includes/header.php';
require_once '../classes/OpenLibraryConnector.php';

$connector = new OpenLibraryConnector();

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'q';
$results = [];

if ($query) {
    $results = $connector->search($query, $type, 20); // Get 20 results
}
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Cari Buku OpenLibrary</h1>
    <p class="text-gray-600">Cari dan impor buku dari koleksi global OpenLibrary ke katalog lokal.</p>
</div>

<!-- Search Box -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
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
            <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Masukkan kata kunci..." class="w-full border-gray-300 rounded-lg focus:ring-icy_blue-200 focus:border-frosted_mint-600 p-3" required>
            <button type="submit" class="bg-baby_pink-600 text-white font-bold px-8 py-3 rounded-lg hover:bg-baby_pink-700 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Cari
            </button>
        </div>
    </form>
</div>

<?php if ($query): ?>
    <?php if (count($results) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($results as $book): ?>
            <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col h-full border hover:border-icy_blue-300 transition">
                <div class="h-48 bg-gray-100 flex items-center justify-center overflow-hidden relative">
                    <?php if ($book['cover']): ?>
                        <img src="<?= htmlspecialchars($book['cover']) ?>" class="h-full object-contain">
                    <?php else: ?>
                        <span class="text-gray-400">No Cover</span>
                    <?php endif; ?>
                </div>
                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-bold text-lg mb-1 line-clamp-2"><?= htmlspecialchars($book['title']) ?></h3>
                    <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($book['author']) ?></p>
                    
                    <div class="text-xs text-gray-500 space-y-1 mb-4">
                        <p>Penerbit: <?= htmlspecialchars($book['publisher']) ?></p>
                        <p>Tahun: <?= htmlspecialchars($book['year']) ?></p>
                        <?php if ($book['isbn']): ?>
                            <p>ISBN: <?= htmlspecialchars($book['isbn']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="mt-auto pt-4 border-t border-gray-100">
                        <?php
                        // Build import URL
                        $importParams = [
                            'title' => $book['title'],
                            'author' => $book['author'],
                            'publisher' => $book['publisher'],
                            'publish_location' => $book['publish_location'],
                            'year' => $book['year'],
                            'isbn' => $book['isbn'],
                            'pages' => $book['pages'],
                            'language' => $book['language'],
                            'subjects' => $book['subjects'],
                            'ddc_code' => $book['ddc'],
                            'cover' => $book['cover']
                        ];
                        $importUrl = "book_form.php?" . http_build_query($importParams);
                        ?>
                        <a href="<?= htmlspecialchars($importUrl) ?>" class="block w-full text-center bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded transition">
                            + Impor ke Katalog
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <p class="text-yellow-700">Tidak ditemukan buku dengan kata kunci tersebut.</p>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
