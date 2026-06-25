<?php
session_start();
include '../../config/config.php';

// Check if user is logged in as student
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

$transaction_id = $_GET['id'] ?? null;
$student_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

if (!$student_id || !$transaction_id) {
    header("Location: borrowed.php?msg=invalid_request");
    exit();
}

try {
    // Get borrow transaction details
    $borrowQuery = $db->prepare("SELECT * FROM tbl_borrowed WHERE id = ? AND student_id = ? AND status = 'borrowed'");
    $borrowQuery->execute([$transaction_id, $student_id]);
    $borrow = $borrowQuery->fetch(PDO::FETCH_ASSOC);

    if (!$borrow) {
        header("Location: borrowed.php?msg=transaction_not_found");
        exit();
    }

    $book_id = $borrow['book_id'];

    // Update borrow status to 'returned'
    $date_returned = date('Y-m-d H:i:s');
    $updateBorrowStmt = $db->prepare("UPDATE tbl_borrowed SET status = 'returned', date_returned = ? WHERE id = ?");
    $updateBorrowStmt->execute([$date_returned, $transaction_id]);

    // Increment book quantity
    $bookQuery = $db->prepare("SELECT quantity FROM tbl_books WHERE id = ?");
    $bookQuery->execute([$book_id]);
    $book = $bookQuery->fetch(PDO::FETCH_ASSOC);
    
    $new_quantity = $book['quantity'] + 1;
    
    $updateBookStmt = $db->prepare("UPDATE tbl_books SET quantity = ?, remarks = 'available' WHERE id = ?");
    $updateBookStmt->execute([$new_quantity, $book_id]);

    header("Location: borrowed.php?msg=return_success");
    exit();

} catch (Exception $e) {
    header("Location: borrowed.php?msg=return_error");
    exit();
}
?>
