<?php 
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

// Check if the ID is set in the URL
if (isset($_GET['id'])) {
    $coa_id = $_GET['id'];

    // Prepare the SQL statement to fetch the COA record
    $sql = "SELECT * FROM coa WHERE coa_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $coa_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the record exists
    if ($result->num_rows === 0) {
        header("Location: data-coa.php?status=not_found");
        exit();
    }

    // Fetch the existing data
    $row = $result->fetch_assoc();
} else {
    header("Location: data-coa.php?status=error");
    exit();
}

// Handle form submission for updating COA
if (isset($_POST['update_coa'])) {
    $kode_akun = $_POST['kode_akun'];
    $nama_akun = $_POST['nama_akun'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];

    // Prepare the SQL statement to update the COA record
    $update_sql = "UPDATE coa SET kode_akun = ?, nama_akun = ?, kategori = ?, deskripsi = ? WHERE coa_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('ssssi', $kode_akun, $nama_akun, $kategori, $deskripsi, $coa_id);

    if ($update_stmt->execute()) {
        header("Location: data-coa.php?status=updated");
        exit();
    } else {
        header("Location: data-coa.php?status=error");
        exit();
    }
}
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Kategori COA</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Edit COA</li>
                </ul>
            </div>
        </div>

        <!-- Form to Edit COA Category -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="edit-coa.php?id=<?php echo $coa_id; ?>" method="POST">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="kode_akun" class="form-label">Kode Akun</label>
                                <input type="text" class="form-control" id="kode_akun" name="kode_akun" value="<?php echo $row['kode_akun']; ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="nama_akun" class="form-label">Nama Akun</label>
                                <input type="text" class="form-control" id="nama_akun" name="nama_akun" value="<?php echo $row['nama_akun']; ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="kategori" name="kategori" value="<?php echo $row['kategori']; ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <input type="text" class="form-control" id="deskripsi" name="deskripsi" value="<?php echo $row['deskripsi']; ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="update_coa">Perbarui Kategori COA</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
