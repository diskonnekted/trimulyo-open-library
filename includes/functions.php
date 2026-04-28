<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "admin/index.php");
        exit;
    }
}

function sanitize($data) {
    return strip_tags(trim($data));
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function uploadFile($file, $targetDir, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf']) {
    $fileName = basename($file['name']);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileType, $allowedTypes)) {
        return ['error' => 'Tipe file tidak diizinkan.'];
    }

    $newFileName = uniqid() . '.' . $fileType;
    $targetPath = $targetDir . '/' . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'path' => $newFileName];
    }

    return ['error' => 'Gagal mengupload file.'];
}

function getSetting($pdo, $key, $default = '') {
    try {
        $stmt = $pdo->prepare("SELECT value FROM settings WHERE key_name = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetchColumn();
        return $result !== false ? $result : $default;
    } catch (PDOException $e) {
        return $default;
    }
}
?>