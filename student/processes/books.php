<?php
session_start();
include '../../config/config.php';

// Check if user is logged in as student
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'student') {
    header('Location: ../../index.php');
    exit();
}

// Get all books
try {
    $booksQuery = "SELECT * FROM tbl_books ORDER BY id DESC";
    $booksStmt = $db->query($booksQuery);
    $books = $booksStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $books = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books | Library Management System</title>
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="sidebar-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-book"></i> Library System</h2>
                <div class="user-info">Welcome, Student</div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Dashboard</div>
                    <a href="../index.php" class="nav-item">
                        <i class="fas fa-tachometer-alt"></i> Overview
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Books</div>
                    <a href="books.php" class="nav-item active">
                        <i class="fas fa-book"></i> Browse Books
                    </a>
                    <a href="borrowed.php" class="nav-item">
                        <i class="fas fa-book-reader"></i> My Borrowed Books
                    </a>
                    <a href="reserved.php" class="nav-item">
                        <i class="fas fa-bookmark"></i> My Reservations
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
                <h1 class="header-title">Browse Books</h1>
                <div class="header-actions">
                    <span class="text-secondary"><?php echo date('M d, Y'); ?></span>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- All Books -->
                <div class="content-card">
                    <div class="content-header">
                        <h3>All Books</h3>
                    </div>
                    <div class="content-body">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($books)): ?>
                                        <?php foreach ($books as $book): ?>
                                            <tr>
                                                <td><?php echo $book['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($book['name']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo htmlspecialchars($book['quantity'] ?? 0); ?> available</span>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $status = $book['remarks'];
                                                    $statusClass = $status === 'available' ? 'success' : 'danger';
                                                    $statusText = ucfirst($status);
                                                    ?>
                                                    <span class="btn btn-<?php echo $statusClass; ?>" style="padding: 4px 8px; font-size: 0.8rem;">
                                                        <?php echo $statusText; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $quantity = $book['quantity'] ?? 0;
                                                    if ($status === 'available' && $quantity > 0): ?>
                                                        <button class="btn btn-primary" style="padding: 4px 8px; font-size: 0.8rem; margin-right: 5px;" onclick="borrowBook(<?php echo $book['id']; ?>)">
                                                            <i class="fas fa-book-reader"></i> Borrow
                                                        </button>
                                                        <button class="btn btn-warning" style="padding: 4px 8px; font-size: 0.8rem;" onclick="reserveBook(<?php echo $book['id']; ?>)">
                                                            <i class="fas fa-bookmark"></i> Reserve
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not available</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No books found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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

        // Book borrowing function
        function borrowBook(bookId) {
            if (confirm('Are you sure you want to borrow this book?')) {
                window.location.href = 'borrow.php?book_id=' + bookId;
            }
        }

        // Book reservation function
        function reserveBook(bookId) {
            if (confirm('Are you sure you want to reserve this book?')) {
                window.location.href = 'reserve.php?book_id=' + bookId;
            }
        }
    </script>
</body>
</html>

