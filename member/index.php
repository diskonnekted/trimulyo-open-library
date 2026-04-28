<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Cek Login
if (!isset($_SESSION['member_id'])) {
    header("Location: ../login.php");
    exit;
}

$member_id = $_SESSION['member_id'];

// Ambil Data Member
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$member_id]);
$member = $stmt->fetch();

if (!$member) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

// Ambil Peminjaman Aktif
$stmt = $pdo->prepare("
    SELECT l.*, b.title, b.cover_image 
    FROM loans l 
    JOIN books b ON l.book_id = b.id 
    WHERE l.member_id = ? AND l.status = 'borrowed' 
    ORDER BY l.due_date ASC
");
$stmt->execute([$member_id]);
$active_loans = $stmt->fetchAll();

// Ambil Riwayat Peminjaman
$stmt = $pdo->prepare("
    SELECT l.*, b.title, b.cover_image 
    FROM loans l 
    JOIN books b ON l.book_id = b.id 
    WHERE l.member_id = ? AND l.status = 'returned' 
    ORDER BY l.return_date DESC 
    LIMIT 10
");
$stmt->execute([$member_id]);
$history_loans = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-mauve-800 to-mauve-600 rounded-2xl shadow-xl p-8 mb-8 text-white relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">Halo, <?= htmlspecialchars($member['name']) ?>!</h1>
                <p class="text-mauve-100 text-lg">Selamat datang di dashboard anggota.</p>
                <div class="mt-4 inline-block bg-white/20 backdrop-blur-md px-4 py-2 rounded-lg border border-white/30">
                    <span class="font-mono text-sm">ID Anggota: <?= htmlspecialchars($member['member_code']) ?></span>
                </div>
            </div>
            <div class="mt-6 md:mt-0 flex gap-3">
                <a href="../catalog.php" class="bg-white text-mauve-900 font-bold py-2 px-6 rounded-full shadow hover:bg-gray-100 transition">
                    Cari Buku
                </a>
                <a href="../logout.php" class="bg-red-500/80 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-full shadow transition border border-red-400">
                    Keluar
                </a>
            </div>
        </div>
        <!-- Decorative Circle -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Active Loans -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Active Loans -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="bg-icy_blue-100 px-6 py-4 border-b border-icy_blue-200 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-frosted_mint-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Sedang Dipinjam
                    </h2>
                    <span class="bg-icy_blue-200 text-mauve-900 text-xs font-bold px-3 py-1 rounded-full"><?= count($active_loans) ?> Buku</span>
                </div>
                
                <?php if (count($active_loans) > 0): ?>
                    <div class="divide-y divide-gray-100">
                        <?php foreach ($active_loans as $loan): ?>
                            <?php 
                                $due_date = new DateTime($loan['due_date']);
                                $today = new DateTime();
                                $interval = $today->diff($due_date);
                                $is_overdue = $today > $due_date;
                                $days_left = $interval->days;
                            ?>
                            <div class="p-6 flex flex-col md:flex-row gap-4 hover:bg-gray-50 transition">
                                <div class="w-full md:w-24 flex-shrink-0">
                                    <?php if ($loan['cover_image']): ?>
                                        <img src="../uploads/covers/<?= htmlspecialchars($loan['cover_image']) ?>" class="w-full h-32 object-cover rounded shadow-sm">
                                    <?php else: ?>
                                        <div class="w-full h-32 bg-gray-200 flex items-center justify-center rounded text-gray-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow">
                                    <h3 class="text-lg font-bold text-gray-900 mb-1"><?= htmlspecialchars($loan['title']) ?></h3>
                                    <div class="grid grid-cols-2 gap-4 text-sm mt-3">
                                        <div>
                                            <span class="block text-gray-500 text-xs uppercase tracking-wider">Tanggal Pinjam</span>
                                            <span class="font-medium"><?= date('d M Y', strtotime($loan['loan_date'])) ?></span>
                                        </div>
                                        <div>
                                            <span class="block text-gray-500 text-xs uppercase tracking-wider">Jatuh Tempo</span>
                                            <span class="<?= $is_overdue ? 'text-red-600 font-bold' : 'font-medium' ?>">
                                                <?= date('d M Y', strtotime($loan['due_date'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <?php if ($is_overdue): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Terlambat <?= $days_left ?> Hari
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Sisa <?= $days_left ?> Hari
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <p class="mb-4">Tidak ada buku yang sedang dipinjam.</p>
                        <a href="../catalog.php" class="text-frosted_mint-700 hover:text-frosted_mint-800 font-semibold">Mulai meminjam buku &rarr;</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- History -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Riwayat Peminjaman Terakhir</h2>
                </div>
                <?php if (count($history_loans) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600 uppercase text-xs leading-normal">
                                    <th class="py-3 px-6">Buku</th>
                                    <th class="py-3 px-6">Tgl Pinjam</th>
                                    <th class="py-3 px-6">Tgl Kembali</th>
                                    <th class="py-3 px-6">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                <?php foreach ($history_loans as $loan): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-6 font-medium text-gray-800">
                                            <?= htmlspecialchars($loan['title']) ?>
                                        </td>
                                        <td class="py-3 px-6">
                                            <?= date('d/m/Y', strtotime($loan['loan_date'])) ?>
                                        </td>
                                        <td class="py-3 px-6">
                                            <?= date('d/m/Y', strtotime($loan['return_date'])) ?>
                                        </td>
                                        <td class="py-3 px-6">
                                            <span class="bg-green-200 text-green-600 py-1 px-3 rounded-full text-xs">Dikembalikan</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-6 text-center text-gray-500">
                        Belum ada riwayat peminjaman.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Profile & Card -->
        <div class="space-y-8">
            <!-- Profile Card -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-mauve-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Profil Saya
                </h2>
                
                <div class="flex flex-col items-center mb-6">
                    <div class="w-32 h-32 rounded-full bg-gray-200 overflow-hidden border-4 border-mauve-100 shadow-inner mb-4">
                        <?php if (!empty($member['photo'])): ?>
                            <img src="../uploads/members/<?= htmlspecialchars($member['photo']) ?>" alt="Foto Profil" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <p class="font-medium text-gray-900"><?= htmlspecialchars($member['name']) ?></p>
                    </div>
                    <div>
                        <label class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Kode Anggota</label>
                        <p class="font-mono font-bold text-mauve-600"><?= htmlspecialchars($member['member_code']) ?></p>
                    </div>
                    <div>
                        <label class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Email</label>
                        <p class="font-medium text-gray-900"><?= htmlspecialchars($member['email']) ?></p>
                    </div>
                    <div>
                        <label class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Nomor Telepon</label>
                        <p class="font-medium text-gray-900"><?= htmlspecialchars($member['phone']) ?></p>
                    </div>
                    <?php if (!empty($member['job'])): ?>
                    <div>
                        <label class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Pekerjaan</label>
                        <p class="font-medium text-gray-900"><?= htmlspecialchars($member['job']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($member['hobby'])): ?>
                    <div>
                        <label class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Hobi</label>
                        <p class="font-medium text-gray-900"><?= htmlspecialchars($member['hobby']) ?></p>
                    </div>
                    <?php endif; ?>
                    <div>
                        <label class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Alamat</label>
                        <p class="font-medium text-gray-900"><?= nl2br(htmlspecialchars($member['address'])) ?></p>
                    </div>
                    <?php if (!empty($member['bio'])): ?>
                    <div>
                        <label class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Bio</label>
                        <p class="font-medium text-gray-900 text-sm italic">"<?= nl2br(htmlspecialchars($member['bio'])) ?>"</p>
                    </div>
                    <?php endif; ?>
                    <div>
                        <label class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Bergabung Sejak</label>
                        <p class="font-medium text-gray-900"><?= date('d F Y', strtotime($member['created_at'])) ?></p>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100">
                    <a href="edit_profile.php" class="w-full bg-mauve-100 text-mauve-800 font-bold py-2 px-4 rounded-lg hover:bg-mauve-200 transition text-sm flex justify-center items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Edit Profil
                    </a>
                </div>
            </div>

            <!-- Digital Card Preview -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
            <div id="card" class="bg-white rounded-xl shadow-2xl overflow-hidden w-full aspect-[1.7/1] relative flex border-2 border-mauve-900" style="background-image: url('../uploads/background.jpg'); background-size: cover; background-position: center;">
                <!-- Left Side: Design & Info -->
                <div class="w-2/3 p-4 relative z-10 flex flex-col justify-between">
                    <div class="absolute inset-0 bg-gradient-to-br from-mauve-100 to-white opacity-50 z-[-1]"></div>
                    
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-mauve-900 rounded-full flex items-center justify-center text-white font-bold text-sm mr-2 flex-shrink-0">
                            P
                        </div>
                        <div>
                            <h1 class="text-sm font-bold text-mauve-900 leading-none"><?= htmlspecialchars($site_name) ?></h1>
                            <p class="text-[10px] text-mauve-700">Kartu Anggota Resmi</p>
                        </div>
                    </div>

                    <div class="space-y-2 mt-2">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider">Nama Anggota</p>
                            <h2 class="text-sm font-bold text-gray-800 line-clamp-1"><?= htmlspecialchars($member['name']) ?></h2>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider">Nomor Anggota</p>
                            <h3 class="text-xs font-mono text-mauve-800"><?= htmlspecialchars($member['member_code']) ?></h3>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider">Bergabung Sejak</p>
                            <p class="text-[10px] text-gray-700"><?= date('d F Y', strtotime($member['created_at'])) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Right Side: QR Code & Photo -->
                <div class="w-1/3 bg-mauve-900 p-2 flex flex-col items-center justify-center text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-white opacity-10 rounded-full transform translate-x-5 -translate-y-5"></div>
                    
                    <!-- Member Photo -->
                    <div class="w-16 h-16 rounded-full bg-white p-0.5 mb-3 shadow-lg overflow-hidden flex-shrink-0">
                        <?php if (!empty($member['photo'])): ?>
                            <img src="../uploads/members/<?= htmlspecialchars($member['photo']) ?>" class="w-full h-full object-cover rounded-full">
                        <?php else: ?>
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400 rounded-full">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="bg-white p-1 rounded mb-2 shadow-lg">
                        <div id="qrcode-dashboard"></div>
                    </div>

                    <div class="text-center">
                        <p class="text-[8px] opacity-75">Scan untuk detail</p>
                    </div>
                </div>
            </div>
            
            <script>
                // Generate QR Code
                // Check if QRCode is loaded, if not wait
                window.onload = function() {
                     if (typeof QRCode !== 'undefined') {
                        new QRCode(document.getElementById("qrcode-dashboard"), {
                            text: "<?= $member['member_code'] ?>",
                            width: 80,
                            height: 80,
                            colorDark : "#000000",
                            colorLight : "#ffffff",
                            correctLevel : QRCode.CorrectLevel.H
                        });
                     }
                };
            </script>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
