<?php
require_once 'config/database.php';

try {
    // News Categories Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS news_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE
    )");

    // News Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS news (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        content TEXT,
        image VARCHAR(255),
        category_id INT,
        status ENUM('published', 'draft') DEFAULT 'published',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        views INT DEFAULT 0,
        FOREIGN KEY (category_id) REFERENCES news_categories(id) ON DELETE SET NULL
    )");

    // News Tags Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS news_tags (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE
    )");

    // News Tag Map Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS news_tag_map (
        news_id INT,
        tag_id INT,
        PRIMARY KEY (news_id, tag_id),
        FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
        FOREIGN KEY (tag_id) REFERENCES news_tags(id) ON DELETE CASCADE
    )");

    // Insert Default Category if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM news_categories");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO news_categories (name, slug) VALUES ('Umum', 'umum'), ('Kegiatan', 'kegiatan'), ('Pengumuman', 'pengumuman')");
    }

    echo "News tables created successfully.";

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>
