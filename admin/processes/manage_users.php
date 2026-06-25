<?php
session_start();
include '../../config/config.php';

// Check if user is logged in as admin
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Librarians | Library Management System</title>
  <link rel="stylesheet" href="../../assets/css/sidebar.css">
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
                    <a href="../index.php" class="nav-item">
                        <i class="fas fa-tachometer-alt"></i> Overview
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">User Management</div>
                    <a href="manage_users.php" class="nav-item active">
                        <i class="fas fa-users"></i> Manage Librarians
                    </a>
                    <a href="add_user.php" class="nav-item">
                        <i class="fas fa-user-plus"></i> Add Librarian
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
                <h1 class="header-title">Manage Librarians</h1>
                <div class="header-actions">
                    <a href="add_user.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add Librarian
                    </a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <div class="content-card">
                    <div class="content-header">
                        <h3>All Librarians</h3>
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
                                        <th>Date Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $stmt = $db->query("SELECT * FROM tbllogininformation WHERE accounttype = 'librarian' ORDER BY datecreated DESC");
                                        if ($stmt->rowCount() > 0) {
                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $statusClass = $row['remarks'] === 'active' ? 'success' : 'danger';
                                                $statusText = ucfirst($row['remarks']);
                                                $dateCreated = date('M d, Y', strtotime($row['datecreated']));
                                                echo "<tr>";
                                                echo "<td>{$row['id']}</td>";
                                                echo "<td>{$row['username']}</td>";
                                                echo "<td>{$row['email']}</td>";
                                                echo "<td>{$row['contact']}</td>";
                                                echo "<td><span class='btn btn-{$statusClass}' style='padding: 4px 8px; font-size: 0.8rem;'>{$statusText}</span></td>";
                                                echo "<td>{$dateCreated}</td>";
                                                echo "<td>
                                                        <a href='edit_user.php?id={$row['id']}' class='btn btn-primary' style='padding: 4px 8px; font-size: 0.8rem; margin-right: 5px;'>
                                                            <i class='fas fa-edit'></i> Edit
                                                        </a> 
                                                        <a href='archive.php?id={$row['id']}' class='btn btn-danger' style='padding: 4px 8px; font-size: 0.8rem;' onclick='return confirm(\"Are you sure you want to archive this librarian?\")'>
                                                            <i class='fas fa-archive'></i> Archive
                                                        </a>
                                                      </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='7' class='text-center'>No librarians found. <a href='add_user.php'>Add the first librarian</a></td></tr>";
                                        }
                                    } catch (Exception $e) {
                                        echo "<tr><td colspan='7' class='text-center'>Error loading librarians: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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