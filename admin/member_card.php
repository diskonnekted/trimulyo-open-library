<?php
require_once '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) die("Member ID required");

$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$id]);
$member = $stmt->fetch();

if (!$member) die("Member not found");

// Settings for Library Name
$stmt = $pdo->prepare("SELECT value FROM settings WHERE key_name = ?");
$stmt->execute(['library_name']);
$app_name = $stmt->fetchColumn() ?: 'Perpustakaan Digital';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota - <?= htmlspecialchars($member['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen p-4">

    <div id="card" class="bg-white rounded-xl shadow-2xl overflow-hidden w-[600px] h-[350px] relative flex border-2 border-mauve-900" style="background-image: url('<?= BASE_URL ?>uploads/background.jpg'); background-size: cover; background-position: center;">
        <!-- Left Side: Design & Info -->
        <div class="w-2/3 p-8 relative z-10">
            <div class="absolute inset-0 bg-gradient-to-br from-mauve-100 to-white opacity-50 z-[-1]"></div>
            
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-mauve-900 rounded-full flex items-center justify-center text-white font-bold text-xl mr-4">
                    P
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-mauve-900 leading-none"><?= htmlspecialchars($app_name) ?></h1>
                    <p class="text-sm text-mauve-700">Kartu Anggota Resmi</p>
                </div>
            </div>

            <div class="space-y-4 mt-8">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Nama Anggota</p>
                    <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($member['name']) ?></h2>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Nomor Anggota</p>
                    <h3 class="text-xl font-mono text-mauve-800"><?= htmlspecialchars($member['member_code']) ?></h3>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Bergabung Sejak</p>
                    <p class="text-md text-gray-700"><?= date('d F Y', strtotime($member['created_at'])) ?></p>
                </div>
            </div>
        </div>

        <!-- Right Side: QR Code & Photo -->
        <div class="w-1/3 bg-mauve-900 p-6 flex flex-col items-center justify-center text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full transform translate-x-10 -translate-y-10"></div>
            
            <!-- Member Photo -->
            <div class="w-24 h-24 rounded-full bg-white p-1 mb-4 shadow-lg overflow-hidden flex-shrink-0 z-10">
                <?php if (!empty($member['photo'])): ?>
                    <img src="../uploads/members/<?= htmlspecialchars($member['photo']) ?>" class="w-full h-full object-cover rounded-full">
                <?php else: ?>
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400 rounded-full">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-white p-2 rounded-lg mb-4 shadow-lg z-10">
                <div id="qrcode"></div>
            </div>

            <div class="text-center z-10">
                <p class="text-xs opacity-75 mb-1">Scan untuk detail</p>
                <p class="text-[10px] opacity-50">Berlaku di seluruh cabang</p>
            </div>
        </div>
    </div>

    <div class="mt-8 space-x-4 no-print">
        <button onclick="window.print()" class="bg-baby_pink-600 hover:bg-baby_pink-700 text-white font-bold py-2 px-6 rounded shadow transition">
            Cetak Kartu
        </button>
        <button onclick="window.close()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded shadow transition">
            Tutup
        </button>
    </div>

    <script>
        // Generate QR Code
        new QRCode(document.getElementById("qrcode"), {
            text: "<?= $member['member_code'] ?>",
            width: 128,
            height: 128,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>
