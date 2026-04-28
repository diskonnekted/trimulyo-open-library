<?php
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$loan = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM loans WHERE id = ?");
    $stmt->execute([$id]);
    $loan = $stmt->fetch();
    
    if (!$loan) {
        die("Peminjaman tidak ditemukan.");
    }
}

// Get Active Members (only needed if creating new)
if (!$loan) {
    $stmt = $pdo->query("SELECT id, name, member_code FROM members WHERE status = 'active' ORDER BY name ASC");
    $members = $stmt->fetchAll();

    // Get Available Physical Books
    $stmt = $pdo->query("SELECT id, title, stock FROM books WHERE type = 'physical' AND stock > 0 ORDER BY title ASC");
    $books = $stmt->fetchAll();
} else {
    // Get specific member and book info for display
    $stmt = $pdo->prepare("SELECT name, member_code FROM members WHERE id = ?");
    $stmt->execute([$loan['member_id']]);
    $member = $stmt->fetch();
    
    $stmt = $pdo->prepare("SELECT title, stock FROM books WHERE id = ?");
    $stmt->execute([$loan['book_id']]);
    $book = $stmt->fetch();
}
?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="loans.php" class="mr-4 text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-3xl font-bold text-gray-800"><?= $loan ? 'Setujui Peminjaman' : 'Catat Peminjaman Baru' ?></h1>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <form action="loan_action.php" method="POST" class="p-8">
            <?php if ($loan): ?>
                <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Anggota</label>
                    <?php if ($loan): ?>
                        <input type="text" value="<?= htmlspecialchars($member['name']) ?> (<?= htmlspecialchars($member['member_code']) ?>)" readonly class="w-full px-4 py-2 border rounded-lg bg-gray-100 cursor-not-allowed">
                        <input type="hidden" name="member_id" value="<?= $loan['member_id'] ?>">
                    <?php else: ?>
                        <select name="member_id" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Anggota --</option>
                            <?php foreach ($members as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?> (<?= htmlspecialchars($m['member_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Buku</label>
                    <?php if ($loan): ?>
                        <input type="text" value="<?= htmlspecialchars($book['title']) ?>" readonly class="w-full px-4 py-2 border rounded-lg bg-gray-100 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Stok saat ini: <?= $book['stock'] ?></p>
                        <input type="hidden" name="book_id" value="<?= $loan['book_id'] ?>">
                    <?php else: ?>
                        <select name="book_id" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Buku --</option>
                            <?php foreach ($books as $b): ?>
                                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['title']) ?> (Stok: <?= $b['stock'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Tanggal Pinjam</label>
                    <input type="date" name="loan_date" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Tanggal Kembali (Tenggat)</label>
                    <input type="date" name="due_date" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-3 px-8 rounded-lg shadow transition transform hover:scale-105">
                    <?= $loan ? 'Setujui & Proses' : 'Proses Peminjaman' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>