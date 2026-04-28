<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$member = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $member = $stmt->fetch();
}
?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="members.php" class="mr-4 text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-3xl font-bold text-gray-800"><?= $id ? 'Edit Anggota' : 'Tambah Anggota Baru' ?></h1>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <form action="member_action.php" method="POST" class="p-8">
            <input type="hidden" name="id" value="<?= $member['id'] ?? '' ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($member['name'] ?? '') ?>" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-icy_blue-200 focus:border-frosted_mint-600">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($member['email'] ?? '') ?>" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-icy_blue-200 focus:border-frosted_mint-600">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Nomor Telepon</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($member['phone'] ?? '') ?>" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-icy_blue-200 focus:border-frosted_mint-600">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-icy_blue-200 focus:border-frosted_mint-600">
                        <option value="active" <?= ($member['status'] ?? '') == 'active' ? 'selected' : '' ?>>Aktif</option>
                        <option value="inactive" <?= ($member['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Non-Aktif</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Alamat Lengkap</label>
                <textarea name="address" rows="3" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-icy_blue-200 focus:border-frosted_mint-600"><?= htmlspecialchars($member['address'] ?? '') ?></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Password <?= $id ? '<span class="text-sm font-normal text-gray-500">(Kosongkan jika tidak ingin mengubah)</span>' : '' ?></label>
                <input type="password" name="password" <?= $id ? '' : 'required' ?> class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-icy_blue-200 focus:border-frosted_mint-600">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-baby_pink-600 hover:bg-baby_pink-700 text-white font-bold py-3 px-8 rounded-lg shadow transition transform hover:scale-105">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
