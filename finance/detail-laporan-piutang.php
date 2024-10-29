<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php'; // Include header
include 'navbar.php'; // Include navigation bar

// Function to format Rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 2, ',', '.');
}

// Fetch the customer_id from the URL
$customer_id = $_GET['customer_id'] ?? '';

if (!$customer_id) {
    die("Customer ID is required.");
}

// Fetch customer name for the specified customer
$sql_customer = "SELECT customer_name FROM customers WHERE customer_id = ?";
$stmt_customer = $conn->prepare($sql_customer);
$stmt_customer->bind_param('i', $customer_id);
$stmt_customer->execute();
$result_customer = $stmt_customer->get_result();

if ($result_customer->num_rows === 0) {
    die("Customer not found.");
}

$customer = $result_customer->fetch_assoc();
$customer_name = htmlspecialchars($customer['customer_name']);

// Fetch details for the specified customer
$sql_piutang = "SELECT p.kode_piutang, p.tanggal, p.nominal, p.keterangan 
                FROM piutang p 
                WHERE p.customer_id = ?";
$stmt_piutang = $conn->prepare($sql_piutang);
$stmt_piutang->bind_param('i', $customer_id);
$stmt_piutang->execute();
$result_piutang = $stmt_piutang->get_result();

// Calculate total receivables
$total_piutang = 0;

?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Detail Piutang untuk <?php echo $customer_name; ?></h5>
                </div>
            </div>
        </div>
        <div class="main-content">
        <!-- Table for displaying receivable details -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Kode Piutang</th>
                                <th>Tanggal</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($result_piutang->num_rows > 0) {
                            while ($row = $result_piutang->fetch_assoc()) {
                                $total_piutang += $row['nominal']; // Accumulate total
                                echo "<tr>
                                        <td>" . htmlspecialchars($row['kode_piutang']) . "</td>
                                        <td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>
                                        <td>" . formatRupiah($row['nominal']) . "</td>
                                        <td>" . htmlspecialchars($row['keterangan']) . "</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>Tidak ada detail piutang untuk customer ini.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <strong>Total Seluruh Piutang: <?php echo formatRupiah($total_piutang); ?></strong>
                </div>
                
                <a href="laporan-piutang.php" class="btn btn-primary mt-2">Kembali</a>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; // Include footer ?>
