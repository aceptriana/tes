<?php 
session_start(); // Start session at the very top
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

// Ensure session is started and user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data from the database based on user_id from session
$sql = "SELECT * FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);

// Check if SQL statement was successfully prepared
if ($stmt === false) {
    die("SQL statement error: " . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if user was found
if ($result->num_rows === 0) {
    die("User not found.");
}

// Fetch user data
$row = $result->fetch_assoc();

// Handle form submission for updating user
if (isset($_POST['update_user'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check if username already exists in the database
    $check_sql = "SELECT * FROM user WHERE username = ? AND user_id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('si', $username, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // If username already exists, show SweetAlert
        echo '<script>
                Swal.fire({
                    title: "Username Already Exists!",
                    text: "Please choose a different username.",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "edit-akun.php";
                    }
                });
              </script>';
    } else {
        // Hash password if it was changed
        $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $row['password'];

        // Prepare SQL statement to update user data
        $update_sql = "UPDATE user SET username = ?, password = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);

        // Check if SQL statement was successfully prepared
        if ($update_stmt === false) {
            die("SQL statement error: " . htmlspecialchars($conn->error));
        }

        $update_stmt->bind_param('ssi', $username, $hashed_password, $user_id);

        if ($update_stmt->execute()) {
            // If successful, store a success message in the session
            $_SESSION['update_success'] = true;
            // Redirect to avoid form resubmission
            header("Location: edit-akun.php");
            exit();
        } else {
            die("Error updating account.");
        }
    }
}

// Check for success message for update
if (isset($_SESSION['update_success'])) {
    $sweetAlertScript = '
        <script>
            Swal.fire({
                title: "Success!",
                text: "Account successfully updated!",
                icon: "success",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "index.php";
                }
            });
        </script>';
    unset($_SESSION['update_success']); // Clear the session variable
} else {
    $sweetAlertScript = '';
}
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit User Account</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Edit User</li>
                </ul>
            </div>
        </div>

        <!-- Form for Editing User -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="edit-akun.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank if you don't want to change">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="update_user">Update User Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<?php echo $sweetAlertScript; ?>

<?php include 'footer.php'; ?>
