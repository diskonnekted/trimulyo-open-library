<?php
require_once 'config/database.php';
try {
    $stmt = $pdo->query("DESCRIBE loans");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($columns);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>