<?php
// Start PHP with no preceding whitespace
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Initialize variables for feedback messages
$feedback = '';

// Check if the ID is provided in the URL
if (isset($_GET['id'])) {
    $kode_piutang = $_GET['id'];

    // Fetch the current record from the database
    $sql = "SELECT * FROM piutang WHERE kode_piutang = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $kode_piutang);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // If no record found, redirect to the list page
        header('Location: catatan-piutang.php?status=not_found');
        exit();
    }

    // Fetch the current values
    $row = $result->fetch_assoc();
    $tanggal = $row['tanggal'];
    $nominal = $row['nominal'];
    $keterangan = $row['keterangan'];
} else {
    // Redirect if no ID is provided
    header('Location: catatan-piutang.php?status=error');
    exit();
}

// Handle the form submission
if (isset($_POST['bayar_piutang'])) {
    $bayar_nominal = $_POST['bayar_nominal'];
    $new_nominal = $nominal - $bayar_nominal;
    
    // Check if the remaining balance is 0 or less, then mark it as paid
    $new_keterangan = ($new_nominal <= 0) ? 'lunas' : 'belum lunas';

    // Update the piutang record in the database
    $update_sql = "UPDATE piutang SET nominal = ?, keterangan = ?, updated_at = CURRENT_TIMESTAMP WHERE kode_piutang = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('dss', $new_nominal, $new_keterangan, $kode_piutang);

    if ($update_stmt->execute()) {
        $feedback = 'bayar_success'; // Set feedback message
    } else {
        $feedback = 'error'; // Set error message
    }
}
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Bayar Piutang</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Catatan Piutang</li>
                    <li class="breadcrumb-item">Bayar Piutang</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="bayar-piutang.php?id=<?php echo htmlspecialchars($kode_piutang); ?>" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">Tanggal Piutang</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nominal" class="form-label">Sisa Piutang</label>
                                <input type="number" class="form-control" id="nominal" name="nominal" value="<?php echo htmlspecialchars($nominal); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bayar_nominal" class="form-label">Jumlah Pembayaran</label>
                                <input type="number" class="form-control" id="bayar_nominal" name="bayar_nominal" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="keterangan" name="keterangan" value="<?php echo htmlspecialchars($keterangan); ?>" readonly>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="bayar_piutang">Bayar Piutang</button>
                    </form>

                    <!-- Feedback Handling -->
                    <?php if ($feedback == 'bayar_success') : ?>
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Pembayaran piutang berhasil!',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'catatan-piutang.php'; // Redirect after clicking OK
                                }
                            });
                        </script>
                    <?php elseif ($feedback == 'error') : ?>
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan!',
                                text: 'Kesalahan saat membayar piutang.',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'catatan-piutang.php'; // Redirect after clicking OK
                                }
                            });
                        </script>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
