<?php
session_start();
include '../config/config.php';

// Check if user is logged in as admin
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Get statistics
try {
    $totalLibrarians = $db->query("SELECT COUNT(*) as count FROM tbllogininformation WHERE accounttype='librarian' AND remarks='active'")->fetch()['count'];
    $totalStudents = $db->query("SELECT COUNT(*) as count FROM tbllogininformation WHERE accounttype='student' AND remarks='active'")->fetch()['count'];
    $totalBooks = $db->query("SELECT COUNT(*) as count FROM tbl_books WHERE remarks='available'")->fetch()['count'];
    $totalBorrowed = $db->query("SELECT COUNT(*) as count FROM tbl_books WHERE remarks='borrowed'")->fetch()['count'];
} catch (Exception $e) {
    $totalLibrarians = $totalStudents = $totalBooks = $totalBorrowed = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Library Management System</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="sidebar-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-book"></i> Library Admin</h2>
                <div class="user-info">Welcome, Admin</div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Dashboard</div>
                    <a href="index.php" class="nav-item active">
                        <i class="fas fa-tachometer-alt"></i> Overview
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">User Management</div>
                    <a href="processes/manage_users.php" class="nav-item">
                        <i class="fas fa-users"></i> Manage Librarians
                    </a>
                    <a href="processes/add_user.php" class="nav-item">
                        <i class="fas fa-user-plus"></i> Add Librarian
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
                <h1 class="header-title">Admin Dashboard</h1>
                <div class="header-actions">
                    <span class="text-secondary"><?php echo date('M d, Y'); ?></span>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Stats Overview -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3 class="stat-number"><?php echo $totalLibrarians; ?></h3>
                        <p class="stat-label">Active Librarians</p>
                    </div>
                    <div class="stat-card success">
                        <h3 class="stat-number"><?php echo $totalStudents; ?></h3>
                        <p class="stat-label">Total Students</p>
                    </div>
                    <div class="stat-card warning">
                        <h3 class="stat-number"><?php echo $totalBooks; ?></h3>
                        <p class="stat-label">Available Books</p>
                    </div>
                    <div class="stat-card danger">
                        <h3 class="stat-number"><?php echo $totalBorrowed; ?></h3>
                        <p class="stat-label">Borrowed Books</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="content-card">
                    <div class="content-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="content-body">
                        <div class="d-flex gap-2">
                            <a href="processes/add_user.php" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Add New Librarian
                            </a>
                            <a href="processes/manage_users.php" class="btn btn-success">
                                <i class="fas fa-users"></i> Manage Librarians
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Librarians -->
                <div class="content-card">
                    <div class="content-header">
                        <h3>Recent Librarians</h3>
                    </div>
                    <div class="content-body">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $stmt = $db->query("SELECT * FROM tbllogininformation WHERE accounttype='librarian' ORDER BY datecreated DESC LIMIT 5");
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $statusClass = $row['remarks'] === 'active' ? 'success' : 'danger';
                                            $statusText = ucfirst($row['remarks']);
                                            echo "<tr>";
                                            echo "<td>{$row['id']}</td>";
                                            echo "<td>{$row['username']}</td>";
                                            echo "<td>{$row['email']}</td>";
                                            echo "<td>{$row['contact']}</td>";
                                            echo "<td><span class='btn btn-{$statusClass}' style='padding: 4px 8px; font-size: 0.8rem;'>{$statusText}</span></td>";
                                            echo "<td>
                                                    <a href='processes/edit_user.php?id={$row['id']}' class='btn btn-primary' style='padding: 4px 8px; font-size: 0.8rem; margin-right: 5px;'>Edit</a>
                                                    <a href='processes/archive.php?id={$row['id']}' class='btn btn-danger' style='padding: 4px 8px; font-size: 0.8rem;'>Archive</a>
                                                  </td>";
                                            echo "</tr>";
                                        }
                                    } catch (Exception $e) {
                                        echo "<tr><td colspan='6' class='text-center'>No librarians found</td></tr>";
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
    </script>
</body>
</html>
