<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's an update (approval)
    if (isset($_POST['loan_id']) && !empty($_POST['loan_id'])) {
        $loan_id = $_POST['loan_id'];
        $loan_date = $_POST['loan_date'];
        $due_date = $_POST['due_date'];
        
        try {
            $pdo->beginTransaction();
            
            // Get book_id from existing loan
            $stmt = $pdo->prepare("SELECT book_id FROM loans WHERE id = ?");
            $stmt->execute([$loan_id]);
            $loan = $stmt->fetch();
            $book_id = $loan['book_id'];

            // Update Loan Status and Dates
            $stmt = $pdo->prepare("UPDATE loans SET loan_date = ?, due_date = ?, status = 'borrowed' WHERE id = ?");
            $stmt->execute([$loan_date, $due_date, $loan_id]);
            
            // Decrease Stock
            $stmt = $pdo->prepare("UPDATE books SET stock = stock - 1 WHERE id = ?");
            $stmt->execute([$book_id]);
            
            $pdo->commit();
            header('Location: loans.php');
            exit;
        } catch (PDOException $e) {
             $pdo->rollBack();
             die("Error: " . $e->getMessage());
        }
    }

    $member_id = $_POST['member_id'];
    $book_id = $_POST['book_id'];
    $loan_date = $_POST['loan_date'];
    $due_date = $_POST['due_date'];

    try {
        $pdo->beginTransaction();

        // 1. Insert Loan
        $stmt = $pdo->prepare("INSERT INTO loans (member_id, book_id, loan_date, due_date, status) VALUES (?, ?, ?, ?, 'borrowed')");
        $stmt->execute([$member_id, $book_id, $loan_date, $due_date]);

        // 2. Decrease Book Stock
        $stmt = $pdo->prepare("UPDATE books SET stock = stock - 1 WHERE id = ?");
        $stmt->execute([$book_id]);

        $pdo->commit();
        header('Location: loans.php');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }

} elseif (isset($_GET['return'])) {
    $loan_id = (int)$_GET['return'];

    try {
        $pdo->beginTransaction();

        // Get Loan Info to find book_id
        $stmt = $pdo->prepare("SELECT book_id, status FROM loans WHERE id = ?");
        $stmt->execute([$loan_id]);
        $loan = $stmt->fetch();

        if ($loan && $loan['status'] == 'borrowed') {
            // 1. Update Loan Status
            $stmt = $pdo->prepare("UPDATE loans SET status = 'returned', return_date = NOW() WHERE id = ?");
            $stmt->execute([$loan_id]);

            // 2. Increase Book Stock
            $stmt = $pdo->prepare("UPDATE books SET stock = stock + 1 WHERE id = ?");
            $stmt->execute([$loan['book_id']]);
        }

        $pdo->commit();
        header('Location: loans.php');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>