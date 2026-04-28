<?php
require_once 'includes/header.php';

$message = '';

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hero_title = sanitize($_POST['hero_title']);
    $hero_subtitle = sanitize($_POST['hero_subtitle']);
    
    // Helper to update or insert setting
    function updateSetting($pdo, $key, $value) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE key_name = ?");
        $stmt->execute([$key]);
        if ($stmt->fetchColumn() > 0) {
            $pdo->prepare("UPDATE settings SET value = ? WHERE key_name = ?")->execute([$value, $key]);
        } else {
            $pdo->prepare("INSERT INTO settings (key_name, value) VALUES (?, ?)")->execute([$key, $value]);
        }
    }

    // Update Texts
    updateSetting($pdo, 'hero_title', $hero_title);
    updateSetting($pdo, 'hero_subtitle', $hero_subtitle);

    // Update Library Info
    $library_keys = ['library_name', 'library_description', 'library_address', 'library_phone', 'library_email', 'library_permit', 'library_head', 'logo_display_mode'];
    foreach ($library_keys as $key) {
        if (isset($_POST[$key])) {
            $val = sanitize($_POST[$key]);
            updateSetting($pdo, $key, $val);
        }
    }

    // Handle Hero Image Upload
    if (!empty($_FILES['hero_image']['name'])) {
        $upload = uploadFile($_FILES['hero_image'], '../uploads/hero', ['jpg', 'jpeg', 'png', 'webp']);
        if (isset($upload['success'])) {
            $new_image = $upload['path'];
            // Delete old image
            $old_image = getSetting($pdo, 'hero_image');
            if ($old_image && file_exists("../uploads/hero/$old_image")) {
                unlink("../uploads/hero/$old_image");
            }
            // Save new image path
            updateSetting($pdo, 'hero_image', $new_image);
        } else {
            $message = "Error upload gambar: " . $upload['error'];
        }
    }

    // Handle Library Logo Upload
    if (!empty($_FILES['library_logo']['name'])) {
        $upload = uploadFile($_FILES['library_logo'], '../uploads/logo', ['png', 'jpg', 'jpeg', 'webp']);
        if (isset($upload['success'])) {
            $new_logo = $upload['path'];
            // Delete old logo
            $old_logo = getSetting($pdo, 'library_logo');
            if ($old_logo && file_exists("../uploads/logo/$old_logo")) {
                unlink("../uploads/logo/$old_logo");
            }
            // Save new logo path
            updateSetting($pdo, 'library_logo', $new_logo);
        } else {
            $message = "Error upload logo: " . $upload['error'];
        }
    }

    if (!$message) {
        $message = "Pengaturan berhasil disimpan!";
    }
}

// Get Current Settings
$hero_title = getSetting($pdo, 'hero_title');
$hero_subtitle = getSetting($pdo, 'hero_subtitle');
$hero_image = getSetting($pdo, 'hero_image');

// Get Library Info Settings
$lib_name = getSetting($pdo, 'library_name');
$lib_logo = getSetting($pdo, 'library_logo');
$lib_display_mode = getSetting($pdo, 'logo_display_mode') ?: 'logo_text';
$lib_desc = getSetting($pdo, 'library_description');
$lib_addr = getSetting($pdo, 'library_address');
$lib_phone = getSetting($pdo, 'library_phone');
$lib_email = getSetting($pdo, 'library_email');
$lib_permit = getSetting($pdo, 'library_permit');
$lib_head = getSetting($pdo, 'library_head');
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Pengaturan Tampilan</h1>
</div>

<?php if ($message): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <?= $message ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow p-8 max-w-2xl">
    <form method="POST" enctype="multipart/form-data">
        <h3 class="text-xl font-bold mb-4 border-b pb-2">Halaman Depan (Landing Page)</h3>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Judul Utama (Hero Title)</label>
            <input type="text" name="hero_title" value="<?= htmlspecialchars($hero_title) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Sub Judul (Subtitle)</label>
            <textarea name="hero_subtitle" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required><?= htmlspecialchars($hero_subtitle) ?></textarea>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Banner Utama</label>
            <?php if ($hero_image): ?>
                <div class="mb-2 relative w-full h-48 rounded overflow-hidden">
                    <img src="../uploads/hero/<?= htmlspecialchars($hero_image) ?>" class="w-full h-full object-cover">
                    <div class="absolute bottom-0 left-0 bg-black bg-opacity-50 text-white text-xs p-1 w-full">Gambar Saat Ini</div>
                </div>
            <?php endif; ?>
            <input type="file" name="hero_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-icy_blue-100 file:text-mauve-900 hover:file:bg-icy_blue-200">
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, WEBP. Disarankan ukuran 1920x600px.</p>
        </div>

        <div class="mt-8 border-t pt-8">
            <h3 class="text-xl font-bold mb-4 border-b pb-2">Informasi Perpustakaan (Halaman Info)</h3>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Logo Perpustakaan (Format PNG Transparan disarankan)</label>
                <?php if ($lib_logo): ?>
                    <div class="mb-2 relative w-32 h-32 bg-gray-200 rounded p-2">
                        <img src="../uploads/logo/<?= htmlspecialchars($lib_logo) ?>" class="w-full h-full object-contain">
                        <div class="absolute bottom-0 left-0 bg-black bg-opacity-50 text-white text-xs p-1 w-full text-center">Logo Saat Ini</div>
                    </div>
                <?php endif; ?>
                <input type="file" name="library_logo" accept="image/png,image/jpeg,image/webp" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-icy_blue-100 file:text-mauve-900 hover:file:bg-icy_blue-200">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Mode Tampilan Header</label>
                <div class="flex flex-col space-y-2">
                    <label class="inline-flex items-center">
                        <input type="radio" class="form-radio" name="logo_display_mode" value="logo_only" <?= $lib_display_mode == 'logo_only' ? 'checked' : '' ?>>
                        <span class="ml-2">Hanya Logo</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" class="form-radio" name="logo_display_mode" value="logo_text" <?= $lib_display_mode == 'logo_text' ? 'checked' : '' ?>>
                        <span class="ml-2">Logo dan Tulisan</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" class="form-radio" name="logo_display_mode" value="text_only" <?= $lib_display_mode == 'text_only' ? 'checked' : '' ?>>
                        <span class="ml-2">Hanya Tulisan</span>
                    </label>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Perpustakaan</label>
                <input type="text" name="library_name" value="<?= htmlspecialchars($lib_name) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi Singkat</label>
                <textarea name="library_description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?= htmlspecialchars($lib_desc) ?></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Alamat</label>
                <input type="text" name="library_address" value="<?= htmlspecialchars($lib_addr) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Telepon/WA</label>
                <input type="text" name="library_phone" value="<?= htmlspecialchars($lib_phone) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="library_email" value="<?= htmlspecialchars($lib_email) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Izin/NPP</label>
                <input type="text" name="library_permit" value="<?= htmlspecialchars($lib_permit) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Kepala Perpustakaan</label>
                <input type="text" name="library_head" value="<?= htmlspecialchars($lib_head) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="bg-baby_pink-600 hover:bg-baby_pink-700 text-white font-bold py-2 px-6 rounded transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
