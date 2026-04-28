<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
requireLogin();

header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q) {
    $stmt = $pdo->prepare("SELECT id, title, author, cover_image FROM books WHERE title LIKE ? OR author LIKE ? LIMIT 10");
    $searchTerm = "%$q%";
    $stmt->execute([$searchTerm, $searchTerm]);
    $books = $stmt->fetchAll();
} else {
    $books = [];
}

echo json_encode($books);
?>
