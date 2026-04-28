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
$success_message = '';
$error_message = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $hobby = trim($_POST['hobby']);
    $job = trim($_POST['job']);
    $bio = trim($_POST['bio']);

    // Basic Validation
    if (empty($name) || empty($email)) {
        $error_message = 'Nama dan Email wajib diisi.';
    } else {
        try {
            // Check if email is taken by another member
            $stmt = $pdo->prepare("SELECT id FROM members WHERE email = ? AND id != ?");
            $stmt->execute([$email, $member_id]);
            if ($stmt->rowCount() > 0) {
                $error_message = 'Email sudah digunakan oleh anggota lain.';
            } else {
                // Update Member Data
                $stmt = $pdo->prepare("
                    UPDATE members 
                    SET name = ?, email = ?, phone = ?, address = ?, hobby = ?, job = ?, bio = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$name, $email, $phone, $address, $hobby, $job, $bio, $member_id]);
                
                // Handle Photo Upload
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['photo']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = uniqid('member_') . '.' . $ext;
                        $upload_dir = '../uploads/members/';
                        
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $new_filename)) {
                            // Delete old photo if exists
                            $stmt = $pdo->prepare("SELECT photo FROM members WHERE id = ?");
                            $stmt->execute([$member_id]);
                            $old_photo = $stmt->fetchColumn();
                            if ($old_photo && file_exists($upload_dir . $old_photo)) {
                                unlink($upload_dir . $old_photo);
                            }

                            // Update photo in database
                            $stmt = $pdo->prepare("UPDATE members SET photo = ? WHERE id = ?");
                            $stmt->execute([$new_filename, $member_id]);
                        } else {
                            $error_message = "Gagal mengupload foto.";
                        }
                    } else {
                        $error_message = "Format foto tidak valid. Gunakan JPG, JPEG, PNG, atau GIF.";
                    }
                }
                
                // Update Session Name if changed
                $_SESSION['member_name'] = $name;
                $_SESSION['member_email'] = $email;
                
                $success_message = 'Profil berhasil diperbarui!';
            }
        } catch (PDOException $e) {
            $error_message = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

// Ambil Data Member Terbaru
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$member_id]);
$member = $stmt->fetch();

if (!$member) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Edit Profil</h1>
                <p class="text-gray-600 mt-1">Perbarui informasi pribadi Anda.</p>
            </div>
            <a href="index.php" class="text-mauve-700 font-semibold hover:text-mauve-900 flex items-center transition">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Dashboard
            </a>
        </div>

        <?php if ($success_message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <p class="font-bold">Sukses</p>
                <p><?= $success_message ?></p>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <p class="font-bold">Error</p>
                <p><?= $error_message ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <form action="" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                
                <!-- Section: Foto Profil -->
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Foto Profil</h2>
                    <div class="flex items-center gap-6">
                        <div class="w-24 h-24 rounded-full bg-gray-200 overflow-hidden flex-shrink-0 border-2 border-gray-300">
                            <?php if (!empty($member['photo'])): ?>
                                <img src="../uploads/members/<?= htmlspecialchars($member['photo']) ?>" alt="Foto Profil" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Foto</label>
                            <input type="file" name="photo" accept="image/*" class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-mauve-50 file:text-mauve-700
                                hover:file:bg-mauve-100
                            ">
                            <p class="mt-1 text-xs text-gray-500">JPG, GIF, atau PNG. Maksimal 2MB.</p>
                        </div>
                    </div>
                </div>

                <!-- Section: Informasi Dasar -->
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Informasi Dasar</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($member['name']) ?>" class="w-full rounded-lg border-gray-300 focus:border-mauve-500 focus:ring focus:ring-mauve-200 transition" required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($member['email']) ?>" class="w-full rounded-lg border-gray-300 focus:border-mauve-500 focus:ring focus:ring-mauve-200 transition" required>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($member['phone']) ?>" class="w-full rounded-lg border-gray-300 focus:border-mauve-500 focus:ring focus:ring-mauve-200 transition">
                        </div>
                        <div>
                            <label for="job" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                            <input type="text" id="job" name="job" value="<?= htmlspecialchars($member['job'] ?? '') ?>" class="w-full rounded-lg border-gray-300 focus:border-mauve-500 focus:ring focus:ring-mauve-200 transition" placeholder="Contoh: Mahasiswa, Guru, dll">
                        </div>
                    </div>
                </div>

                <!-- Section: Informasi Tambahan -->
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Informasi Tambahan</h2>
                    <div class="space-y-6">
                        <div>
                            <label for="hobby" class="block text-sm font-medium text-gray-700 mb-1">Hobi / Minat</label>
                            <input type="text" id="hobby" name="hobby" value="<?= htmlspecialchars($member['hobby'] ?? '') ?>" class="w-full rounded-lg border-gray-300 focus:border-mauve-500 focus:ring focus:ring-mauve-200 transition" placeholder="Contoh: Membaca, Menulis, Sepak Bola">
                        </div>
                        
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                            <textarea id="address" name="address" rows="3" class="w-full rounded-lg border-gray-300 focus:border-mauve-500 focus:ring focus:ring-mauve-200 transition"><?= htmlspecialchars($member['address']) ?></textarea>
                        </div>

                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio Singkat / Catatan</label>
                            <textarea id="bio" name="bio" rows="3" class="w-full rounded-lg border-gray-300 focus:border-mauve-500 focus:ring focus:ring-mauve-200 transition" placeholder="Ceritakan sedikit tentang diri Anda..."><?= htmlspecialchars($member['bio'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end gap-4">
                    <a href="index.php" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition">Batal</a>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-mauve-800 text-white font-medium hover:bg-mauve-900 shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
