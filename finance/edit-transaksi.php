<?php 
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

// Check if the ID is set in the URL
if (isset($_GET['id'])) {
    $transaction_id = $_GET['id'];

    // Prepare the SQL statement to fetch the transaction record
    $sql = "SELECT * FROM transactions WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the record exists
    if ($result->num_rows === 0) {
        header("Location: data-transaksi.php?status=not_found");
        exit();
    }

    // Fetch the existing data
    $row = $result->fetch_assoc();
} else {
    header("Location: data-transaksi.php?status=error");
    exit();
}

// Handle form submission for updating transaction
if (isset($_POST['update_transaction'])) {
    $tanggal = $_POST['tanggal'];
    $jenis = $_POST['jenis'];
    $kode_coa = $_POST['kode_coa'];
    $nominal = $_POST['nominal'];
    $keterangan = $_POST['keterangan'];
    $nama_saldo = $_POST['nama_saldo'];

    // Prepare the SQL statement to update the transaction record
    $update_sql = "UPDATE transactions SET tanggal = ?, jenis = ?, kode_coa = ?, nominal = ?, keterangan = ?, nama_saldo = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('sssdssi', $tanggal, $jenis, $kode_coa, $nominal, $keterangan, $nama_saldo, $transaction_id);

    if ($update_stmt->execute()) {
        header("Location: data-transaksi.php?status=updated");
        exit();
    } else {
        header("Location: data-transaksi.php?status=error");
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
                    <h5 class="m-b-10">Edit Transaksi</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Edit Transaksi</li>
                </ul>
            </div>
        </div>

        <!-- Form to Edit Transaction -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="edit-transaksi.php?id=<?php echo $transaction_id; ?>" method="POST">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo $row['tanggal']; ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <select class="form-control" id="jenis" name="jenis" required>
                                    <option value="Pemasukan" <?php echo ($row['jenis'] == 'Pemasukan') ? 'selected' : ''; ?>>Pemasukan</option>
                                    <option value="Pengeluaran" <?php echo ($row['jenis'] == 'Pengeluaran') ? 'selected' : ''; ?>>Pengeluaran</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="kode_coa" class="form-label">Kode COA</label>
                                <input type="text" class="form-control" id="kode_coa" name="kode_coa" value="<?php echo $row['kode_coa']; ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="nominal" class="form-label">Nominal</label>
                                <input type="number" class="form-control" id="nominal" name="nominal" value="<?php echo $row['nominal']; ?>" step="0.01" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="keterangan" name="keterangan" value="<?php echo $row['keterangan']; ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="nama_saldo" class="form-label">Nama Saldo</label>
                                <input type="text" class="form-control" id="nama_saldo" name="nama_saldo" value="<?php echo $row['nama_saldo']; ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="update_transaction">Perbarui Transaksi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
