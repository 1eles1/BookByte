<?php
session_start();
include '../../config/config.php';

// Check if user is logged in as student
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

$book_id = $_GET['book_id'];
$student_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

if (!$student_id) {
    header("Location: ../index.php?msg=session_error");
    exit();
}

try {
    // Check if book exists and is available
    $bookQuery = $db->prepare("SELECT * FROM tbl_books WHERE id = ?");
    $bookQuery->execute([$book_id]);
    $book = $bookQuery->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        header("Location: ../index.php?msg=book_not_found");
        exit();
    }

    if ($book['quantity'] <= 0) {
        header("Location: ../index.php?msg=book_not_available");
        exit();
    }

    // Check if student already borrowed this book
    $existingBorrowQuery = $db->prepare("SELECT id FROM tbl_borrowed WHERE student_id = ? AND book_id = ? AND status = 'borrowed'");
    $existingBorrowQuery->execute([$student_id, $book_id]);
    
    if ($existingBorrowQuery->rowCount() > 0) {
        header("Location: ../index.php?msg=already_borrowed");
        exit();
    }

    // Insert borrow record
    $date_borrowed = date('Y-m-d H:i:s');
    $borrowStmt = $db->prepare("INSERT INTO tbl_borrowed (student_id, book_id, date_borrowed, status) VALUES (?, ?, ?, 'borrowed')");
    $borrowStmt->execute([$student_id, $book_id, $date_borrowed]);

    // Update book quantity
    $new_quantity = $book['quantity'] - 1;
    $book_status = ($new_quantity <= 0) ? 'unavailable' : 'available';
    
    $updateBookStmt = $db->prepare("UPDATE tbl_books SET quantity = ?, remarks = ? WHERE id = ?");
    $updateBookStmt->execute([$new_quantity, $book_status, $book_id]);

    header("Location: ../index.php?msg=borrow_success");
    exit();

} catch (Exception $e) {
    header("Location: ../index.php?msg=borrow_error");
    exit();
}
?>
