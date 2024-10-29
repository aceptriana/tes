<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Handle verification of pre-order
if (isset($_POST['verifikasi_prapesan'])) {
    $kode_prapesan = $_POST['kode_prapesan'];
    $status = $_POST['status']; // 'disetujui' or 'ditolak'

    // Update the status in the database
    $sql = "UPDATE prapesan SET status = ? WHERE kode_prapesan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $status, $kode_prapesan);

    if ($stmt->execute()) {
        // Insert into transaksi_pre_order
        $sql_insert = "INSERT INTO transaksi_pre_order (kode_prapesan, status_verifikasi) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param('ss', $kode_prapesan, $status);
        $stmt_insert->execute();

        header('Location: verifikasi_pre_order.php?status=updated');
    } else {
        header('Location: verifikasi_pre_order.php?status=error');
    }
}

// Handle verification of finance
if (isset($_POST['verifikasi_keuangan'])) {
    $kode_prapesan = $_POST['kode_prapesan'];
    $status_keuangan = $_POST['status_keuangan']; // 'disetujui' or 'ditolak'

    // Update the finance status in the database
    $sql = "UPDATE prapesan SET status_keuangan = ? WHERE kode_prapesan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $status_keuangan, $kode_prapesan);

    if ($stmt->execute()) {
        header('Location: verifikasi_pre_order.php?status=updated_keuangan');
    } else {
        header('Location: verifikasi_pre_order.php?status=error_keuangan');
    }
}

// Handle search for pre-order
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql = "SELECT * FROM prapesan WHERE kode_prapesan LIKE ? OR nama LIKE ?";
    $search_value = "%" . $search_query . "%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $search_value, $search_value);
} else {
    $sql = "SELECT * FROM prapesan";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Verifikasi Pre-Order</h5>
                </div>
            </div>
        </div>

        <!-- Form to Search Pre-Order -->
        <div class="main-content">
            <div class="card mt-3">
                <div class="card-body">
                    <form action="verifikasi_pre_order.php" method="GET">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="search" class="form-label">Cari Pre-Order</label>
                                <input type="text" class="form-control" id="search" name="search" value="<?php echo $search_query; ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info">Cari</button>
                    </form>
                </div>
            </div>

            <!-- Pre-Order Table -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Prapesan</th>
                                    <th>Nama</th>
                                    <th>Jenis</th>
                                    <th>Warna</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal Pesan</th>
                                    <th>Status</th>
                                    <th>Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>{$row['kode_prapesan']}</td>
                                            <td>{$row['nama']}</td>
                                            <td>{$row['jenis']}</td>
                                            <td>{$row['warna']}</td>
                                            <td>{$row['jumlah']}</td>
                                            <td>{$row['tanggal_pesan']}</td>
                                            <td>{$row['status']}</td>
                                            <td>
                                                <form action='verifikasi-preorder.php' method='POST'>
                                                    <input type='hidden' name='kode_prapesan' value='{$row['kode_prapesan']}'>
                                                    <select name='status' class='form-select' required>
                                                        <option value='Menunggu' ".($row['status'] == 'Menunggu' ? 'selected' : '').">Menunggu</option>
                                                        <option value='Ditolak' ".($row['status'] == 'Ditolak' ? 'selected' : '').">Ditolak</option>
                                                        <option value='Disetujui' ".($row['status'] == 'Disetujui' ? 'selected' : '').">Disetujui</option>
                                                    </select>
                                                    <button type='submit' class='btn btn-success btn-sm' name='verifikasi_prapesan'>Verifikasi</button>
                                                </form>
                                            </td>
                                          </tr>";
                                    $no++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
