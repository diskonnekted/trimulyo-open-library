<?php
require_once 'config/database.php';

try {
    echo "Menambahkan kategori baru...\n";
    $cats = ['Pendidikan', 'Kesehatan', 'Anak'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
    
    foreach ($cats as $cat) {
        $stmt->execute([$cat]);
        if ($stmt->rowCount() > 0) {
            echo "- Kategori '$cat' berhasil ditambahkan.\n";
        } else {
            echo "- Kategori '$cat' sudah ada.\n";
        }
    }
    echo "Selesai.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
