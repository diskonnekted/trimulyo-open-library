<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $status = $_POST['status'];
    $password = $_POST['password'];

    try {
        if ($id) {
            // Update
            $sql = "UPDATE members SET name = ?, email = ?, phone = ?, address = ?, status = ? WHERE id = ?";
            $params = [$name, $email, $phone, $address, $status, $id];

            if (!empty($password)) {
                $sql = "UPDATE members SET name = ?, email = ?, phone = ?, address = ?, status = ?, password = ? WHERE id = ?";
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $params = [$name, $email, $phone, $address, $status, $hashed_password, $id];
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        } else {
            // Create
            // Generate Member Code
            $prefix = "MEM-" . date("Ym") . "-";
            $stmt = $pdo->query("SELECT MAX(id) as max_id FROM members");
            $row = $stmt->fetch();
            $next_id = ($row['max_id'] ?? 0) + 1;
            $member_code = $prefix . str_pad($next_id, 4, '0', STR_PAD_LEFT);

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO members (member_code, name, email, phone, address, status, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$member_code, $name, $email, $phone, $address, $status, $hashed_password]);
        }
        
        header('Location: members.php');
        exit;

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} elseif (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: members.php');
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>