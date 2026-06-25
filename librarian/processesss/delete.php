<?php
session_start();
include '../../config/config.php';

// Check if user is logged in as librarian
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'librarian') {
    header('Location: ../../index.php');
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    try {
        // Check if book exists
        $checkStmt = $db->prepare("SELECT id FROM tbl_books WHERE id = :id");
        $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            // Check if book is currently borrowed
            $borrowCheck = $db->prepare("SELECT id FROM tbl_borrowings WHERE book_id = :id AND status = 'borrowed'");
            $borrowCheck->bindParam(':id', $id, PDO::PARAM_INT);
            $borrowCheck->execute();
            
            if ($borrowCheck->rowCount() > 0) {
                header("Location: ../index.php?error=book_borrowed");
                exit;
            }
            
            // Delete the book
            $stmt = $db->prepare("DELETE FROM tbl_books WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                header("Location: ../index.php?msg=deleted");
                exit;
            } else {
                header("Location: ../index.php?error=delete_failed");
                exit;
            }
        } else {
            header("Location: ../index.php?error=book_not_found");
            exit;
        }
    } catch (Exception $e) {
        header("Location: ../index.php?error=database_error");
        exit;
    }
} else {
    header("Location: ../index.php?error=invalid_id");
    exit;
}
?>
