<?php
session_start();
include '../config/config.php';

// Check if user is logged in as student
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

// Get statistics
try {
    $totalAvailableBooks = $db->query("SELECT COUNT(*) as count FROM tbl_books WHERE remarks='available'")->fetch()['count'];
    $totalBooks = $db->query("SELECT COUNT(*) as count FROM tbl_books")->fetch()['count'];
    // Get user's borrowed books - use correct session variable name from login
    $student_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;
    
    if ($student_id) {
        $userBorrowedBooks = $db->prepare("SELECT COUNT(*) as count FROM tbl_borrowed WHERE student_id = ? AND status = 'borrowed'");
        $userBorrowedBooks->execute([$student_id]);
        $userBorrowedBooks = $userBorrowedBooks->fetch()['count'];
        
        // Get user's reserved books
        $userReservedBooks = $db->prepare("SELECT COUNT(*) as count FROM tbl_reservations WHERE student_id = ? AND status = 'reserved'");
        $userReservedBooks->execute([$student_id]);
        $userReservedBooks = $userReservedBooks->fetch()['count'];
    } else {
        $userBorrowedBooks = $userReservedBooks = 0;
    }
} catch (Exception $e) {
    $totalAvailableBooks = $totalBooks = $userBorrowedBooks = $userReservedBooks = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | Library Management System</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
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
                    <a href="index.php" class="nav-item active">
                        <i class="fas fa-tachometer-alt"></i> Overview
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Books</div>
                    <a href="processes/books.php" class="nav-item">
                        <i class="fas fa-book"></i> Browse Books
                    </a>
                    <a href="processes/borrowed.php" class="nav-item">
                        <i class="fas fa-book-reader"></i> My Borrowed Books
                    </a>
                    <a href="processes/reserved.php" class="nav-item">
                        <i class="fas fa-bookmark"></i> My Reservations
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">History</div>
                    <a href="processes/history.php" class="nav-item">
                        <i class="fas fa-history"></i> Transaction History
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">System</div>
                    <a href="../processes/logout.php" class="nav-item">
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
                <h1 class="header-title">Student Dashboard</h1>
                <div class="header-actions">
                    <span class="text-secondary"><?php echo date('M d, Y'); ?></span>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Stats Overview -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3 class="stat-number"><?php echo $totalAvailableBooks; ?></h3>
                        <p class="stat-label">Available Books</p>
                    </div>
                    <div class="stat-card success">
                        <h3 class="stat-number"><?php echo $userBorrowedBooks; ?></h3>
                        <p class="stat-label">Books Borrowed</p>
                    </div>
                    <div class="stat-card warning">
                        <h3 class="stat-number"><?php echo $userReservedBooks; ?></h3>
                        <p class="stat-label">Books Reserved</p>
                    </div>
                    <div class="stat-card danger">
                        <h3 class="stat-number">0</h3>
                        <p class="stat-label">Total Books</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="content-card">
                    <div class="content-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="content-body">
                        <div class="d-flex gap-2">
                            <a href="processes/books.php" class="btn btn-primary">
                                <i class="fas fa-search"></i> Browse Books
                            </a>
                            <a href="processes/borrowed.php" class="btn btn-success">
                                <i class="fas fa-book-reader"></i> My Borrowed Books
                            </a>
                            <a href="processes/reserved.php" class="btn btn-warning">
                                <i class="fas fa-bookmark"></i> My Reservations
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Available Books -->
                <div class="content-card">
                    <div class="content-header">
                        <h3>Available Books</h3>
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
    <?php
    try {
                                        $stmt = $db->query("SELECT * FROM tbl_books WHERE remarks = 'available' ORDER BY id DESC LIMIT 10");
                                        if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
                                                echo "<td>{$row['id']}</td>";
                                                echo "<td>{$row['name']}</td>";
                                                echo "<td>{$row['author']}</td>";
                                                echo "<td><span class='btn btn-info' style='padding: 4px 8px; font-size: 0.8rem;'>{$row['quantity']} available</span></td>";
                                                echo "<td><span class='btn btn-success' style='padding: 4px 8px; font-size: 0.8rem;'>Available</span></td>";
                                                echo "<td>
                                                        <button class='btn btn-primary' style='padding: 4px 8px; font-size: 0.8rem; margin-right: 5px;' onclick='borrowBook({$row['id']})'>
                                                            <i class='fas fa-book-reader'></i> Borrow
                                                        </button>
                                                        <button class='btn btn-warning' style='padding: 4px 8px; font-size: 0.8rem;' onclick='reserveBook({$row['id']})'>
                                                            <i class='fas fa-bookmark'></i> Reserve
                                                        </button>
                                                      </td>";
            echo "</tr>";
        }
                                        } else {
                                            echo "<tr><td colspan='6' class='text-center'>No available books found.</td></tr>";
                                        }
                                    } catch (Exception $e) {
                                        echo "<tr><td colspan='6' class='text-center'>Error loading books: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                                    }
                                    ?>
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
                window.location.href = 'processes/borrow.php?book_id=' + bookId;
            }
        }

        // Book reservation function
        function reserveBook(bookId) {
            if (confirm('Are you sure you want to reserve this book?')) {
                window.location.href = 'processes/reserve.php?book_id=' + bookId;
            }
        }
    </script>
</body>
</html>

