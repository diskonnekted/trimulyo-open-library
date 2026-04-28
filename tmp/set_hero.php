<?php
require_once __DIR__ . '/../config/database.php';
try {
    $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE key_name = ?");
    $stmt->execute(['hero.jpg', 'hero_image']);
    echo "updated\n";
} catch (PDOException $e) {
    echo "error: " . $e->getMessage() . "\n";
    http_response_code(500);
}
?>
