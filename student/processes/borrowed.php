<?php
session_start();
include '../../config/config.php';

// Check if user is logged in as student
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'student') {
    header('Location: ../../index.php');
    exit();
}

// Get student ID - use correct session variable
$student_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

if (!$student_id) {
    $borrowedBooks = [];
} else {
    // Get student's borrowed books
    try {
        $borrowedQuery = "
            SELECT 
                bb.id as transaction_id,
                bb.book_id,
                bb.date_borrowed,
                bb.status,
                b.name as book_title,
                b.author as book_author
            FROM tbl_borrowed bb
            JOIN tbl_books b ON bb.book_id = b.id
            WHERE bb.student_id = ? AND bb.status = 'borrowed'
            ORDER BY bb.date_borrowed DESC
        ";
        
        $borrowedStmt = $db->prepare($borrowedQuery);
        $borrowedStmt->execute([$student_id]);
        $borrowedBooks = $borrowedStmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        $borrowedBooks = [];
    }
}

// Handle success/error messages
$message = '';
$messageClass = '';
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'return_success':
            $message = 'Book returned successfully!';
            $messageClass = 'success';
            break;
        case 'return_error':
            $message = 'Error returning book. Please try again.';
            $messageClass = 'error';
            break;
        case 'invalid_request':
            $message = 'Invalid request.';
            $messageClass = 'error';
            break;
        case 'transaction_not_found':
            $message = 'Transaction not found.';
            $messageClass = 'error';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Borrowed Books | Library Management System</title>
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
                    <a href="books.php" class="nav-item">
                        <i class="fas fa-book"></i> Browse Books
                    </a>
                    <a href="borrowed.php" class="nav-item active">
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
                <h1 class="header-title">My Borrowed Books</h1>
                <div class="header-actions">
                    <span class="text-secondary"><?php echo date('M d, Y'); ?></span>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <?php if ($message): ?>
                    <div style="background: <?php echo $messageClass === 'success' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $messageClass === 'success' ? '#155724' : '#721c24'; ?>; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid <?php echo $messageClass === 'success' ? '#c3e6cb' : '#f5c6cb'; ?>;">
                        <i class="fas fa-<?php echo $messageClass === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i> <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <!-- Borrowed Books -->
                <div class="content-card">
                    <div class="content-header">
                        <h3>Borrowed Books</h3>
                    </div>
                    <div class="content-body">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Borrow Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($borrowedBooks)): ?>
                                        <?php foreach ($borrowedBooks as $book): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($book['book_title']); ?></strong><br>
                                                    <small class="text-muted">by <?php echo htmlspecialchars($book['book_author']); ?></small>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($book['date_borrowed'])); ?></td>
                                                <td>
                                                    <?php 
                                                    $status = $book['status'];
                                                    $statusClass = $status === 'borrowed' ? 'success' : 'secondary';
                                                    $statusText = ucfirst($status);
                                                    ?>
                                                    <span class="btn btn-<?php echo $statusClass; ?>" style="padding: 4px 8px; font-size: 0.8rem;">
                                                        <?php echo $statusText; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-danger" style="padding: 4px 8px; font-size: 0.8rem;" onclick="returnBook(<?php echo $book['transaction_id']; ?>)">
                                                        <i class="fas fa-undo"></i> Return
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No borrowed books found.</td>
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

        // Return book function
        function returnBook(transactionId) {
            if (confirm('Are you sure you want to return this book?')) {
                window.location.href = 'return.php?id=' + transactionId;
            }
        }
    </script>
</body>
</html>

