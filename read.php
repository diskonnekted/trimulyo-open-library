<?php
require_once 'config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT title, file_path FROM books WHERE id = ? AND type = 'digital'");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book || !$book['file_path']) {
    die("Buku tidak ditemukan atau bukan buku digital.");
}

$file_url = 'uploads/files/' . $book['file_path'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca: <?= htmlspecialchars($book['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; }
        #pdf-container { width: 100%; height: 100%; }
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 50;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            font-weight: bold;
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            background-color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>

    <a href="detail.php?id=<?= $id ?>" class="back-btn">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali
    </a>

    <div id="pdf-container">
        <embed src="<?= htmlspecialchars($file_url) ?>" type="application/pdf" width="100%" height="100%" />
    </div>

</body>
</html>
