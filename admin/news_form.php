<?php
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$news = null;
$tags_string = '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $news = $stmt->fetch();

    if (!$news) {
        redirect('news.php');
    }

    // Get Tags
    $stmt = $pdo->prepare("SELECT t.name FROM news_tags t JOIN news_tag_map m ON t.id = m.tag_id WHERE m.news_id = ?");
    $stmt->execute([$id]);
    $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $tags_string = implode(', ', $tags);

    // Get Related Books
    $stmt = $pdo->prepare("SELECT b.id, b.title, b.cover_image FROM books b JOIN news_book_map m ON b.id = m.book_id WHERE m.news_id = ?");
    $stmt->execute([$id]);
    $related_books = $stmt->fetchAll();
} else {
    $related_books = [];
}

// Get Categories
$categories = $pdo->query("SELECT * FROM news_categories ORDER BY name")->fetchAll();
?>

<div class="mb-6">
    <div class="flex items-center text-sm text-gray-500 mb-2">
        <a href="news.php" class="hover:text-mauve-900">Berita</a>
        <svg class="h-4 w-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span><?= $news ? 'Edit Berita' : 'Tambah Berita' ?></span>
    </div>
    <h1 class="text-3xl font-bold text-gray-800"><?= $news ? 'Edit Berita' : 'Tambah Berita' ?></h1>
</div>

<form action="news_action.php" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-lg overflow-hidden">
    <?php if ($news): ?>
        <input type="hidden" name="id" value="<?= $news['id'] ?>">
    <?php endif; ?>

    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="md:col-span-2 space-y-6">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Judul Berita</label>
                <input type="text" name="title" value="<?= $news ? htmlspecialchars($news['title']) : '' ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Konten</label>
                <textarea name="content" rows="15" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required><?= $news ? htmlspecialchars($news['content']) : '' ?></textarea>
                <p class="text-xs text-gray-500 mt-1">Gunakan enter untuk paragraf baru.</p>
            </div>
        </div>

        <!-- Sidebar Options -->
        <div class="md:col-span-1 space-y-6">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                <select name="status" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="published" <?= ($news && $news['status'] == 'published') ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= ($news && $news['status'] == 'draft') ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                <select name="category_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($news && $news['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Tags</label>
                <input type="text" name="tags" value="<?= htmlspecialchars($tags_string) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Contoh: pendidikan, literasi, event">
                <p class="text-xs text-gray-500 mt-1">Pisahkan dengan koma.</p>
            </div>

            <div x-data="{
                query: '',
                searchResults: [],
                selectedBooks: <?= htmlspecialchars(json_encode($related_books)) ?>,
                searchBooks() {
                    if (this.query.length < 2) {
                        this.searchResults = [];
                        return;
                    }
                    fetch('api_get_books.php?q=' + this.query)
                        .then(res => res.json())
                        .then(data => {
                            this.searchResults = data;
                        });
                },
                addBook(book) {
                    if (!this.selectedBooks.some(b => b.id === book.id)) {
                        this.selectedBooks.push(book);
                    }
                    this.query = '';
                    this.searchResults = [];
                },
                removeBook(index) {
                    this.selectedBooks.splice(index, 1);
                }
            }">
                <label class="block text-gray-700 text-sm font-bold mb-2">Lampiran Buku</label>
                
                <!-- Search Input -->
                <div class="relative mb-2">
                    <input type="text" 
                           x-model="query" 
                           @input.debounce.300ms="searchBooks()" 
                           placeholder="Cari judul buku..." 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    
                    <!-- Search Results Dropdown -->
                    <div x-show="searchResults.length > 0" class="absolute z-10 bg-white shadow-lg rounded mt-1 w-full max-h-40 overflow-y-auto border">
                        <template x-for="book in searchResults" :key="book.id">
                            <div @click="addBook(book)" class="p-2 hover:bg-icy_blue-100 cursor-pointer border-b flex items-center">
                                <img :src="book.cover_image ? '../uploads/covers/' + book.cover_image : '../assets/no-cover.png'" class="w-8 h-10 object-cover mr-2">
                                <div>
                                    <div class="font-bold text-xs" x-text="book.title"></div>
                                    <div class="text-xs text-gray-500" x-text="book.author"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Selected Books List -->
                <div class="space-y-2">
                    <template x-for="(book, index) in selectedBooks" :key="book.id">
                        <div class="flex items-center justify-between bg-icy_blue-100 p-2 rounded border border-icy_blue-200">
                            <div class="flex items-center">
                                <input type="hidden" name="related_books[]" :value="book.id">
                                <img :src="book.cover_image ? '../uploads/covers/' + book.cover_image : '../assets/no-cover.png'" class="w-8 h-10 object-cover mr-2">
                                <div class="text-xs">
                                    <div class="font-bold text-mauve-900" x-text="book.title"></div>
                                </div>
                            </div>
                            <button type="button" @click="removeBook(index)" class="text-red-500 hover:text-red-700">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
                <p class="text-xs text-gray-500 mt-1">Cari dan pilih buku yang berkaitan.</p>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Utama</label>
                <?php if ($news && $news['image']): ?>
                    <div class="mb-2">
                        <img src="../uploads/news/<?= htmlspecialchars($news['image']) ?>" class="w-full h-auto rounded shadow">
                    </div>
                <?php endif; ?>
                <input type="file" name="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-icy_blue-100 file:text-mauve-900 hover:file:bg-icy_blue-200">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Max 2MB.</p>
            </div>
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
        <a href="news.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded mr-2">Batal</a>
        <button type="submit" class="bg-baby_pink-600 hover:bg-baby_pink-700 text-white font-bold py-2 px-4 rounded">
            <?= $news ? 'Simpan Perubahan' : 'Terbitkan Berita' ?>
        </button>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>
