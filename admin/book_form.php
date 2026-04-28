<?php
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$book = null;

// Pre-fill data from GET params (e.g. from OpenLibrary Import)
$pre_title = isset($_GET['title']) ? $_GET['title'] : '';
$pre_author = isset($_GET['author']) ? $_GET['author'] : '';
$pre_publisher = isset($_GET['publisher']) ? $_GET['publisher'] : '';
$pre_publish_location = isset($_GET['publish_location']) ? $_GET['publish_location'] : '';
$pre_year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$pre_isbn = isset($_GET['isbn']) ? $_GET['isbn'] : '';
$pre_pages = isset($_GET['pages']) ? $_GET['pages'] : '';
$pre_language = isset($_GET['language']) ? $_GET['language'] : 'id';
$pre_subjects = isset($_GET['subjects']) ? $_GET['subjects'] : '';
$pre_ddc_code = isset($_GET['ddc_code']) ? $_GET['ddc_code'] : '';
$pre_cover = isset($_GET['cover']) ? $_GET['cover'] : '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch();
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<div class="mb-6">
    <div class="flex items-center text-sm text-gray-500 mb-2">
        <a href="books.php" class="hover:text-frosted_mint-700">Buku</a>
        <svg class="h-4 w-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span><?= $book ? 'Edit Buku' : 'Tambah Buku' ?></span>
    </div>
    <h1 class="text-3xl font-bold text-gray-800"><?= $book ? 'Edit Buku' : 'Tambah Buku Baru' ?></h1>
</div>

<div class="bg-white rounded-lg shadow p-8">
    <form action="book_action.php" method="POST" enctype="multipart/form-data">
        <?php if ($book): ?>
            <input type="hidden" name="id" value="<?= $book['id'] ?>">
        <?php endif; ?>
        <?php if ($pre_cover): ?>
            <input type="hidden" name="remote_cover_url" value="<?= htmlspecialchars($pre_cover) ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Judul Buku</label>
                    <input type="text" name="title" value="<?= $book ? htmlspecialchars($book['title']) : htmlspecialchars($pre_title) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Penulis</label>
                        <input type="text" name="author" value="<?= $book ? htmlspecialchars($book['author']) : htmlspecialchars($pre_author) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Penerjemah</label>
                        <input type="text" name="translator" value="<?= $book ? htmlspecialchars($book['translator']) : '' ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Opsional">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">ISBN</label>
                        <input type="text" name="isbn" value="<?= $book ? htmlspecialchars($book['isbn']) : htmlspecialchars($pre_isbn) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Klasifikasi DDC</label>
                        <input type="text" name="ddc_code" value="<?= $book ? htmlspecialchars($book['ddc_code']) : htmlspecialchars($pre_ddc_code) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Penerbit</label>
                        <input type="text" name="publisher" list="publisher_list" value="<?= $book ? htmlspecialchars($book['publisher']) : htmlspecialchars($pre_publisher) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" autocomplete="off" placeholder="Pilih atau ketik...">
                        <datalist id="publisher_list">
                            <?php foreach ($publishers as $pub): ?>
                                <option value="<?= htmlspecialchars($pub) ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Kota Terbit</label>
                        <input type="text" name="publish_location" value="<?= $book ? htmlspecialchars($book['publish_location']) : htmlspecialchars($pre_publish_location) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tahun Terbit</label>
                        <input type="number" name="year" value="<?= $book ? htmlspecialchars($book['year']) : htmlspecialchars($pre_year) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Bahasa</label>
                        <select name="language" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="id" <?= ($book && $book['language'] == 'id') || (!$book && $pre_language == 'id') ? 'selected' : '' ?>>Indonesia</option>
                            <option value="en" <?= ($book && $book['language'] == 'en') || (!$book && $pre_language == 'en') ? 'selected' : '' ?>>Inggris</option>
                            <option value="jp" <?= ($book && $book['language'] == 'jp') || (!$book && $pre_language == 'jp') ? 'selected' : '' ?>>Jepang</option>
                            <option value="ar" <?= ($book && $book['language'] == 'ar') || (!$book && $pre_language == 'ar') ? 'selected' : '' ?>>Arab</option>
                            <option value="other" <?= ($book && $book['language'] == 'other') || (!$book && $pre_language == 'other') ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                    <select name="category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($book && $book['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Subjek / Topik (Pisahkan dengan koma)</label>
                    <input type="text" name="subjects" value="<?= $book ? htmlspecialchars($book['subjects']) : htmlspecialchars($pre_subjects) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Contoh: Fiction, Japan, Romance">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Sinopsis</label>
                    <textarea name="synopsis" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?= $book ? htmlspecialchars($book['synopsis']) : '' ?></textarea>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4" x-data="{ type: '<?= $book ? $book['type'] : 'physical' ?>' }">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Cover Buku</label>
                    <?php if ($book && $book['cover_image']): ?>
                        <div class="mb-2">
                            <img src="../uploads/covers/<?= htmlspecialchars($book['cover_image']) ?>" class="h-32 object-cover rounded shadow">
                            <p class="text-xs text-gray-500 mt-1">Cover saat ini</p>
                        </div>
                    <?php elseif ($pre_cover): ?>
                        <div class="mb-2">
                            <img src="<?= htmlspecialchars($pre_cover) ?>" class="h-32 object-cover rounded shadow">
                            <p class="text-xs text-green-600 mt-1 font-semibold">Cover dari OpenLibrary akan disimpan otomatis</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="cover_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-icy_blue-100 file:text-mauve-900 hover:file:bg-icy_blue-200">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tipe Buku</label>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" class="form-radio" name="type" value="physical" x-model="type">
                            <span class="ml-2">Fisik</span>
                        </label>
                        <label class="inline-flex items-center ml-6">
                            <input type="radio" class="form-radio" name="type" value="digital" x-model="type">
                            <span class="ml-2">Digital (E-Book)</span>
                        </label>
                    </div>
                </div>

                <!-- Physical Details -->
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <h3 class="font-bold text-gray-700 mb-4">Fisik Buku</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-2">Halaman</label>
                            <input type="number" name="pages" value="<?= $book ? htmlspecialchars($book['pages']) : htmlspecialchars($pre_pages) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-2">Ukuran</label>
                            <input type="text" name="dimensions" value="<?= $book ? htmlspecialchars($book['dimensions']) : '' ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="20x13 cm">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-xs font-bold mb-2">Tipe Cover</label>
                        <select name="cover_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Pilih Tipe</option>
                            <option value="Paperback" <?= ($book && $book['cover_type'] == 'Paperback') ? 'selected' : '' ?>>Paperback</option>
                            <option value="Hardcover" <?= ($book && $book['cover_type'] == 'Hardcover') ? 'selected' : '' ?>>Hardcover</option>
                            <option value="E-Book" <?= ($book && $book['cover_type'] == 'E-Book') ? 'selected' : '' ?>>E-Book</option>
                        </select>
                    </div>
                </div>

                <div x-show="type === 'physical'">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Stok Buku</label>
                    <input type="number" name="stock" value="<?= $book ? htmlspecialchars($book['stock']) : '1' ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="0">
                </div>

                <div x-show="type === 'digital'" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Upload File PDF</label>
                    <input type="file" name="pdf_file" accept=".pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-icy_blue-100 file:text-mauve-900 hover:file:bg-icy_blue-200">
                    <p class="text-xs text-gray-500 mt-2">Format wajib .pdf. <?= $book && $book['file_path'] ? 'Biarkan kosong jika tidak ingin mengganti file.' : '' ?></p>
                    <?php if ($book && $book['file_path']): ?>
                        <div class="mt-2 text-sm text-green-600">File saat ini: <?= htmlspecialchars($book['file_path']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="border-t pt-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Cover Buku</label>
                    <?php if ($book && $book['cover_image']): ?>
                        <div class="mb-2">
                            <img src="../uploads/covers/<?= htmlspecialchars($book['cover_image']) ?>" class="h-32 object-cover rounded">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="cover_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-icy_blue-100 file:text-mauve-900 hover:file:bg-icy_blue-200">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end mt-8">
            <a href="books.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded mr-4 transition">Batal</a>
            <button type="submit" class="bg-baby_pink-600 hover:bg-baby_pink-700 text-white font-bold py-2 px-6 rounded transition">
                <?= $book ? 'Update Buku' : 'Simpan Buku' ?>
            </button>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
