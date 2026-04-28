<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get file info to delete files
    $stmt = $pdo->prepare("SELECT cover_image, file_path FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch();

    if ($book) {
        if ($book['cover_image'] && file_exists("../uploads/covers/" . $book['cover_image'])) {
            unlink("../uploads/covers/" . $book['cover_image']);
        }
        if ($book['file_path'] && file_exists("../uploads/files/" . $book['file_path'])) {
            unlink("../uploads/files/" . $book['file_path']);
        }

        $pdo->prepare("DELETE FROM books WHERE id = ?")->execute([$id]);
    }

    redirect('books.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $title = sanitize($_POST['title']);
    $author = sanitize($_POST['author']);
    $publisher = sanitize($_POST['publisher']);
    $year = (int)$_POST['year'];
    $synopsis = $_POST['synopsis']; // Allow formatting
    $category_id = (int)$_POST['category_id'];
    $type = $_POST['type'];
    $stock = (int)$_POST['stock'];

    // New Fields
    $translator = isset($_POST['translator']) ? sanitize($_POST['translator']) : null;
    $isbn = isset($_POST['isbn']) ? sanitize($_POST['isbn']) : null;
    $language = isset($_POST['language']) ? sanitize($_POST['language']) : 'id';
    $pages = isset($_POST['pages']) ? (int)$_POST['pages'] : null;
    $dimensions = isset($_POST['dimensions']) ? sanitize($_POST['dimensions']) : null;
    $cover_type = isset($_POST['cover_type']) ? sanitize($_POST['cover_type']) : null;
    $ddc_code = isset($_POST['ddc_code']) ? sanitize($_POST['ddc_code']) : null;
    $subjects = isset($_POST['subjects']) ? sanitize($_POST['subjects']) : null;
    $publish_location = isset($_POST['publish_location']) ? sanitize($_POST['publish_location']) : null;

    // Handle File Uploads
    $cover_image = null;
    $file_path = null;

    if (!empty($_FILES['cover_image']['name'])) {
        $upload = uploadFile($_FILES['cover_image'], '../uploads/covers', ['jpg', 'jpeg', 'png', 'webp']);
        if (isset($upload['success'])) {
            $cover_image = $upload['path'];
        }
    } elseif (isset($_POST['remote_cover_url']) && !empty($_POST['remote_cover_url'])) {
        // Download remote image
        $remote_url = $_POST['remote_cover_url'];
        $ext = pathinfo($remote_url, PATHINFO_EXTENSION);
        if (!$ext) $ext = 'jpg'; // Default to jpg if extension missing
        
        // Clean URL params if any
        $ext = explode('?', $ext)[0];

        $new_filename = uniqid() . '.' . $ext;
        $local_path = '../uploads/covers/' . $new_filename;

        // Use cURL instead of file_get_contents for better compatibility
        $ch = curl_init($remote_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "LibraryApp/1.0");
        $image_content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200 && $image_content) {
            file_put_contents($local_path, $image_content);
            $cover_image = $new_filename;
        }
    }

    if ($type === 'digital' && !empty($_FILES['pdf_file']['name'])) {
        $upload = uploadFile($_FILES['pdf_file'], '../uploads/files', ['pdf']);
        if (isset($upload['success'])) {
            $file_path = $upload['path'];
        }
    }

    if ($id) {
        // Update
        $query = "UPDATE books SET title=?, author=?, publisher=?, year=?, synopsis=?, category_id=?, type=?, stock=?, translator=?, isbn=?, language=?, pages=?, dimensions=?, cover_type=?, ddc_code=?, subjects=?, publish_location=?";
        $params = [$title, $author, $publisher, $year, $synopsis, $category_id, $type, $stock, $translator, $isbn, $language, $pages, $dimensions, $cover_type, $ddc_code, $subjects, $publish_location];

        if ($cover_image) {
            $query .= ", cover_image=?";
            $params[] = $cover_image;
        }
        if ($file_path) {
            $query .= ", file_path=?";
            $params[] = $file_path;
        }

        $query .= " WHERE id=?";
        $params[] = $id;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

    } else {
        // Insert
        $query = "INSERT INTO books (title, author, publisher, year, synopsis, category_id, type, stock, cover_image, file_path, translator, isbn, language, pages, dimensions, cover_type, ddc_code, subjects, publish_location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$title, $author, $publisher, $year, $synopsis, $category_id, $type, $stock, $cover_image, $file_path, $translator, $isbn, $language, $pages, $dimensions, $cover_type, $ddc_code, $subjects, $publish_location];
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
    }

    redirect('books.php');
}
?>