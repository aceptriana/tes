<?php
include '../config/session.php'; // Check login session
include '../config/db.php'; // Include your database connection file
include 'header.php';
include 'navbar.php';

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Initialize variables
$pemasok = null;
$error_message = '';

// Fetch supplier data based on kode_pemasok
if (isset($_GET['kode_pemasok'])) {
    $kode_pemasok = sanitize_input($_GET['kode_pemasok']);
    $sql = "SELECT * FROM pemasok WHERE kode_pemasok = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        $error_message = "Error preparing statement: " . $conn->error;
    } else {
        $stmt->bind_param("s", $kode_pemasok);
        
        if (!$stmt->execute()) {
            $error_message = "Error executing statement: " . $stmt->error;
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $pemasok = $result->fetch_assoc();
            } else {
                $error_message = "Supplier not found.";
            }
        }
        $stmt->close();
    }
} else {
    $error_message = "Supplier code not provided.";
}
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Supplier</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Edit Supplier</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php elseif ($pemasok): ?>
                        <form action="process_edit_supplier.php" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="kode_pemasok" class="form-label">Kode Supplier</label>
                                    <input type="text" class="form-control" id="kode_pemasok" name="kode_pemasok" value="<?php echo sanitize_input($pemasok['kode_pemasok']); ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nama" class="form-label">Nama Supplier</label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo sanitize_input($pemasok['nama']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="kontak" class="form-label">Kontak Person</label>
                                    <input type="text" class="form-control" id="kontak" name="kontak" value="<?php echo sanitize_input($pemasok['kontak']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telepon" class="form-label">Telepon</label>
                                    <input type="tel" class="form-control" id="telepon" name="telepon" value="<?php echo sanitize_input($pemasok['telepon']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="wechat" class="form-label">We Chat (Opsional)</label>
                                    <input type="text" class="form-control" id="wechat" name="wechat" value="<?php echo sanitize_input($pemasok['wechat']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email (Opsional)</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo sanitize_input($pemasok['email']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?php echo sanitize_input($pemasok['alamat']); ?></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Supplier</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>