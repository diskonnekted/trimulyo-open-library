<?php
require_once 'includes/header.php';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search/Filter
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$where = "WHERE 1=1";
$params = [];

if ($search) {
    $where .= " AND (members.name LIKE ? OR members.member_code LIKE ? OR books.title LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status_filter) {
    $where .= " AND loans.status = ?";
    $params[] = $status_filter;
}

// Get Total Records
$count_query = "SELECT COUNT(*) FROM loans 
                JOIN members ON loans.member_id = members.id 
                JOIN books ON loans.book_id = books.id 
                $where";
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_results = $stmt->fetchColumn();
$total_pages = ceil($total_results / $limit);

// Get Loans
$query = "SELECT loans.*, members.name as member_name, members.member_code, books.title as book_title 
          FROM loans 
          JOIN members ON loans.member_id = members.id 
          JOIN books ON loans.book_id = books.id 
          $where 
          ORDER BY loans.created_at DESC 
          LIMIT $start, $limit";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$loans = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Sirkulasi Peminjaman</h1>
    <a href="loan_form.php" class="bg-baby_pink-600 hover:bg-baby_pink-700 text-white font-bold py-2 px-4 rounded flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Peminjaman Baru
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b border-gray-200 flex flex-col md:flex-row gap-4">
        <form action="" method="GET" class="flex flex-grow gap-2">
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari member atau buku..." class="flex-grow border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-icy_blue-200 focus:border-frosted_mint-600">
            <select name="status" class="border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-icy_blue-200 focus:border-frosted_mint-600">
                <option value="">Semua Status</option>
                <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Menunggu Persetujuan</option>
                <option value="borrowed" <?= $status_filter == 'borrowed' ? 'selected' : '' ?>>Dipinjam</option>
                <option value="returned" <?= $status_filter == 'returned' ? 'selected' : '' ?>>Dikembalikan</option>
                <option value="overdue" <?= $status_filter == 'overdue' ? 'selected' : '' ?>>Terlambat</option>
            </select>
            <button type="submit" class="bg-gray-100 border rounded px-4 hover:bg-gray-200">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 uppercase text-xs leading-normal">
                    <th class="py-3 px-6 text-left">Anggota</th>
                    <th class="py-3 px-6 text-left">Buku</th>
                    <th class="py-3 px-6 text-center">Tgl Pinjam</th>
                    <th class="py-3 px-6 text-center">Tenggat</th>
                    <th class="py-3 px-6 text-center">Status</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php if (count($loans) > 0): ?>
                    <?php foreach ($loans as $loan): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">
                            <span class="font-medium"><?= htmlspecialchars($loan['member_name']) ?></span>
                            <br>
                            <span class="text-xs text-gray-500"><?= htmlspecialchars($loan['member_code']) ?></span>
                        </td>
                        <td class="py-3 px-6 text-left">
                            <span class="font-medium"><?= htmlspecialchars($loan['book_title']) ?></span>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <?= $loan['loan_date'] ? date('d/m/Y', strtotime($loan['loan_date'])) : '-' ?>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <?= $loan['due_date'] ? date('d/m/Y', strtotime($loan['due_date'])) : '-' ?>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <?php if ($loan['status'] == 'borrowed'): ?>
                                <?php if (strtotime($loan['due_date']) < time()): ?>
                                    <span class="bg-red-100 text-red-600 py-1 px-3 rounded-full text-xs">Terlambat</span>
                                <?php else: ?>
                                    <span class="bg-yellow-100 text-yellow-600 py-1 px-3 rounded-full text-xs">Dipinjam</span>
                                <?php endif; ?>
                            <?php elseif ($loan['status'] == 'returned'): ?>
                                <span class="bg-green-100 text-green-600 py-1 px-3 rounded-full text-xs">Dikembalikan</span>
                            <?php elseif ($loan['status'] == 'pending'): ?>
                                <span class="bg-orange-100 text-orange-600 py-1 px-3 rounded-full text-xs">Menunggu</span>
                            <?php elseif ($loan['status'] == 'rejected'): ?>
                                <span class="bg-red-100 text-red-600 py-1 px-3 rounded-full text-xs">Ditolak</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center">
                                <?php if ($loan['status'] == 'borrowed'): ?>
                                    <a href="loan_action.php?return=<?= $loan['id'] ?>" onclick="return confirm('Proses pengembalian buku?')" class="bg-green-500 hover:bg-green-600 text-white py-1 px-3 rounded text-xs transition mr-2">
                                        Kembalikan
                                    </a>
                                <?php elseif ($loan['status'] == 'pending'): ?>
                                    <a href="loan_form.php?id=<?= $loan['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded text-xs transition mr-2">
                                        Setujui
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs">-</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">Tidak ada data peminjaman.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="p-4 border-t border-gray-200 flex justify-center">
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&q=<?= htmlspecialchars($search) ?>&status=<?= htmlspecialchars($status_filter) ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 <?= $page == $i ? 'bg-icy_blue-100 text-mauve-900' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
