<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php'; // Include header
include 'navbar.php'; // Include navigation bar

// Fetch filter parameters if set
$customer_name = $_GET['customer_name'] ?? '';

// Fetch data
if ($customer_name) {
    $sql = "SELECT c.customer_name, c.customer_id, SUM(p.nominal) AS total_piutang, 
                   MAX(p.tanggal) AS last_payment_date
            FROM piutang p 
            JOIN customers c ON p.customer_id = c.customer_id 
            WHERE c.customer_name LIKE ? 
            GROUP BY c.customer_name, c.customer_id";
    $search_value = "%" . $customer_name . "%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $search_value);
} else {
    $sql = "SELECT c.customer_name, c.customer_id, SUM(p.nominal) AS total_piutang, 
                   MAX(p.tanggal) AS last_payment_date
            FROM piutang p 
            JOIN customers c ON p.customer_id = c.customer_id 
            GROUP BY c.customer_name, c.customer_id";
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

// Function to format Rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 2, ',', '.');
}

// Function to check if receivable is overdue
function isOverdue($last_payment_date) {
    if (!$last_payment_date) return false;
    $three_months_ago = date('Y-m-d', strtotime('-3 months'));
    
    // Debugging output
 

    return $last_payment_date < $three_months_ago;
}
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Laporan Piutang per Customer</h5>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="laporan-piutang.php" method="GET">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label">Nama Customer</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table for displaying receivables -->
        <div class="main-content">
            <div class="card mt-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Customer</th>
                                    <th>Total Piutang</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                $no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    $overdue = isOverdue($row['last_payment_date']);
                                    $status = $overdue ? 'Piutang Macet' : 'Normal';
                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>" . htmlspecialchars($row['customer_name']) . "</td>
                                            <td>" . formatRupiah($row['total_piutang']) . "</td>
                                            <td class='" . ($overdue ? 'text-danger' : 'text-success') . "'>{$status}</td>
                                            <td><a href='detail-laporan-piutang.php?customer_id=" . htmlspecialchars($row['customer_id']) . "' class='btn btn-info btn-sm'>Detail</a></td>
                                          </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>Tidak ada data.</td></tr>";
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

<?php include 'footer.php'; // Include footer ?>
