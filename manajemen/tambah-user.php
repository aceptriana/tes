<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = $_POST['role'];

    // Prepare the SQL statement (use `user` table)
    $sql = "INSERT INTO user (username, password, role) VALUES (?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('sss', $username, $password, $role);
        
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>User added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error adding user: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Error preparing statement: " . $conn->error . "</div>";
    }
}
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Tambah User</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Data User</li>
                </ul>
            </div>
        </div>

        <!-- Form to Add New User -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                <form action="tambah-user.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="admin">Admin</option>
                                    <option value="finance">Finance</option>
                                    <option value="manajemen">Manajemen</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
        <label for="user_image" class="form-label">Upload Profile Image</label>
        <input type="file" class="form-control" id="user_image" name="user_image" accept="image/*">
    </div>

    <button type="submit" class="btn btn-primary" name="add_user">Tambah User</button>
</form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
