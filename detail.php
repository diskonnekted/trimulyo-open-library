<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT books.*, categories.name as category_name FROM books LEFT JOIN categories ON books.category_id = categories.id WHERE books.id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();

// Logic to check loan status
$loan_status = null;
$success_message = '';
$error_message = '';

if (isset($_SESSION['member_id'])) {
    $member_id = $_SESSION['member_id'];
    // Check for active or pending loans
    $stmt = $pdo->prepare("SELECT status FROM loans WHERE member_id = ? AND book_id = ? AND status IN ('borrowed', 'pending')");
    $stmt->execute([$member_id, $id]);
    $existing_loan = $stmt->fetch();
    if ($existing_loan) {
        $loan_status = $existing_loan['status'];
    }

    // Handle Borrow Request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow_book'])) {
        if (!$loan_status && $book['stock'] > 0) {
            try {
                // Insert loan request with 'pending' status
                $stmt = $pdo->prepare("INSERT INTO loans (member_id, book_id, status, loan_date, due_date) VALUES (?, ?, 'pending', NULL, NULL)");
                
                if ($stmt->execute([$member_id, $id])) {
                    $success_message = "Permintaan peminjaman berhasil diajukan. Silakan tunggu persetujuan admin.";
                    $loan_status = 'pending';
                } else {
                    $error_message = "Gagal mengajukan peminjaman.";
                }
            } catch (PDOException $e) {
                $error_message = "Terjadi kesalahan database: " . $e->getMessage();
            }
        }
    }
}

if (!$book) {
    echo "<div class='container mx-auto px-4 py-12 text-center'><h2 class='text-2xl font-bold'>Buku tidak ditemukan</h2><a href='catalog.php' class='text-baby_pink-700 mt-4 inline-block'>Kembali ke Katalog</a></div>";
    require_once 'includes/footer.php';
    exit;
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="catalog.php" class="text-gray-500 hover:text-mauve-900 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Katalog
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="md:flex">
            <!-- Cover Image -->
            <div class="md:w-1/3 bg-gray-100 p-8 flex items-start justify-center relative overflow-hidden">
                <!-- Status Ribbon -->
                <?php if ($book['type'] == 'physical' && $book['stock'] <= 0): ?>
                    <div class="absolute top-0 left-0 z-10">
                            <div class="bg-red-600 text-white text-[10px] font-bold px-6 py-1 shadow-md transform -rotate-45 -translate-x-7 translate-y-4 w-32 text-center border-2 border-white">
                                DIPINJAM
                            </div>
                    </div>
                <?php endif; ?>

                <?php if ($book['cover_image']): ?>
                    <img src="uploads/covers/<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="shadow-xl rounded max-w-full h-auto object-contain" style="max-height: 400px;">
                <?php else: ?>
                    <div class="w-64 h-80 bg-gray-200 flex items-center justify-center text-gray-400 rounded shadow-inner">
                        <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Details -->
            <div class="md:w-2/3 p-8">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="px-3 py-1 bg-icy_blue-100 text-mauve-900 text-xs font-bold rounded-full uppercase"><?= $book['category_name'] ? htmlspecialchars($book['category_name']) : 'Umum' ?></span>
                    <?php if ($book['type'] == 'digital'): ?>
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full uppercase flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Digital (PDF)
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-bold rounded-full uppercase flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            Fisik
                        </span>
                    <?php endif; ?>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($book['title']) ?></h1>
                <p class="text-xl text-gray-600 mb-6">
                    oleh <span class="font-semibold text-gray-800"><?= htmlspecialchars($book['author']) ?></span>
                    <?php if (!empty($book['translator'])): ?>
                        <br><span class="text-sm text-gray-500">Penerjemah: <?= htmlspecialchars($book['translator']) ?></span>
                    <?php endif; ?>
                </p>

                <div class="bg-gray-50 rounded-lg p-6 mb-8 text-sm border border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                        <div>
                            <span class="block text-gray-500 text-xs uppercase tracking-wider mb-1">ISBN</span>
                            <span class="font-medium text-gray-900"><?= htmlspecialchars($book['isbn'] ?? '-') ?></span>
                        </div>
                        <div>
                            <span class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Penerbit</span>
                            <span class="font-medium text-gray-900">
                                <?= htmlspecialchars($book['publisher'] ?? '-') ?>
                                <?php if (!empty($book['publish_location'])): ?>
                                    , <?= htmlspecialchars($book['publish_location']) ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div>
                            <span class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Tahun Terbit</span>
                            <span class="font-medium text-gray-900"><?= htmlspecialchars($book['year'] ?? '-') ?></span>
                        </div>
                        <div>
                            <span class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Bahasa</span>
                            <span class="font-medium text-gray-900">
                                <?php
                                    $langs = ['id' => 'Indonesia', 'en' => 'Inggris', 'jp' => 'Jepang', 'ar' => 'Arab', 'other' => 'Lainnya'];
                                    echo isset($langs[$book['language']]) ? $langs[$book['language']] : $book['language'];
                                ?>
                            </span>
                        </div>
                        <?php if ($book['type'] == 'physical'): ?>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Fisik Buku</span>
                                <span class="font-medium text-gray-900">
                                    <?= $book['pages'] ? $book['pages'] . ' Halaman' : '-' ?>
                                    <?php if (!empty($book['dimensions'])) echo ' • ' . htmlspecialchars($book['dimensions']); ?>
                                </span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Tipe Sampul</span>
                                <span class="font-medium text-gray-900"><?= htmlspecialchars($book['cover_type'] ?? '-') ?></span>
                            </div>
                        <?php endif; ?>
                        <div>
                            <span class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Klasifikasi DDC</span>
                            <span class="font-medium text-gray-900 font-mono"><?= htmlspecialchars($book['ddc_code'] ?? '-') ?></span>
                        </div>
                        <?php if ($book['type'] == 'physical'): ?>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Stok Tersedia</span>
                                <?php if ((int)$book['stock'] > 0): ?>
                                    <span class="font-bold text-green-600"><?= (int)$book['stock'] ?> Buku</span>
                                <?php else: ?>
                                    <span class="font-bold text-red-600">Sedang Dipinjam (Stok Habis)</span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Lokasi Rak</span>
                                <span class="font-medium text-gray-900">R-<?= substr(md5($book['id']), 0, 3) ?></span> <!-- Dummy rak -->
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($book['subjects'])): ?>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <span class="block text-gray-500 text-xs uppercase tracking-wider mb-2">Subjek / Topik</span>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach (explode(',', $book['subjects']) as $subject): ?>
                                    <span class="inline-block bg-white border border-gray-300 rounded-full px-3 py-1 text-xs text-gray-600">
                                        <?= htmlspecialchars(trim($subject)) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-8">
                    <h3 class="font-bold text-lg mb-2">Sinopsis</h3>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                        <?= nl2br(htmlspecialchars($book['synopsis'] ?? 'Tidak ada sinopsis tersedia.')) ?>
                    </p>
                </div>

                <!-- Messages -->
                <?php if ($success_message): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                        <?= $success_message ?>
                    </div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                        <?= $error_message ?>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <?php if ($book['type'] == 'physical'): ?>
                    <div class="mt-6 border-t border-gray-100 pt-6">
                        <?php if (!isset($_SESSION['member_id'])): ?>
                            <div class="bg-icy_blue-100 text-mauve-900 p-4 rounded-lg text-center">
                                <p>Silakan <a href="login.php" class="font-bold underline hover:text-frosted_mint-800">Login</a> untuk meminjam buku ini.</p>
                            </div>
                        <?php elseif ($loan_status == 'borrowed'): ?>
                            <div class="bg-green-100 text-green-800 p-4 rounded-lg flex items-center shadow-sm">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="font-medium">Anda sedang meminjam buku ini.</span>
                            </div>
                        <?php elseif ($loan_status == 'pending'): ?>
                            <div class="bg-yellow-100 text-yellow-800 p-4 rounded-lg flex items-center shadow-sm">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="font-medium">Permintaan peminjaman sedang menunggu persetujuan admin.</span>
                            </div>
                        <?php elseif ((int)$book['stock'] > 0): ?>
                            <form method="POST" action="">
                                <button type="submit" name="borrow_book" class="bg-mauve-800 hover:bg-mauve-900 text-white font-bold py-3 px-8 rounded shadow-lg flex items-center transition transform hover:-translate-y-0.5" onclick="return confirm('Apakah Anda yakin ingin mengajukan peminjaman untuk buku ini?')">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Ajukan Peminjaman
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="bg-red-50 text-red-800 p-4 rounded-lg shadow-sm">
                                <span class="font-bold">Stok Habis.</span> Buku ini sedang tidak tersedia untuk dipinjam.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($book['type'] == 'digital' && $book['file_path']): ?>
                    <div class="flex flex-wrap gap-4">
                        <a href="read.php?id=<?= $book['id'] ?>" class="bg-baby_pink-500 hover:bg-baby_pink-600 text-white font-bold py-3 px-8 rounded shadow-lg flex items-center transition inline-flex">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            <span>Buka PDF Full Satu Halaman</span>
                        </a>
                        <a href="uploads/files/<?= htmlspecialchars($book['file_path']) ?>" target="_blank" class="bg-icy_blue-500 hover:bg-icy_blue-600 text-white font-bold py-3 px-8 rounded shadow-lg flex items-center transition inline-flex">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <span>Baca Online</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
