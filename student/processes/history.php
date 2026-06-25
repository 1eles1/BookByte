<?php
session_start();
include '../../config/config.php';

// Check if user is logged in as student
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

$student_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

// Get all transactions (borrowed and reserved)
try {
    // Get borrowed books with book details
    $borrowedQuery = $db->prepare("
        SELECT 
            b.id as transaction_id,
            b.date_borrowed,
            b.date_returned,
            b.status,
            bk.name as book_name,
            bk.author,
            'borrowed' as transaction_type
        FROM tbl_borrowed b
        JOIN tbl_books bk ON b.book_id = bk.id
        WHERE b.student_id = ?
        ORDER BY b.date_borrowed DESC
    ");
    $borrowedQuery->execute([$student_id]);
    $borrowedTransactions = $borrowedQuery->fetchAll(PDO::FETCH_ASSOC);

    // Get reserved books with book details
    $reservedQuery = $db->prepare("
        SELECT 
            r.id as transaction_id,
            r.date_reserved,
            r.status,
            bk.name as book_name,
            bk.author,
            'reserved' as transaction_type
        FROM tbl_reservations r
        JOIN tbl_books bk ON r.book_id = bk.id
        WHERE r.student_id = ?
        ORDER BY r.date_reserved DESC
    ");
    $reservedQuery->execute([$student_id]);
    $reservedTransactions = $reservedQuery->fetchAll(PDO::FETCH_ASSOC);

    // Combine and sort all transactions
    $allTransactions = array_merge($borrowedTransactions, $reservedTransactions);
    
    // Sort by date (most recent first)
    usort($allTransactions, function($a, $b) {
        $dateA = $a['transaction_type'] === 'borrowed' ? $a['date_borrowed'] : $a['date_reserved'];
        $dateB = $b['transaction_type'] === 'borrowed' ? $b['date_borrowed'] : $b['date_reserved'];
        return strtotime($dateB) - strtotime($dateA);
    });

} catch (Exception $e) {
    $allTransactions = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History | Library Management System</title>
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
                    <a href="reserved.php" class="nav-item">
                        <i class="fas fa-bookmark"></i> My Reservations
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">History</div>
                    <a href="history.php" class="nav-item active">
                        <i class="fas fa-history"></i> Transaction History
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
                <h1 class="header-title">Transaction History</h1>
                <div class="header-actions">
                    <span class="text-secondary"><?php echo date('M d, Y'); ?></span>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Transaction History -->
                <div class="content-card">
                    <div class="content-header">
                        <h3>All Transactions (<?php echo count($allTransactions); ?> total)</h3>
                    </div>
                    <div class="content-body">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Book Title</th>
                                        <th>Author</th>
                                        <th>Transaction Date</th>
                                        <th>Return/Cancel Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($allTransactions) > 0): ?>
                                        <?php foreach ($allTransactions as $transaction): ?>
                                            <?php
                                            $statusClass = '';
                                            $statusText = '';
                                            
                                            if ($transaction['transaction_type'] === 'borrowed') {
                                                if ($transaction['status'] === 'borrowed') {
                                                    $statusClass = 'warning';
                                                    $statusText = 'Currently Borrowed';
                                                } else {
                                                    $statusClass = 'success';
                                                    $statusText = 'Returned';
                                                }
                                            } else {
                                                if ($transaction['status'] === 'reserved') {
                                                    $statusClass = 'info';
                                                    $statusText = 'Currently Reserved';
                                                } else {
                                                    $statusClass = 'secondary';
                                                    $statusText = 'Cancelled';
                                                }
                                            }
                                            
                                            $transactionDate = $transaction['transaction_type'] === 'borrowed' 
                                                ? date('M d, Y h:i A', strtotime($transaction['date_borrowed']))
                                                : date('M d, Y h:i A', strtotime($transaction['date_reserved']));
                                            
                                            $returnDate = $transaction['transaction_type'] === 'borrowed' 
                                                ? ($transaction['date_returned'] ? date('M d, Y h:i A', strtotime($transaction['date_returned'])) : '-')
                                                : '-';
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php if ($transaction['transaction_type'] === 'borrowed'): ?>
                                                        <span class="badge" style="background: #f59e0b; padding: 5px 10px; border-radius: 4px;">
                                                            <i class="fas fa-book-reader"></i> Borrow
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge" style="background: #3b82f6; padding: 5px 10px; border-radius: 4px;">
                                                            <i class="fas fa-bookmark"></i> Reserve
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($transaction['book_name']); ?></td>
                                                <td><?php echo htmlspecialchars($transaction['author']); ?></td>
                                                <td><?php echo $transactionDate; ?></td>
                                                <td><?php echo $returnDate; ?></td>
                                                <td>
                                                    <span class="btn btn-<?php echo $statusClass; ?>" style="padding: 4px 8px; font-size: 0.85rem;">
                                                        <?php echo $statusText; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                No transaction history yet.
                                            </td>
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
    </script>
</body>
</html>
