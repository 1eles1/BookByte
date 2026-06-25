<?php
include '../../config/config.php';
if (isset($_POST['update'])) {
 $id = $_POST['id'];
 $name = $_POST['name'];
 $author = $_POST['author'];
 $stmt = $db->prepare("UPDATE tbl_books SET name = :name, author = :author WHERE id = :id");
 $stmt->bindParam(':name', $name);
 $stmt->bindParam(':author', $author);
 $stmt->bindParam(':id', $id);
 $stmt->execute();
 header('Location: ../index.php');
}

?>