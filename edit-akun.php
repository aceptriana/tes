<?php
include 'config/db.php'; // Include database connection
include 'config/session.php'; // Include session management
include 'finance/navbar.php';
include 'finance/navbar.php';

// Fetch user data based on the logged-in user
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch the current password from the database
    $sql = "SELECT password FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify if the old password is correct
    if (password_verify($old_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Handle file upload for updating profile image
            $user_image = null;
            $imageUpdated = false; // Track if image was updated
            if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] === UPLOAD_ERR_OK) {
                $imageTmpPath = $_FILES['user_image']['tmp_name'];
                $imageName = $_FILES['user_image']['name'];
                $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

                // Define allowed file extensions
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($imageExtension, $allowedExtensions)) {
                    // Define the target directory and file name
                    $uploadDirectory = '../assets/images/avatar/';
                    $newImageName = $user_id . '_' . time() . '.' . $imageExtension; // Unique file name
                    $imageDestPath = $uploadDirectory . $newImageName;

                    // Move the file to the desired location
                    if (move_uploaded_file($imageTmpPath, $imageDestPath)) {
                        $user_image = $newImageName;
                        $imageUpdated = true; // Set flag that the image was updated
                    } else {
                        echo "<div class='alert alert-danger'>Error uploading the image.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.</div>";
                }
            }

            // Update the password and user image in the database
            if ($imageUpdated) {
                $sql = "UPDATE user SET password = ?, user_image = ? WHERE user_id = ?";
            } else {
                $sql = "UPDATE user SET password = ? WHERE user_id = ?";
            }

            if ($stmt = $conn->prepare($sql)) {
                if ($imageUpdated) {
                    $stmt->bind_param('ssi', $hashed_password, $user_image, $user_id);
                } else {
                    $stmt->bind_param('si', $hashed_password, $user_id);
                }

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Profile updated successfully!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error updating profile: " . $stmt->error . "</div>";
                }
                $stmt->close();
            } else {
                echo "<div class='alert alert-danger'>Error preparing statement: " . $conn->error . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>New password and confirm password do not match.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Old password is incorrect.</div>";
    }
}

// Fetch the current user data for display in the form
$sql = "SELECT user_image FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<div class='alert alert-danger'>User data not found.</div>";
    exit;
}

$user_image_path = $user['user_image'] ? "../assets/images/avatar/" . $user['user_image'] : "img/default-avatar.png"; // Fallback to default image
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <h5 class="m-b-10">Edit Profile</h5>
        </div>

        <!-- Edit Profile Form -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="edit-profil.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="old_password" class="form-label">Old Password</label>
                                <input type="password" class="form-control" id="old_password" name="old_password" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="user_image" class="form-label">Update Profile Image</label>
                                <input type="file" class="form-control" id="user_image" name="user_image" accept="image/*">
                                <img src="<?php echo $user_image_path; ?>" alt="profile image" class="img-fluid mt-3" style="max-width: 100px;">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="update_profile">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'finance/footer.php'; ?>
