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
    header("Location: reserved.php?msg=invalid_request");
    exit();
}

try {
    // Get reservation transaction details
    $reserveQuery = $db->prepare("SELECT * FROM tbl_reservations WHERE id = ? AND student_id = ? AND status = 'reserved'");
    $reserveQuery->execute([$transaction_id, $student_id]);
    $reserve = $reserveQuery->fetch(PDO::FETCH_ASSOC);

    if (!$reserve) {
        header("Location: reserved.php?msg=transaction_not_found");
        exit();
    }

    $book_id = $reserve['book_id'];

    // Update reservation status to 'cancelled'
    $updateReserveStmt = $db->prepare("UPDATE tbl_reservations SET status = 'cancelled' WHERE id = ?");
    $updateReserveStmt->execute([$transaction_id]);

    // Increment book quantity
    $bookQuery = $db->prepare("SELECT quantity FROM tbl_books WHERE id = ?");
    $bookQuery->execute([$book_id]);
    $book = $bookQuery->fetch(PDO::FETCH_ASSOC);
    
    $new_quantity = $book['quantity'] + 1;
    
    $updateBookStmt = $db->prepare("UPDATE tbl_books SET quantity = ?, remarks = 'available' WHERE id = ?");
    $updateBookStmt->execute([$new_quantity, $book_id]);

    header("Location: reserved.php?msg=cancel_success");
    exit();

} catch (Exception $e) {
    header("Location: reserved.php?msg=cancel_error");
    exit();
}
?>
