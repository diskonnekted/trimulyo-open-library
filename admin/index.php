<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        redirect('dashboard.php');
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        baby_pink: { DEFAULT: '#fb8500', 100: '#FFF3E0', 200: '#FFE0B2', 300: '#FFCC80', 400: '#FFB74D', 500: '#FB8500', 600: '#E27600', 700: '#C76600', 800: '#A95600', 900: '#6B3800' },
                        lemon_chiffon: { DEFAULT: '#ffb703', 100: '#FFF7E0', 200: '#FFE8A3', 300: '#FFD66B', 400: '#FFC53A', 500: '#FFB703', 600: '#E6A402', 700: '#CC9302', 800: '#A67702', 900: '#5E4501' },
                        frosted_mint: { DEFAULT: '#219ebc', 100: '#E6F5FA', 200: '#CDECF6', 300: '#A3DBED', 400: '#6EC3E0', 500: '#219EBC', 600: '#1C8AA4', 700: '#176F86', 800: '#12586A', 900: '#0B3642' },
                        icy_blue: { DEFAULT: '#8ecae6', 100: '#EFF7FC', 200: '#D9EEF9', 300: '#BFE3F5', 400: '#A5D7EF', 500: '#8ECAE6', 600: '#6FB7DA', 700: '#559FC6', 800: '#3D84AC', 900: '#245570' },
                        mauve: { DEFAULT: '#023047', 100: '#E6EEF2', 200: '#CCDDE5', 300: '#9EBECE', 400: '#6E96AB', 500: '#023047', 600: '#02283A', 700: '#011F2D', 800: '#011723', 900: '#000E16' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Login Admin</h1>
            <p class="text-gray-600">Masuk untuk mengelola perpustakaan</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" name="username" type="text" placeholder="Username" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" name="password" type="password" placeholder="******************" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-baby_pink-600 hover:bg-baby_pink-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full" type="submit">
                    Masuk
                </button>
            </div>
            <div class="text-center mt-4">
                 <a href="../index.php" class="text-sm text-gray-500 hover:text-gray-800">Kembali ke Beranda</a>
            </div>
        </form>
    </div>
</body>
</html>
