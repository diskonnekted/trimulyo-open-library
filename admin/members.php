<?php
require_once 'includes/header.php';

// Pagination Setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$where = "WHERE 1=1";
$params = [];

if ($search) {
    $where .= " AND (name LIKE ? OR member_code LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Get Total Records
$stmt = $pdo->prepare("SELECT COUNT(*) FROM members $where");
$stmt->execute($params);
$total_results = $stmt->fetchColumn();
$total_pages = ceil($total_results / $limit);

// Get Members
$query = "SELECT * FROM members $where ORDER BY created_at DESC LIMIT $start, $limit";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$members = $stmt->fetchAll();

// Fetch Loan Data for these members
$active_loans = [];
$history_loans = [];

if (count($members) > 0) {
    $member_ids = array_column($members, 'id');
    $placeholders = implode(',', array_fill(0, count($member_ids), '?'));
    
    // Active Loans
    $sql_active = "SELECT l.member_id, b.title, l.due_date 
                   FROM loans l 
                   JOIN books b ON l.book_id = b.id 
                   WHERE l.member_id IN ($placeholders) AND l.status = 'borrowed'";
    $stmt_active = $pdo->prepare($sql_active);
    $stmt_active->execute($member_ids);
    $active_loans = $stmt_active->fetchAll(PDO::FETCH_GROUP); // Group by member_id

    // History Loans
    $sql_history = "SELECT l.member_id, b.title, l.return_date 
                    FROM loans l 
                    JOIN books b ON l.book_id = b.id 
                    WHERE l.member_id IN ($placeholders) AND l.status = 'returned' 
                    ORDER BY l.return_date DESC";
    $stmt_history = $pdo->prepare($sql_history);
    $stmt_history->execute($member_ids);
    $history_loans = $stmt_history->fetchAll(PDO::FETCH_GROUP);
}
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Kelola Anggota</h1>
    <a href="member_form.php" class="bg-baby_pink-600 hover:bg-baby_pink-700 text-white font-bold py-2 px-4 rounded flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Anggota
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b border-gray-200">
        <form action="" method="GET" class="flex">
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama, kode, atau email..." class="flex-grow border rounded-l px-4 py-2 focus:outline-none focus:ring-2 focus:ring-icy_blue-200 focus:border-frosted_mint-600">
            <button type="submit" class="bg-gray-100 border border-l-0 rounded-r px-4 hover:bg-gray-200">Cari</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 uppercase text-xs leading-normal">
                    <th class="py-3 px-6 text-left">Member Info</th>
                    <th class="py-3 px-6 text-left">Sedang Dipinjam</th>
                    <th class="py-3 px-6 text-center">Riwayat</th>
                    <th class="py-3 px-6 text-center">Status</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php if (count($members) > 0): ?>
                    <?php foreach ($members as $member): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">
                            <div class="flex items-center">
                                <div class="mr-3 flex-shrink-0 w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
                                    <?php if (!empty($member['photo'])): ?>
                                        <img src="../uploads/members/<?= htmlspecialchars($member['photo']) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-gray-500 font-bold">
                                            <?= strtoupper(substr($member['name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-800 block"><?= htmlspecialchars($member['name']) ?></span>
                                    <div class="text-xs text-gray-500 font-mono"><?= htmlspecialchars($member['member_code']) ?></div>
                                    <div class="text-xs text-gray-500 mt-0.5"><?= htmlspecialchars($member['email']) ?></div>
                                    <div class="text-xs text-gray-500"><?= htmlspecialchars($member['phone']) ?></div>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Active Loans -->
                        <td class="py-3 px-6 text-left align-top">
                            <?php if (isset($active_loans[$member['id']])): ?>
                                <ul class="list-disc list-inside space-y-1">
                                    <?php foreach ($active_loans[$member['id']] as $loan): ?>
                                        <li class="text-xs">
                                            <span class="font-medium text-gray-700"><?= htmlspecialchars($loan['title']) ?></span>
                                            <div class="text-[10px] text-red-500 pl-4">Tenggat: <?= date('d/m/Y', strtotime($loan['due_date'])) ?></div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <span class="text-gray-400 text-xs italic">Tidak ada pinjaman aktif</span>
                            <?php endif; ?>
                        </td>

                        <!-- History Loans (Modal) -->
                        <td class="py-3 px-6 text-center align-top" x-data="{ showHistory: false }">
                            <?php $history = $history_loans[$member['id']] ?? []; ?>
                            <?php if (count($history) > 0): ?>
                                <button @click="showHistory = true" class="bg-icy_blue-100 text-mauve-900 hover:bg-icy_blue-200 py-1 px-3 rounded-full text-xs font-bold transition">
                                    Lihat (<?= count($history) ?>)
                                </button>
                                
                                <!-- Modal -->
                                <div x-show="showHistory" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showHistory = false">
                                            <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                                        </div>
                                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="flex justify-between items-center mb-4 border-b pb-2">
                                                    <h3 class="text-lg leading-6 font-bold text-gray-900">Riwayat Peminjaman</h3>
                                                    <button @click="showHistory = false" class="text-gray-400 hover:text-gray-500">
                                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                                <div class="mt-2 max-h-80 overflow-y-auto">
                                                    <p class="text-sm text-gray-500 mb-2">Member: <span class="font-bold"><?= htmlspecialchars($member['name']) ?></span></p>
                                                    <ul class="divide-y divide-gray-200">
                                                        <?php foreach ($history as $h): ?>
                                                            <li class="py-3">
                                                                <div class="font-medium text-gray-800 text-sm"><?= htmlspecialchars($h['title']) ?></div>
                                                                <div class="text-xs text-green-600 mt-1 flex items-center">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                    Dikembalikan: <?= date('d/m/Y H:i', strtotime($h['return_date'])) ?>
                                                                </div>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="showHistory = false">
                                                    Tutup
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="text-gray-400 text-xs">-</span>
                            <?php endif; ?>
                        </td>

                        <td class="py-3 px-6 text-center align-top">
                            <?php if ($member['status'] == 'active'): ?>
                                <span class="bg-green-200 text-green-600 py-1 px-3 rounded-full text-xs">Aktif</span>
                            <?php else: ?>
                                <span class="bg-red-200 text-red-600 py-1 px-3 rounded-full text-xs">Non-Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-6 text-center align-top">
                            <div class="flex item-center justify-center space-x-2">
                                <a href="member_form.php?id=<?= $member['id'] ?>" class="text-purple-600 hover:text-purple-800" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </a>
                                <a href="member_card.php?id=<?= $member['id'] ?>" class="text-frosted_mint-700 hover:text-frosted_mint-800" title="Kartu Anggota" target="_blank">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                </a>
                                <a href="member_action.php?delete=<?= $member['id'] ?>" onclick="return confirm('Yakin ingin menghapus anggota ini?')" class="text-red-600 hover:text-red-800" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada data anggota.</td>
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
                <a href="?page=<?= $i ?>&q=<?= htmlspecialchars($search) ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 <?= $page == $i ? 'bg-icy_blue-100 text-mauve-900' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
