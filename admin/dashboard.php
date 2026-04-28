<?php
require_once 'includes/header.php';

// Stats
$totalBooks = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$physicalBooks = $pdo->query("SELECT COUNT(*) FROM books WHERE type = 'physical'")->fetchColumn();
$digitalBooks = $pdo->query("SELECT COUNT(*) FROM books WHERE type = 'digital'")->fetchColumn();
$totalCats = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

// Recent Books
$stmt = $pdo->query("SELECT books.*, categories.name as category_name FROM books LEFT JOIN categories ON books.category_id = categories.id ORDER BY created_at DESC LIMIT 5");
$recentBooks = $stmt->fetchAll();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-600">Selamat datang kembali, <?= htmlspecialchars($_SESSION['admin_username']) ?></p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-frosted_mint-600">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-icy_blue-100 text-frosted_mint-700 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Buku</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalBooks ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-500 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium">Buku Fisik</p>
                <p class="text-2xl font-bold text-gray-800"><?= $physicalBooks ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium">Buku Digital</p>
                <p class="text-2xl font-bold text-gray-800"><?= $digitalBooks ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium">Kategori</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalCats ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Books Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Buku Baru Ditambahkan</h3>
        <a href="books.php" class="text-frosted_mint-700 text-sm hover:underline">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 uppercase text-xs leading-normal">
                    <th class="py-3 px-6 text-left">Judul</th>
                    <th class="py-3 px-6 text-left">Penulis</th>
                    <th class="py-3 px-6 text-center">Tipe</th>
                    <th class="py-3 px-6 text-center">Kategori</th>
                    <th class="py-3 px-6 text-center">Tanggal</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php foreach ($recentBooks as $book): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap">
                        <div class="font-medium"><?= htmlspecialchars($book['title']) ?></div>
                    </td>
                    <td class="py-3 px-6 text-left">
                        <?= htmlspecialchars($book['author']) ?>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <?php if ($book['type'] == 'digital'): ?>
                            <span class="bg-green-200 text-green-600 py-1 px-3 rounded-full text-xs">Digital</span>
                        <?php else: ?>
                            <span class="bg-icy_blue-200 text-mauve-900 py-1 px-3 rounded-full text-xs">Fisik</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <?= htmlspecialchars($book['category_name']) ?>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <?= date('d M Y', strtotime($book['created_at'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
