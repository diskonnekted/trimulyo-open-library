<?php
require_once 'config/database.php';

try {
    // 1. Modify status ENUM to include 'pending' and 'rejected'
    // Note: We need to list all existing values plus new ones
    $pdo->exec("ALTER TABLE loans MODIFY COLUMN status ENUM('pending', 'borrowed', 'returned', 'overdue', 'rejected') DEFAULT 'pending'");
    echo "Modified status column.\n";

    // 2. Make loan_date and due_date nullable
    $pdo->exec("ALTER TABLE loans MODIFY COLUMN loan_date DATE NULL");
    echo "Made loan_date nullable.\n";
    
    $pdo->exec("ALTER TABLE loans MODIFY COLUMN due_date DATE NULL");
    echo "Made due_date nullable.\n";

    echo "Database schema updated successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>