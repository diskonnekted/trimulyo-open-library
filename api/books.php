<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try {
    if ($q) {
        $stmt = $pdo->prepare("SELECT b.id, b.title, b.author, b.publisher, b.year, b.isbn, b.cover_image, c.name as category 
                               FROM books b 
                               LEFT JOIN categories c ON b.category_id = c.id 
                               WHERE b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ? 
                               ORDER BY b.created_at DESC LIMIT ? OFFSET ?");
        $searchTerm = "%$q%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $limit, $offset]);
    } else {
        $stmt = $pdo->prepare("SELECT b.id, b.title, b.author, b.publisher, b.year, b.isbn, b.cover_image, c.name as category 
                               FROM books b 
                               LEFT JOIN categories c ON b.category_id = c.id 
                               ORDER BY b.created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
    }

    $books = $stmt->fetchAll();
    
    // Clean data and add full cover URL
    foreach ($books as &$book) {
        if ($book['cover_image']) {
            $book['cover_url'] = BASE_URL . 'uploads/covers/' . $book['cover_image'];
        } else {
            $book['cover_url'] = null;
        }
    }

    echo json_encode([
        'status' => 'success',
        'query' => $q,
        'page' => $page,
        'limit' => $limit,
        'data' => $books
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
