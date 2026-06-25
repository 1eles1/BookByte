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
    $reservedBooks = [];
} else {
    // Get student's reserved books
    try {
        $reservedQuery = "
            SELECT 
                rb.id as transaction_id,
                rb.book_id,
                rb.date_reserved,
                rb.status,
                b.name as book_title,
                b.author as book_author
            FROM tbl_reservations rb
            JOIN tbl_books b ON rb.book_id = b.id
            WHERE rb.student_id = ? AND rb.status = 'reserved'
            ORDER BY rb.date_reserved DESC
        ";
        
        $reservedStmt = $db->prepare($reservedQuery);
        $reservedStmt->execute([$student_id]);
        $reservedBooks = $reservedStmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        $reservedBooks = [];
    }
}

// Handle success/error messages
$message = '';
$messageClass = '';
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'cancel_success':
            $message = 'Reservation cancelled successfully!';
            $messageClass = 'success';
            break;
        case 'cancel_error':
            $message = 'Error cancelling reservation. Please try again.';
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
    <title>My Reserved Books | Library Management System</title>
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
                    <a href="borrowed.php" class="nav-item">
                        <i class="fas fa-book-reader"></i> My Borrowed Books
                    </a>
                    <a href="reserved.php" class="nav-item active">
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
                <h1 class="header-title">My Reserved Books</h1>
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

                <!-- Reserved Books -->
                <div class="content-card">
                    <div class="content-header">
                        <h3>Reserved Books</h3>
                    </div>
                    <div class="content-body">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Reserve Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($reservedBooks)): ?>
                                        <?php foreach ($reservedBooks as $book): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($book['book_title']); ?></strong><br>
                                                    <small class="text-muted">by <?php echo htmlspecialchars($book['book_author']); ?></small>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($book['date_reserved'])); ?></td>
                                                <td>
                                                    <?php 
                                                    $status = $book['status'];
                                                    $statusClass = $status === 'reserved' ? 'warning' : ($status === 'fulfilled' ? 'success' : 'secondary');
                                                    $statusText = ucfirst($status);
                                                    ?>
                                                    <span class="btn btn-<?php echo $statusClass; ?>" style="padding: 4px 8px; font-size: 0.8rem;">
                                                        <?php echo $statusText; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($status === 'reserved'): ?>
                                                        <button class="btn btn-danger" style="padding: 4px 8px; font-size: 0.8rem;" onclick="cancelReservation(<?php echo $book['transaction_id']; ?>)">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="text-muted">No actions available</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No reserved books found.</td>
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

        // Cancel reservation function
        function cancelReservation(reservationId) {
            if (confirm('Are you sure you want to cancel this reservation?')) {
                window.location.href = 'cancel_reserve.php?id=' + reservationId;
            }
        }
    </script>
</body>
</html>

