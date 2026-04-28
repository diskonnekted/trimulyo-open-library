<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get image to delete
    $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $news = $stmt->fetch();

    if ($news) {
        if ($news['image'] && file_exists("../uploads/news/" . $news['image'])) {
            unlink("../uploads/news/" . $news['image']);
        }
        $pdo->prepare("DELETE FROM news WHERE id = ?")->execute([$id]);
    }

    redirect('news.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $title = sanitize($_POST['title']);
    $content = $_POST['content']; // Allow HTML/formatting if we had an editor, but sanitize implies stripping tags. For now keep as is or use htmlspecialchars on output.
    $category_id = (int)$_POST['category_id'];
    $status = $_POST['status'];
    $tags_input = $_POST['tags'];

    // Slug generation
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    // Ensure unique slug
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM news WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $id ? $id : 0]);
    if ($stmt->fetchColumn() > 0) {
        $slug .= '-' . time();
    }

    // Handle Image Upload
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $upload = uploadFile($_FILES['image'], '../uploads/news', ['jpg', 'jpeg', 'png', 'webp']);
        if (isset($upload['success'])) {
            $image = $upload['path'];
        }
    }

    try {
        $pdo->beginTransaction();

        if ($id) {
            // Update
            $query = "UPDATE news SET title=?, slug=?, content=?, category_id=?, status=?, updated_at=NOW()";
            $params = [$title, $slug, $content, $category_id, $status];

            if ($image) {
                // Delete old image
                $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
                $stmt->execute([$id]);
                $old_news = $stmt->fetch();
                if ($old_news['image'] && file_exists("../uploads/news/" . $old_news['image'])) {
                    unlink("../uploads/news/" . $old_news['image']);
                }

                $query .= ", image=?";
                $params[] = $image;
            }

            $query .= " WHERE id=?";
            $params[] = $id;
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
        } else {
            // Insert
            $query = "INSERT INTO news (title, slug, content, category_id, status, image) VALUES (?, ?, ?, ?, ?, ?)";
            $params = [$title, $slug, $content, $category_id, $status, $image];
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $id = $pdo->lastInsertId();
        }

        // Handle Tags
        // 1. Clear existing tags for this news
        $pdo->prepare("DELETE FROM news_tag_map WHERE news_id = ?")->execute([$id]);

        // 2. Process new tags
        if (!empty($tags_input)) {
            $tags_array = array_map('trim', explode(',', $tags_input));
            $tags_array = array_unique($tags_array);

            foreach ($tags_array as $tag_name) {
                if (empty($tag_name)) continue;

                $tag_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tag_name)));

                // Check if tag exists
                $stmt = $pdo->prepare("SELECT id FROM news_tags WHERE slug = ?");
                $stmt->execute([$tag_slug]);
                $tag_id = $stmt->fetchColumn();

                if (!$tag_id) {
                    // Create new tag
                    $stmt = $pdo->prepare("INSERT INTO news_tags (name, slug) VALUES (?, ?)");
                    $stmt->execute([$tag_name, $tag_slug]);
                    $tag_id = $pdo->lastInsertId();
                }

                // Link tag to news
                $stmt = $pdo->prepare("INSERT INTO news_tag_map (news_id, tag_id) VALUES (?, ?)");
                $stmt->execute([$id, $tag_id]);
            }
        }

        // Handle Related Books
        // 1. Clear existing books map
        $pdo->prepare("DELETE FROM news_book_map WHERE news_id = ?")->execute([$id]);

        // 2. Insert new books map
        if (isset($_POST['related_books']) && is_array($_POST['related_books'])) {
            $book_stmt = $pdo->prepare("INSERT INTO news_book_map (news_id, book_id) VALUES (?, ?)");
            foreach ($_POST['related_books'] as $book_id) {
                $book_stmt->execute([$id, (int)$book_id]);
            }
        }

        $pdo->commit();
        redirect('news.php');

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>
