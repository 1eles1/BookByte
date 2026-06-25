<?php
include '../../config/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $db->prepare("UPDATE tbllogininformation SET remarks = :remarks WHERE id = :id");
    $archivedStatus = 'archived';
    $stmt->bindParam(':remarks', $archivedStatus);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header('Location: manage_users.php');
    exit();
} else {
    header('Location: manage_users.php');
    exit();
}
?>