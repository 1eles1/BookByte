<?php
session_start();
include '../../config/config.php';

// Check if user is logged in as librarian
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'librarian') {
    header('Location: ../../index.php');
    exit();
}

$success_message = '';
$error_message = '';
$user_data = null;
$personal_data = null;

// Get user ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_users.php?error=invalid_id');
    exit();
}

$user_id = (int) $_GET['id'];

// Get user data
try {
    $stmt = $db->prepare("SELECT * FROM tbllogininformation WHERE id = :id AND accounttype = 'student'");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        header('Location: manage_users.php?error=user_not_found');
        exit();
    }
    
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get personal information
    $stmt2 = $db->prepare("SELECT * FROM tbl_personal_information WHERE login_id = :id");
    $stmt2->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt2->execute();
    $personal_data = $stmt2->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    header('Location: manage_users.php?error=database_error');
    exit();
}

// Handle form submission
if (isset($_POST['update'])) {
    try {
        // Login Information
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $contact = trim($_POST['contact']);
        $password = trim($_POST['password']);
        
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
        
        // Check if email already exists (excluding current user)
        $checkStmt = $db->prepare("SELECT id FROM tbllogininformation WHERE email = :email AND id != :id");
        $checkStmt->bindParam(':email', $email);
        $checkStmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            $error_message = "Email already exists. Please use a different email.";
        } else {
            // Update login data
            if (!empty($password)) {
                $stmt = $db->prepare("UPDATE tbllogininformation 
                                     SET username = :username, email = :email, contact = :contact, password = :password 
                                     WHERE id = :id");
                $hashedPassword = md5($password);
                $stmt->bindParam(':password', $hashedPassword);
            } else {
                $stmt = $db->prepare("UPDATE tbllogininformation 
                                     SET username = :username, email = :email, contact = :contact 
                                     WHERE id = :id");
            }
            
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':contact', $contact);
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            
            // Update personal information
            if ($personal_data) {
                $stmt3 = $db->prepare("UPDATE tbl_personal_information 
                                      SET first_name = :first_name, middle_name = :middle_name, last_name = :last_name, 
                                          gender = :gender, birthdate = :birthdate, address = :address, 
                                          student_id_number = :student_id_number, course = :course, year_level = :year_level
                                      WHERE login_id = :login_id");
            } else {
                $stmt3 = $db->prepare("INSERT INTO tbl_personal_information 
                                      (login_id, first_name, middle_name, last_name, gender, birthdate, address, 
                                       student_id_number, course, year_level) 
                                      VALUES (:login_id, :first_name, :middle_name, :last_name, :gender, :birthdate, :address, 
                                              :student_id_number, :course, :year_level)");
            }
            
            $stmt3->bindParam(':first_name', $firstname);
            $stmt3->bindParam(':middle_name', $middlename);
            $stmt3->bindParam(':last_name', $lastname);
            $stmt3->bindParam(':gender', $gender);
            $stmt3->bindParam(':birthdate', $birthdate);
            $stmt3->bindParam(':address', $address);
            $stmt3->bindParam(':student_id_number', $student_id_number);
            $stmt3->bindParam(':course', $course);
            $stmt3->bindParam(':year_level', $year_level);
            
            if (!$personal_data) {
                $stmt3->bindParam(':login_id', $user_id, PDO::PARAM_INT);
            } else {
                $stmt3->bindParam(':login_id', $user_id, PDO::PARAM_INT);
            }
            
            if ($stmt3->execute()) {
                $success_message = "Student updated successfully!";
                // Refresh data
                $stmt = $db->prepare("SELECT * FROM tbllogininformation WHERE id = :id");
                $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $stmt2 = $db->prepare("SELECT * FROM tbl_personal_information WHERE login_id = :id");
                $stmt2->bindParam(':id', $user_id, PDO::PARAM_INT);
                $stmt2->execute();
                $personal_data = $stmt2->fetch(PDO::FETCH_ASSOC);
            } else {
                $error_message = "Failed to update student. Please try again.";
            }
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Student | Library Management System</title>
  <link rel="stylesheet" href="../../assets/css/sidebar.css">
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
                    <a href="../index.php" class="nav-item">
                        <i class="fas fa-tachometer-alt"></i> Overview
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Student Management</div>
                    <a href="manage_users.php" class="nav-item">
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

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <div class="top-header">
                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="header-title">Edit Student</h1>
                <div class="header-actions">
                    <a href="manage_users.php" class="btn btn-primary">
                        <i class="fas fa-users"></i> Back to List
                    </a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <div class="content-card">
                    <div class="content-header">
                        <h3>Edit Student Information</h3>
                    </div>
                    <div class="content-body">
                        <?php if ($success_message): ?>
                            <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error_message): ?>
                            <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="edit_user.php?id=<?php echo $user_id; ?>">
                            <h4>Login Information</h4>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" 
                                       value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Contact Number</label>
                                <input type="text" name="contact" class="form-control" 
                                       value="<?php echo htmlspecialchars($user_data['contact']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="password" class="form-control" 
                                       placeholder="Enter new password">
                            </div>

                            <hr>
                            <h4>Personal Information</h4>

                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="firstname" class="form-control" 
                                       value="<?php echo htmlspecialchars($personal_data['first_name'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Middle Name</label>
                                <input type="text" name="middlename" class="form-control" 
                                       value="<?php echo htmlspecialchars($personal_data['middle_name'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="lastname" class="form-control" 
                                       value="<?php echo htmlspecialchars($personal_data['last_name'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Birthdate</label>
                                <input type="date" name="birthdate" class="form-control" 
                                       value="<?php echo htmlspecialchars($personal_data['birthdate'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control" 
                                       value="<?php echo htmlspecialchars($personal_data['address'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control" required>
                                    <option value="">Select gender</option>
                                    <option value="Male" <?php echo (isset($personal_data['gender']) && $personal_data['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo (isset($personal_data['gender']) && $personal_data['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Student ID Number</label>
                                <input type="text" name="student_id_number" class="form-control" 
                                       value="<?php echo htmlspecialchars($personal_data['student_id_number'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Course</label>
                                <input type="text" name="course" class="form-control" 
                                       value="<?php echo htmlspecialchars($personal_data['course'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Year Level</label>
                                <select name="year_level" class="form-control" required>
                                    <option value="">Select year level</option>
                                    <option value="1" <?php echo (isset($personal_data['year_level']) && $personal_data['year_level'] === '1') ? 'selected' : ''; ?>>1st Year</option>
                                    <option value="2" <?php echo (isset($personal_data['year_level']) && $personal_data['year_level'] === '2') ? 'selected' : ''; ?>>2nd Year</option>
                                    <option value="3" <?php echo (isset($personal_data['year_level']) && $personal_data['year_level'] === '3') ? 'selected' : ''; ?>>3rd Year</option>
                                    <option value="4" <?php echo (isset($personal_data['year_level']) && $personal_data['year_level'] === '4') ? 'selected' : ''; ?>>4th Year</option>
                                </select>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" name="update" class="btn btn-success">
                                    <i class="fas fa-save"></i> Update Student
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
        // Mobile menu toggle
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('open');
        });
    </script>
</body>
</html>