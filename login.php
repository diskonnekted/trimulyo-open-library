<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['member_id'])) {
    header("Location: member/index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM members WHERE email = ?");
    $stmt->execute([$email]);
    $member = $stmt->fetch();

    if ($member && password_verify($password, $member['password'])) {
        $_SESSION['member_id'] = $member['id'];
        $_SESSION['member_name'] = $member['name'];
        $_SESSION['member_email'] = $member['email'];
        header("Location: member/index.php");
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}

require_once 'includes/header.php';
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg overflow-hidden md:max-w-lg">
        <div class="md:flex">
            <div class="w-full p-8">
                <div class="text-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-900">Login Anggota</h2>
                    <p class="text-gray-600 mt-2">Masuk untuk mengakses layanan perpustakaan</p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p><?= $error ?></p>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Email</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-frosted_mint-600 focus:ring-2 focus:ring-icy_blue-200 transition">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Password</label>
                        <input type="password" name="password" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-frosted_mint-600 focus:ring-2 focus:ring-icy_blue-200 transition">
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm">
                            <a href="register.php" class="font-medium text-frosted_mint-700 hover:text-frosted_mint-600">Belum punya akun? Daftar</a>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-baby_pink-600 hover:bg-baby_pink-700 text-white font-bold py-3 rounded-lg shadow-lg transition transform hover:scale-[1.02]">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
