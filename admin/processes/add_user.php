<?php
session_start();
include '../../config/config.php';

// Check if user is logged in as admin
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$success_message = '';
$error_message = '';

if (isset($_POST['submit'])) {
    try {
        // Login Information
        $username       = trim($_POST['username']);
        $email          = trim($_POST['email']);
        $contact        = trim($_POST['contact']);
        $password       = md5($_POST['password']);
        $accounttype    = 'librarian';
        $remarks        = 'active';
        $datecreated    = date('Y-m-d H:i:s');

        // Personal Information
        $firstname      = trim($_POST['firstname']);
        $middlename     = trim($_POST['middlename']);
        $lastname       = trim($_POST['lastname']);
        $birthdate      = trim($_POST['birthdate']);
        $address        = trim($_POST['address']);
        $gender         = trim($_POST['gender']);

        // Default librarian values (not a student)
        $student_id_number = "N/A";
        $course            = "N/A";
        $year_level        = "N/A";

        // Check if email exists
        $checkStmt = $db->prepare("SELECT id FROM tbllogininformation WHERE email = :email");
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            $error_message = "Email already exists. Please use another email.";
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

            $success_message = "Librarian account created successfully!";
            $_POST = array(); // Clear form
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
    <title>Add Librarian | Library Management System</title>

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
                <a href="manage_users.php" class="nav-item">
                    <i class="fas fa-users"></i> Manage Librarians
                </a>
                <a href="add_user.php" class="nav-item active">
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

        <div class="top-header">
            <h1 class="header-title">Add New Librarian</h1>
            <a href="manage_users.php" class="btn btn-primary">
                <i class="fas fa-users"></i> Back
            </a>
        </div>

        <div class="content-area">
            <div class="content-card">

                <div class="content-header"><h3>Create Librarian Account</h3></div>

                <div class="content-body">

<?php if ($success_message): ?>
    <div style="
        display: inline-block;
        background: #d4edda;
        color: #155724;
        padding: 8px 14px;
        border-radius: 6px;
        border: 1px solid #c3e6cb;
        margin-bottom: 10px;
        font-size: 14px;
    ">
        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
    </div><br>
<?php endif; ?>

<?php if ($error_message): ?>
    <div style="
        display: inline-block;
        background: #f8d7da;
        color: #721c24;
        padding: 8px 14px;
        border-radius: 6px;
        border: 1px solid #f5c6cb;
        margin-bottom: 10px;
        font-size: 14px;
    ">
        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
    </div><br>
<?php endif; ?>


                    <form method="POST">

                        <h4>Login Information</h4>
                        <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
                        <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
                        <div class="form-group"><label>Contact</label><input type="text" name="contact" required></div>
                        <div class="form-group"><label>Password</label><input type="password" name="password" required></div>

                        <hr>
                        <h4>Personal Information</h4>

                        <div class="form-group"><label>First Name</label><input type="text" name="firstname" required></div>
                        <div class="form-group"><label>Middle Name</label><input type="text" name="middlename"></div>
                        <div class="form-group"><label>Last Name</label><input type="text" name="lastname" required></div>
                        <div class="form-group"><label>Birthdate</label><input type="date" name="birthdate" required></div>
                        <div class="form-group"><label>Address</label><input type="text" name="address" required></div>

                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" required>
                                <option value="">Select gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <button type="submit" name="submit" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Create Librarian
                        </button>

                    </form>

                </div>

            </div>
        </div>

    </div>

</div>
</body>
</html>
