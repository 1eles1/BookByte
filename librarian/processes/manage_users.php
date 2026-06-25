<?php
session_start();
include '../../config/config.php';

// ✅ Allow only librarians
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'librarian') {
  header('Location: ../../index.php');
  exit();
}

// ✅ Feedback message variables
$message = '';
$type = ''; // success or error

// ✅ Handle feedback messages
if (isset($_GET['msg'])) {
  if ($_GET['msg'] === 'archived') {
    $user = $_GET['user'] ?? 'Student';
    $message = "Student '{$user}' has been archived successfully!";
    $type = 'success';
  }
}

if (isset($_GET['error'])) {
  $errors = [
    'archive_failed' => 'Failed to archive student. Please try again.',
    'user_not_found' => 'Student not found.',
    'invalid_id' => 'Invalid student ID.',
    'database_error' => 'Database error occurred.',
  ];
  $message = $errors[$_GET['error']] ?? 'An error occurred.';
  $type = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Students | Library Management System</title>
  <link rel="stylesheet" href="../../assets/css/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <div class="sidebar-layout">

    <!-- ✅ Sidebar -->
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
          <a href="manage_users.php" class="nav-item active">
            <i class="fas fa-users"></i> Manage Students
          </a>
          <a href="add_user.php" class="nav-item">
            <i class="fas fa-user-plus"></i> Add Student
          </a>
        </div>

        <div class="nav-section">
          <div class="nav-section-title">Book Management</div>
          <a href="../processesss/add.php" class="nav-item">
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

    <!-- ✅ Main Content -->
    <div class="main-content">
      <div class="top-header">
        <button class="mobile-menu-btn"><i class="fas fa-bars"></i></button>
        <h1 class="header-title">Manage Students</h1>
        <div class="header-actions">
          <a href="add_user.php" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Add Student
          </a>
        </div>
      </div>

      <div class="content-area">
        <div class="content-card">
          <div class="content-header">
            <h3>All Students</h3>
          </div>

          <div class="content-body">
            <!-- ✅ Feedback Message -->
            <?php if ($message): ?>
              <div style="
                background: <?= $type === 'success' ? '#d4edda' : '#f8d7da' ?>;
                color: <?= $type === 'success' ? '#155724' : '#721c24' ?>;
                padding: 12px; border-radius: 8px; margin-bottom: 20px;
                border: 1px solid <?= $type === 'success' ? '#c3e6cb' : '#f5c6cb' ?>;">
                <i class="fas <?= $type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($message) ?>
              </div>
            <?php endif; ?>

            <!-- ✅ Table -->
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
                    $stmt = $db->query("SELECT * FROM tbllogininformation WHERE accounttype = 'student' ORDER BY datecreated DESC");
                    if ($stmt->rowCount() > 0) {
                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $statusClass = $row['remarks'] === 'active' ? 'success' : 'danger';
                        $statusText = ucfirst($row['remarks']);
                        $dateCreated = date('M d, Y', strtotime($row['datecreated']));
                        ?>
                        <tr>
                          <td><?= $row['id'] ?></td>
                          <td><?= htmlspecialchars($row['username']) ?></td>
                          <td><?= htmlspecialchars($row['email']) ?></td>
                          <td><?= htmlspecialchars($row['contact']) ?></td>
                          <td>
                            <span class="btn btn-<?= $statusClass ?>" style="padding: 4px 8px; font-size: 0.8rem;">
                              <?= $statusText ?>
                            </span>
                          </td>
                          <td><?= $dateCreated ?></td>
                          <td>
                            <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-primary" style="padding: 4px 8px; font-size: 0.8rem; margin-right: 5px;">
                              <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="archive.php?id=<?= $row['id'] ?>" 
                               class="btn btn-danger" 
                               style="padding: 4px 8px; font-size: 0.8rem;" 
                               onclick="return confirm('Are you sure you want to archive this student?')">
                              <i class="fas fa-archive"></i> Archive
                            </a>
                          </td>
                        </tr>
                        <?php
                      }
                    } else {
                      echo "<tr><td colspan='7' class='text-center'>No students found. <a href='add_user.php'>Add the first student</a></td></tr>";
                    }
                  } catch (Exception $e) {
                    echo "<tr><td colspan='7' class='text-center'>Error loading students: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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
    // ✅ Mobile menu toggle
    document.querySelector('.mobile-menu-btn').addEventListener('click', () => {
      document.querySelector('.sidebar').classList.toggle('open');
    });
  </script>
</body>
</html>
