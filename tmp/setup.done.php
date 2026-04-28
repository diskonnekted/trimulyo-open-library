<?php
require_once 'config/database.php';

try {
    // Table Admins
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Table Categories
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Table Books
    $pdo->exec("CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        author VARCHAR(255) NOT NULL,
        publisher VARCHAR(255),
        year INT,
        synopsis TEXT,
        cover_image VARCHAR(255),
        file_path VARCHAR(255),
        type ENUM('physical', 'digital') DEFAULT 'physical',
        stock INT DEFAULT 0,
        category_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    )");

    // Insert Default Admin if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $pass = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO admins (username, password) VALUES ('admin', '$pass')");
        echo "Admin default dibuat (user: admin, pass: admin123)<br>";
    }

    // Insert Default Categories
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO categories (name) VALUES ('Fiksi'), ('Sains'), ('Sejarah'), ('Teknologi'), ('Biografi')");
        echo "Kategori default dibuat<br>";
    }

    echo "Setup database berhasil! Silakan hapus file setup.php demi keamanan.";

} catch (PDOException $e) {
    die("Error setup database: " . $e->getMessage());
}
?>