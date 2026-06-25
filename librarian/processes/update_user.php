<?php
include '../../config/config.php';

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['username'];
    $email = $_POST['email'];

    $stmt = $db->prepare("UPDATE tbllogininformation SET username = :username, email = :email WHERE id = :id");
    $stmt->bindParam(':username', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header('Location: manage_users.php');
}
?>