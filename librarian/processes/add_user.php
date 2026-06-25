<?php
session_start();
include '../../config/config.php';

// ✅ Allow only librarians
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'librarian') {
  header('Location: ../../index.php');
  exit();
}

// ✅ Messages for feedback
$message = '';
$type = ''; // success or error

if (isset($_POST['submit'])) {
  try {
    // Login Information
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = md5($_POST['password']);
    $accounttype = 'student';
    $remarks = 'active';
    $datecreated = date('Y-m-d H:i:s');

    // Personal Information
    $firstname = trim($_POST['firstname']);
    $middlename = trim($_POST['middlename']);
    $lastname = trim($_POST['lastname']);
    $birthdate = trim($_POST['birthdate']);
    $address = trim($_POST['address']);
    $gender = trim($_POST['gender']);
    $student_id_number = trim($_POST['student_id_number']);
    $course = trim($_POST['course']);
    $year_level = trim($_POST['year_level']);

    // Check if email exists
    $checkStmt = $db->prepare("SELECT id FROM tbllogininformation WHERE email = :email");
    $checkStmt->bindParam(':email', $email);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
      $message = "Email already exists. Please use another email.";
      $type = "error";
    } else {
      // Insert Login Data
      $stmt = $db->prepare("
        INSERT INTO tbllogininformation 
        (username, email, password, accounttype, remarks, datecreated, contact)
        VALUES (:username, :email, :password, :accounttype, :remarks, :datecreated, :contact)
      ");
      
      $stmt->execute([
        ':username'     => $username,
        ':email'        => $email,
        ':password'     => $password,
        ':accounttype'  => $accounttype,
        ':remarks'      => $remarks,
        ':datecreated'  => $datecreated,
        ':contact'      => $contact
      ]);

      // Get new login_id
      $login_id = $db->lastInsertId();

      // Insert Personal Information
      $stmt2 = $db->prepare("
        INSERT INTO tbl_personal_information 
        (login_id, first_name, middle_name, last_name, gender, birthdate, address, student_id_number, course, year_level) 
        VALUES (:login_id, :first_name, :middle_name, :last_name, :gender, :birthdate, :address, :student_id_number, :course, :year_level)
      ");

      $stmt2->execute([
        ':login_id'           => $login_id,
        ':first_name'         => $firstname,
        ':middle_name'        => $middlename,
        ':last_name'          => $lastname,
        ':gender'             => $gender,
        ':birthdate'          => $birthdate,
        ':address'            => $address,
        ':student_id_number'  => $student_id_number,
        ':course'             => $course,
        ':year_level'         => $year_level
      ]);

      $message = "Student account created successfully!";
      $type = "success";
      $_POST = array(); // Clear form
    }

  } catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
    $type = "error";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Student | Library Management System</title>
  <link rel="stylesheet" href="../../assets/css/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <div class="sidebar-layout">

    <!-- ✅ Sidebar (unchanged) -->
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
          <a href="manage_users.php" class="nav-item">
            <i class="fas fa-users"></i> Manage Students
          </a>
          <a href="add_user.php" class="nav-item active">
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
        <h1 class="header-title">Add New Student</h1>
        <div class="header-actions">
          <a href="manage_users.php" class="btn btn-primary">
            <i class="fas fa-users"></i> Back to List
          </a>
        </div>
      </div>

      <div class="content-area">
        <div class="content-card">
          <div class="content-header">
            <h3>Create Student Account</h3>
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
                <?= $message ?>
              </div>
            <?php endif; ?>

            <!-- ✅ Form -->
            <form method="POST" action="">
              <h4>Login Information</h4>
              <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required
                  value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
              </div>

              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required
                  value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
              </div>

              <div class="form-group">
                <label>Contact</label>
                <input type="text" name="contact" class="form-control" required
                  value="<?= isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : '' ?>">
              </div>

              <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>

              <hr>
              <h4>Personal Information</h4>

              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="firstname" class="form-control" required
                  value="<?= isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : '' ?>">
              </div>

              <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="middlename" class="form-control"
                  value="<?= isset($_POST['middlename']) ? htmlspecialchars($_POST['middlename']) : '' ?>">
              </div>

              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lastname" class="form-control" required
                  value="<?= isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : '' ?>">
              </div>

              <div class="form-group">
                <label>Birthdate</label>
                <input type="date" name="birthdate" class="form-control" required
                  value="<?= isset($_POST['birthdate']) ? htmlspecialchars($_POST['birthdate']) : '' ?>">
              </div>

              <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control" required
                  value="<?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?>">
              </div>

              <div class="form-group">
                <label>Gender</label>
                <select name="gender" class="form-control" required>
                  <option value="">Select gender</option>
                  <option value="Male" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
                  <option value="Female" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
                </select>
              </div>

              <div class="form-group">
                <label>Student ID Number</label>
                <input type="text" name="student_id_number" class="form-control" required
                  value="<?= isset($_POST['student_id_number']) ? htmlspecialchars($_POST['student_id_number']) : '' ?>">
              </div>

              <div class="form-group">
                <label>Course</label>
                <input type="text" name="course" class="form-control" required
                  value="<?= isset($_POST['course']) ? htmlspecialchars($_POST['course']) : '' ?>">
              </div>

              <div class="form-group">
                <label>Year Level</label>
                <select name="year_level" class="form-control" required>
                  <option value="">Select year level</option>
                  <option value="1st Year" <?= (isset($_POST['year_level']) && $_POST['year_level'] === '1st Year') ? 'selected' : '' ?>>1st Year</option>
                  <option value="2nd Year" <?= (isset($_POST['year_level']) && $_POST['year_level'] === '2nd Year') ? 'selected' : '' ?>>2nd Year</option>
                  <option value="3rd Year" <?= (isset($_POST['year_level']) && $_POST['year_level'] === '3rd Year') ? 'selected' : '' ?>>3rd Year</option>
                  <option value="4th Year" <?= (isset($_POST['year_level']) && $_POST['year_level'] === '4th Year') ? 'selected' : '' ?>>4th Year</option>
                </select>
              </div>

              <div class="d-flex gap-2">
                <button type="submit" name="submit" class="btn btn-success">
                  <i class="fas fa-user-plus"></i> Create Student
                </button>
                <a href="manage_users.php" class="btn btn-primary">
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
    // ✅ Mobile menu toggle
    document.querySelector('.mobile-menu-btn').addEventListener('click', () => {
      document.querySelector('.sidebar').classList.toggle('open');
    });
  </script>
</body>
</html>
