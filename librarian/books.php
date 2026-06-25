<?php
session_start();
include '../config/config.php';

// Check if user is logged in as librarian
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'librarian') {
    header('Location: ../index.php');
    exit();
}

// Get all books
try {
    $books = $db->query("SELECT * FROM tbl_books ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $books = [];
}

// Get statistics
try {
    $totalBooks = $db->query("SELECT COUNT(*) as count FROM tbl_books")->fetch()['count'];
    $availableBooks = $db->query("SELECT COUNT(*) as count FROM tbl_books WHERE remarks='available'")->fetch()['count'];
    $unavailableBooks = $db->query("SELECT COUNT(*) as count FROM tbl_books WHERE remarks='unavailable'")->fetch()['count'];
} catch (Exception $e) {
    $totalBooks = $availableBooks = $unavailableBooks = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Books | Library Management System</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
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
                    <a href="index.php" class="nav-item">
                        <i class="fas fa-tachometer-alt"></i> Overview
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Student Management</div>
                    <a href="processes/manage_users.php" class="nav-item">
                        <i class="fas fa-users"></i> Manage Students
                    </a>
                    <a href="processes/add_user.php" class="nav-item">
                        <i class="fas fa-user-plus"></i> Add Student
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Book Management</div>
                    <a href="processesss/add.php" class="nav-item">
                        <i class="fas fa-book-medical"></i> Add Book
                    </a>
                    <a href="books.php" class="nav-item active">
                        <i class="fas fa-list"></i> All Books
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
                <h1 class="header-title">All Books</h1>
                <div class="header-actions">
                    <a href="processesss/add.php" class="btn btn-primary">
                        <i class="fas fa-book-medical"></i> Add Book
                    </a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Stats Overview -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3 class="stat-number"><?php echo $totalBooks; ?></h3>
                        <p class="stat-label">Total Books</p>
                    </div>
                    <div class="stat-card success">
                        <h3 class="stat-number"><?php echo $availableBooks; ?></h3>
                        <p class="stat-label">Available</p>
                    </div>
                    <div class="stat-card danger">
                        <h3 class="stat-number"><?php echo $unavailableBooks; ?></h3>
                        <p class="stat-label">Unavailable</p>
                    </div>
                </div>

                <!-- Books List -->
                <div class="content-card">
                    <div class="content-header">
                        <h3>Books List (<?php echo count($books); ?> books)</h3>
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
                                    <?php if (count($books) > 0): ?>
                                        <?php foreach ($books as $book): ?>
                                            <?php
                                            $statusClass = ($book['remarks'] === 'available') ? 'success' : 'danger';
                                            ?>
                                            <tr>
                                                <td><?php echo $book['id']; ?></td>
                                                <td><?php echo htmlspecialchars($book['name']); ?></td>
                                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                                <td><?php echo htmlspecialchars($book['quantity'] ?? 0); ?></td>
                                                <td>
                                                    <span class="btn btn-<?php echo $statusClass; ?>" style="padding: 4px 8px; font-size: 0.8rem;">
                                                        <?php echo ucfirst($book['remarks']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="processesss/edit.php?id=<?php echo $book['id']; ?>" class="btn btn-primary" style="padding: 4px 8px; font-size: 0.8rem; margin-right: 5px;">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="processesss/delete.php?id=<?php echo $book['id']; ?>" class="btn btn-danger" style="padding: 4px 8px; font-size: 0.8rem;" 
                                                       onclick="return confirm('Are you sure you want to delete this book?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                No books found. <a href="processesss/add.php">Add the first book</a>
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

