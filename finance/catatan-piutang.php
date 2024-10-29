<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php'; // Include header
include 'navbar.php'; // Include navigation bar

// Fungsi untuk format Rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 2, ',', '.');
}

// Handle search
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql = "SELECT p.*, c.customer_name 
            FROM piutang p 
            LEFT JOIN customers c ON p.customer_id = c.customer_id 
            WHERE p.kode_piutang LIKE ? OR p.keterangan LIKE ? OR c.customer_name LIKE ?";
    $search_value = "%" . $search_query . "%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $search_value, $search_value, $search_value);
} else {
    $sql = "SELECT p.*, c.customer_name 
            FROM piutang p 
            LEFT JOIN customers c ON p.customer_id = c.customer_id";
    $stmt = $conn->prepare($sql);
}

// Check for errors in preparing the statement
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Execute the statement
if (!$stmt->execute()) {
    die("Error executing query: " . $stmt->error);
}

$result = $stmt->get_result();
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Catatan Piutang</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Data Piutang</li>
                </ul>
            </div>
        </div>

        <!-- Form to Add New Receivable -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="proses-catatan-piutang.php" method="POST">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="nominal" class="form-label">Nominal</label>
                                <input type="number" class="form-control" id="nominal" name="nominal" step="0.01" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="jenis_piutang" class="form-label">Jenis Piutang</label>
                                <select class="form-control" id="jenis_piutang" name="jenis_piutang" required>
                                    <option value="">Pilih Jenis Piutang</option>
                                    <option value="Piutang Sahara">Piutang Sahara</option>
                                    <option value="Piutang Macet">Piutang Macet</option>
                                    <option value="Piutang Container">Piutang Container</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="customer_id" class="form-label">Customer</label>
                                <select class="form-control" id="customer_id" name="customer_id" required>
                                    <option value="">Pilih Customer</option>
                                    <?php
                                    // Fetch customers from the database
                                    $customer_query = "SELECT customer_id, customer_name FROM customers";
                                    $customer_result = $conn->query($customer_query);
                                    while ($customer_row = $customer_result->fetch_assoc()) {
                                        echo "<option value='{$customer_row['customer_id']}'>{$customer_row['customer_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" name="add_piutang">Tambah Piutang</button>
                    </form>
                </div>
            </div>

            <!-- Search Receivable -->
            <div class="card mt-3">
                <div class="card-body">
                    <form action="catatan-piutang.php" method="GET">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="search" class="form-label">Cari Piutang</label>
                                <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info">Cari</button>
                    </form>
                </div>
            </div>

            <!-- Receivable Table -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Piutang</th>
                                    <th>Nama Customer</th> <!-- Column for customer name -->
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Jenis Piutang</th>
                                    <th>Nominal</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>{$row['kode_piutang']}</td>
                                            <td>{$row['customer_name']}</td>
                                            <td>{$row['tanggal']}</td>
                                            <td>{$row['keterangan']}</td>
                                            <td>{$row['jenis_piutang']}</td>
                                            <td>" . formatRupiah($row['nominal']) . "</td>
                                            <td>
                                                <a href='bayar-piutang.php?id={$row['kode_piutang']}' class='btn btn-warning btn-sm'>Bayar</a>
                                                <a href='delete-piutang.php?id={$row['kode_piutang']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus piutang ini?\");'>Hapus</a>
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
