<?php
session_start();
include '../../config/config.php';

// Check if user is logged in as librarian
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'librarian') {
    header('Location: ../../index.php');
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    try {
        $id = (int) $_GET['id'];
        
        // Check if user exists and is a student
        $checkStmt = $db->prepare("SELECT id, username FROM tbllogininformation WHERE id = :id AND accounttype = 'student'");
        $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            $user = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            // Archive the student (set remarks to 'archived')
            $stmt = $db->prepare("UPDATE tbllogininformation SET remarks = :remarks WHERE id = :id");
            $archivedStatus = 'archived';
            $stmt->bindParam(':remarks', $archivedStatus);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                // Success - redirect with success message
                header('Location: manage_users.php?msg=archived&user=' . urlencode($user['username']));
                exit();
            } else {
                // Database error
                header('Location: manage_users.php?error=archive_failed');
                exit();
            }
        } else {
            // User not found or not a student
            header('Location: manage_users.php?error=user_not_found');
            exit();
        }
    } catch (Exception $e) {
        // Exception occurred
        header('Location: manage_users.php?error=database_error');
        exit();
    }
} else {
    // Invalid ID
    header('Location: manage_users.php?error=invalid_id');
    exit();
}
?>