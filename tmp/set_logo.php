<?php
require_once __DIR__ . '/../config/database.php';
try {
    $pdo->prepare("INSERT INTO settings (key_name, value) VALUES ('library_logo', 'logo.png') ON DUPLICATE KEY UPDATE value='logo.png'")->execute();
    $pdo->prepare("INSERT INTO settings (key_name, value) VALUES ('logo_display_mode', 'logo_text') ON DUPLICATE KEY UPDATE value='logo_text'")->execute();
    echo "updated\n";
} catch (PDOException $e) {
    echo "error: " . $e->getMessage() . "\n";
    http_response_code(500);
}
?>
