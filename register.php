<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if ($password !== $confirm_password) {
        $error_msg = "Password tidak cocok.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM members WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error_msg = "Email sudah terdaftar.";
        } else {
            // Generate Member Code
            $prefix = "MEM-" . date("Ym") . "-";
            $stmt = $pdo->query("SELECT MAX(id) as max_id FROM members");
            $row = $stmt->fetch();
            $next_id = ($row['max_id'] ?? 0) + 1;
            $member_code = $prefix . str_pad($next_id, 4, '0', STR_PAD_LEFT);

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                $stmt = $pdo->prepare("INSERT INTO members (member_code, name, email, phone, address, password) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$member_code, $name, $email, $phone, $address, $hashed_password]);
                $success_msg = "Pendaftaran berhasil! Kode Anggota Anda adalah <strong>$member_code</strong>. Silakan <a href='login.php' class='underline font-bold'>login di sini</a> atau hubungi petugas.";
            } catch (PDOException $e) {
                $error_msg = "Terjadi kesalahan: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Form Pendaftaran -->
        <div class="w-full md:w-2/3">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-3xl font-bold text-mauve-900 mb-6">Pendaftaran Anggota Baru</h2>
                
                <?php if ($success_msg): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                        <?= $success_msg ?>
                    </div>
                <?php endif; ?>

                <?php if ($error_msg): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                        <?= $error_msg ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-mauve-500 focus:ring-2 focus:ring-mauve-200 transition">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Email</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-mauve-500 focus:ring-2 focus:ring-mauve-200 transition">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Nomor Telepon</label>
                            <input type="text" name="phone" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-mauve-500 focus:ring-2 focus:ring-mauve-200 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Alamat Lengkap</label>
                        <textarea name="address" required rows="3" class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-mauve-500 focus:ring-2 focus:ring-mauve-200 transition"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Password</label>
                            <input type="password" name="password" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-mauve-500 focus:ring-2 focus:ring-mauve-200 transition">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Konfirmasi Password</label>
                            <input type="password" name="confirm_password" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-mauve-500 focus:ring-2 focus:ring-mauve-200 transition">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-mauve-900 hover:bg-mauve-800 text-white font-bold py-4 rounded-lg shadow-lg transition transform hover:scale-[1.02]">
                        Daftar Sekarang
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="w-full md:w-1/3">
            <div class="bg-mauve-100 rounded-xl shadow p-6 mb-6">
                <h3 class="text-xl font-bold text-mauve-900 mb-4">Keuntungan Membership</h3>
                <ul class="space-y-3 text-mauve-800">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-2 text-mauve-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Akses penuh ke ribuan koleksi buku fisik dan digital.
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-2 text-mauve-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Durasi peminjaman hingga 14 hari.
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-2 text-mauve-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Akses internet Wi-Fi gratis di perpustakaan.
                    </li>
                </ul>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Syarat & Ketentuan</h3>
                <div class="text-sm text-gray-600 space-y-2">
                    <p>1. Pendaftar wajib mengisi data diri dengan benar dan valid.</p>
                    <p>2. Menjaga kerahasiaan akun dan password.</p>
                    <p>3. Mematuhi segala peraturan yang berlaku di perpustakaan.</p>
                    <p>4. Mengembalikan buku tepat waktu sesuai batas peminjaman.</p>
                    <p>5. Kartu anggota digital wajib ditunjukkan saat peminjaman fisik.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>