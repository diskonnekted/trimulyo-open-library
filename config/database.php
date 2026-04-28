<?php
$host = 'localhost';
$dbname = 'pustakatrimulyo';
$username = 'root';
$password = '';

if (!defined('BASE_URL')) {
    $projectRoot = realpath(__DIR__ . '/..');
    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : null;

    $basePath = '';
    if ($docRoot && $projectRoot && strncmp($projectRoot, $docRoot, strlen($docRoot)) === 0) {
        $basePath = str_replace('\\', '/', substr($projectRoot, strlen($docRoot)));
    }

    $basePath = '/' . trim($basePath, '/');
    if ($basePath === '/') {
        $basePath = '';
    }
    define('BASE_URL', $basePath . '/');
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If database not found, try connecting without dbname to create it
    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
        $pdo->exec("USE `$dbname`");
    } catch (PDOException $ex) {
        die("Koneksi database gagal: " . $ex->getMessage());
    }
}

function tableExists(PDO $pdo, string $tableName): bool {
    $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
    $stmt->execute([$tableName]);
    return $stmt->fetchColumn() !== false;
}

function ensureSchema(PDO $pdo): void {
    if (!tableExists($pdo, 'admins')) {
        $pdo->exec("CREATE TABLE admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    if (!tableExists($pdo, 'categories')) {
        $pdo->exec("CREATE TABLE categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    if (!tableExists($pdo, 'books')) {
        $pdo->exec("CREATE TABLE books (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            translator VARCHAR(255) NULL,
            publisher VARCHAR(255) NULL,
            publish_location VARCHAR(255) NULL,
            year INT NULL,
            isbn VARCHAR(64) NULL,
            language VARCHAR(16) NULL,
            pages INT NULL,
            dimensions VARCHAR(64) NULL,
            cover_type VARCHAR(64) NULL,
            ddc_code VARCHAR(64) NULL,
            subjects TEXT NULL,
            synopsis TEXT NULL,
            cover_image VARCHAR(255) NULL,
            file_path VARCHAR(255) NULL,
            type ENUM('physical','digital') DEFAULT 'physical',
            stock INT DEFAULT 0,
            category_id INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_books_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    if (!tableExists($pdo, 'members')) {
        $pdo->exec("CREATE TABLE members (
            id INT AUTO_INCREMENT PRIMARY KEY,
            member_code VARCHAR(32) NOT NULL UNIQUE,
            name VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            phone VARCHAR(50) NULL,
            address TEXT NULL,
            photo VARCHAR(255) NULL,
            status ENUM('active','inactive') DEFAULT 'active',
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    if (!tableExists($pdo, 'loans')) {
        $pdo->exec("CREATE TABLE loans (
            id INT AUTO_INCREMENT PRIMARY KEY,
            member_id INT NOT NULL,
            book_id INT NOT NULL,
            loan_date DATE NULL,
            due_date DATE NULL,
            return_date DATETIME NULL,
            status ENUM('pending','borrowed','returned','overdue','rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_loans_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
            CONSTRAINT fk_loans_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    if (!tableExists($pdo, 'settings')) {
        $pdo->exec("CREATE TABLE settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            key_name VARCHAR(100) NOT NULL UNIQUE,
            value TEXT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    if (!tableExists($pdo, 'news_categories')) {
        $pdo->exec("CREATE TABLE news_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    if (!tableExists($pdo, 'news')) {
        $pdo->exec("CREATE TABLE news (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            content TEXT NULL,
            image VARCHAR(255) NULL,
            category_id INT NULL,
            status ENUM('published','draft') DEFAULT 'published',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            views INT DEFAULT 0,
            CONSTRAINT fk_news_category FOREIGN KEY (category_id) REFERENCES news_categories(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    if (!tableExists($pdo, 'news_tags')) {
        $pdo->exec("CREATE TABLE news_tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    if (!tableExists($pdo, 'news_tag_map')) {
        $pdo->exec("CREATE TABLE news_tag_map (
            news_id INT NOT NULL,
            tag_id INT NOT NULL,
            PRIMARY KEY (news_id, tag_id),
            CONSTRAINT fk_news_tag_map_news FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
            CONSTRAINT fk_news_tag_map_tag FOREIGN KEY (tag_id) REFERENCES news_tags(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = 'admin'");
    $stmt->execute();
    if ((int)$stmt->fetchColumn() === 0) {
        $pass = password_hash('admin123', PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO admins (username, password) VALUES ('admin', ?)");
        $insert->execute([$pass]);
    }

    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    $stmt->fetchColumn();
    $defaultCategories = ['Fiksi', 'Sains', 'Sejarah', 'Teknologi', 'Biografi', 'Pendidikan', 'Kesehatan', 'Anak'];
    $checkCategory = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE name = ?");
    $insertCategory = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    foreach ($defaultCategories as $name) {
        $checkCategory->execute([$name]);
        if ((int)$checkCategory->fetchColumn() === 0) {
            $insertCategory->execute([$name]);
        }
    }

    $stmt = $pdo->query("SELECT COUNT(*) FROM news_categories");
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->exec("INSERT INTO news_categories (name, slug) VALUES ('Umum', 'umum'), ('Kegiatan', 'kegiatan'), ('Pengumuman', 'pengumuman')");
    }

    $defaultSettings = [
        'library_name' => 'Perpustakaan Kalurahan Trimulyo',
        'library_description' => 'Perpustakaan Kalurahan Trimulyo, Kapanewon Sleman, Daerah Istimewa Yogyakarta.',
        'library_address' => 'Kalurahan Trimulyo, Sleman, Daerah Istimewa Yogyakarta',
        'library_phone' => '',
        'library_email' => '',
        'library_permit' => '',
        'library_head' => '',
        'logo_display_mode' => 'logo_text',
        'hero_title' => 'Selamat Datang di Perpustakaan Kalurahan Trimulyo',
        'hero_subtitle' => 'Akses koleksi buku fisik dan digital untuk warga Kalurahan Trimulyo.',
        'hero_image' => '69419e648e0cf.jpeg'
    ];

    $insertIgnore = $pdo->prepare("INSERT IGNORE INTO settings (key_name, value) VALUES (?, ?)");
    foreach ($defaultSettings as $k => $v) {
        $insertIgnore->execute([$k, $v]);
    }
}

ensureSchema($pdo);
?>
