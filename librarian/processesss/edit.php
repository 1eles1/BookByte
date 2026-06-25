<?php
session_start();
include '../../config/config.php';

// Check if user is logged in as librarian
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'librarian') {
    header('Location: ../../index.php');
    exit();
}

$success_message = '';
$error_message = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../books.php');
    exit();
}

$id = $_GET['id'];

// Get book data
$stmt = $db->prepare("SELECT * FROM tbl_books WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header('Location: ../books.php');
    exit();
}

if (isset($_POST['update'])) {
    try {
        $name = trim($_POST['name']);
        $author = trim($_POST['author']);
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        
        // Update book
        $stmt = $db->prepare("UPDATE tbl_books SET name = :name, author = :author, quantity = :quantity WHERE id = :id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $success_message = "Book updated successfully!";
            // Refresh book data
            $stmt = $db->prepare("SELECT * FROM tbl_books WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error_message = "Failed to update book. Please try again.";
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Book | Library Management System</title>
  <link rel="stylesheet" href="../../assets/css/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="sidebar-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-book"></i> Library System</h2>
                <div class="user-info">Welcome, Librarian</div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Dashboard</div>
                    <a href="../index.php" class="nav-item">
                        <i class="fas fa-tachometer-alt"></i> Overview
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Student Management</div>
                    <a href="../processes/manage_users.php" class="nav-item">
                        <i class="fas fa-users"></i> Manage Students
                    </a>
                    <a href="../processes/add_user.php" class="nav-item">
                        <i class="fas fa-user-plus"></i> Add Student
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Book Management</div>
                    <a href="add.php" class="nav-item">
                        <i class="fas fa-book-medical"></i> Add Book
                    </a>
                    <a href="../books.php" class="nav-item">
                        <i class="fas fa-list"></i> All Books
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">System</div>
                    <a href="../../processes/logout.php" class="nav-item">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <div class="top-header">
                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="header-title">Edit Book</h1>
                <div class="header-actions">
                    <a href="../books.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Books
                    </a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <div class="content-card">
                    <div class="content-header">
                        <h3>Edit Book Information</h3>
                    </div>
                    <div class="content-body">
                        <?php if ($success_message): ?>
                            <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error_message): ?>
                            <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="edit.php?id=<?php echo $id; ?>" style="max-width: 600px;">
                            <div class="form-group">
                                <label>Book Title</label>
                                <input type="text" name="name" class="form-control" 
                                       value="<?php echo htmlspecialchars($row['name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Author</label>
                                <input type="text" name="author" class="form-control" 
                                       value="<?php echo htmlspecialchars($row['author']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="number" name="quantity" class="form-control" min="0"
                                       value="<?php echo htmlspecialchars($row['quantity'] ?? 0); ?>" required>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" name="update" class="btn btn-success">
                                    <i class="fas fa-save"></i> Update Book
                                </button>
                                <a href="../books.php" class="btn btn-primary">
                                    <i class="fas fa-arrow-left"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('open');
        });
    </script>
</body>
</html>

