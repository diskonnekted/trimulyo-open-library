<?php
require_once 'includes/header.php';

$message = '';
$editCategory = null;

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM news_categories WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = "Kategori berita berhasil dihapus.";
        } else {
            $message = "Gagal menghapus kategori.";
        }
    } catch (PDOException $e) {
        $message = "Gagal menghapus: Kategori mungkin sedang digunakan oleh berita.";
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("UPDATE news_categories SET name = ?, slug = ? WHERE id = ?");
        if ($stmt->execute([$name, $slug, $id])) {
            $message = "Kategori berhasil diperbarui.";
        }
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO news_categories (name, slug) VALUES (?, ?)");
        if ($stmt->execute([$name, $slug])) {
            $message = "Kategori berhasil ditambahkan.";
        }
    }
}

// Check if Edit Mode
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM news_categories WHERE id = ?");
    $stmt->execute([$id]);
    $editCategory = $stmt->fetch();
}

// Get All Categories
$categories = $pdo->query("SELECT * FROM news_categories ORDER BY name")->fetchAll();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Kelola Kategori Berita</h1>
</div>

<?php if ($message): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <?= $message ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Form Section -->
    <div class="md:col-span-1">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-lg mb-4"><?= $editCategory ? 'Edit Kategori' : 'Tambah Kategori' ?></h3>
            <form method="POST" action="news_categories.php">
                <?php if ($editCategory): ?>
                    <input type="hidden" name="id" value="<?= $editCategory['id'] ?>">
                <?php endif; ?>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori</label>
                    <input type="text" name="name" value="<?= $editCategory ? htmlspecialchars($editCategory['name']) : '' ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-baby_pink-600 hover:bg-baby_pink-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                        <?= $editCategory ? 'Update' : 'Simpan' ?>
                    </button>
                </div>
                <?php if ($editCategory): ?>
                    <div class="mt-2 text-center">
                        <a href="news_categories.php" class="text-sm text-gray-500 hover:text-gray-800">Batal Edit</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- List Section -->
    <div class="md:col-span-2">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-xs leading-normal">
                        <th class="py-3 px-6 text-left">Nama</th>
                        <th class="py-3 px-6 text-left">Slug</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <?php foreach ($categories as $cat): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">
                            <span class="font-medium"><?= htmlspecialchars($cat['name']) ?></span>
                        </td>
                        <td class="py-3 px-6 text-left">
                            <?= htmlspecialchars($cat['slug']) ?>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center">
                                <a href="news_categories.php?edit=<?= $cat['id'] ?>" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                                <a href="news_categories.php?delete=<?= $cat['id'] ?>" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110" onclick="return confirm('Yakin ingin menghapus?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
