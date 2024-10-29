<?php
include '../config/db.php'; // Include your database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Initialize variables
$assets = [];
$liabilities = [];
$equities = [];

// Define the account codes for assets, liabilities, and equities
$asset_codes = [1000, 1010, 1020, 1030, 1040, 1050, 1060, 1070];
$liability_codes = [2000, 2010, 2020, 2030, 2040];
$equity_codes = [3000, 3010];

// Initialize date variables
$start_date = date('Y-m-01'); // Default to the first of the month
$end_date = date('Y-m-t'); // Default to the last day of the month
$data_fetched = false; // Flag to check if data has been fetched

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = isset($_POST['start_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $_POST['start_date']))) : date('Y-m-01');
    $end_date = isset($_POST['end_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $_POST['end_date']))) : date('Y-m-t');
    $data_fetched = true; // Set the flag to true since data will be fetched

    // Fetch data from COA for assets
    $asset_sql = "SELECT c.kode_akun, c.nama_akun, COALESCE(SUM(t.nominal), 0) as total 
                  FROM coa c
                  LEFT JOIN transactions t ON c.kode_akun = t.kode_coa AND t.jenis = 'Pemasukan' AND t.tanggal BETWEEN '$start_date' AND '$end_date'
                  WHERE c.kode_akun IN (" . implode(',', $asset_codes) . ")
                  GROUP BY c.kode_akun";
    $asset_result = $conn->query($asset_sql);

    if (!$asset_result) {
        die("Error fetching assets: " . $conn->error);
    }

    while ($row = $asset_result->fetch_assoc()) {
        $assets[] = $row;
    }

    // Fetch data from COA for liabilities
    $liability_sql = "SELECT c.kode_akun, c.nama_akun, COALESCE(SUM(t.nominal), 0) as total 
                      FROM coa c
                      LEFT JOIN transactions t ON c.kode_akun = t.kode_coa AND t.jenis = 'Pengeluaran' AND t.tanggal BETWEEN '$start_date' AND '$end_date'
                      WHERE c.kode_akun IN (" . implode(',', $liability_codes) . ")
                      GROUP BY c.kode_akun";
    $liability_result = $conn->query($liability_sql);

    if (!$liability_result) {
        die("Error fetching liabilities: " . $conn->error);
    }

    while ($row = $liability_result->fetch_assoc()) {
        $liabilities[] = $row;
    }

    // Fetch data from COA for equities
    $equity_sql = "SELECT c.kode_akun, c.nama_akun, COALESCE(SUM(t.nominal), 0) as total 
                    FROM coa c
                    LEFT JOIN transactions t ON c.kode_akun = t.kode_coa AND t.tanggal BETWEEN '$start_date' AND '$end_date'
                    WHERE c.kode_akun IN (" . implode(',', $equity_codes) . ")
                    GROUP BY c.kode_akun";
    $equity_result = $conn->query($equity_sql);

    if (!$equity_result) {
        die("Error fetching equities: " . $conn->error);
    }

    while ($row = $equity_result->fetch_assoc()) {
        $equities[] = $row;
    }

    // Calculate totals
    $total_assets = array_sum(array_column($assets, 'total'));
    $total_liabilities = array_sum(array_column($liabilities, 'total'));
    $total_equities = array_sum(array_column($equities, 'total'));
}
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Laporan Neraca</h5>
                </div>
            </div>
        </div>

<div class="main-content">
    <div class="card">
        <div class="card-body">
            <!-- Filter Form -->
            <form method="POST" class="mb-4">
                <div class="form-row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label for="start_date">Tanggal Mulai:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="<?php echo date('Y-m-d', strtotime($start_date)); ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="end_date">Tanggal Akhir:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="<?php echo date('Y-m-d', strtotime($end_date)); ?>" required>
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">Tampilkan</button>
                        <a href="print-laporan-neraca.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-secondary">Print</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


                    <?php if ($data_fetched): // Only display data if it has been fetched ?>
                    <!-- Table for displaying balance sheet -->
                    <div class="main-content">
                    <div class="card mt-4">
                        <div class="card-body">
                            <h6><strong>Aset</strong></h6>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kode Akun</th>
                                        <th>Nama Akun</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Display asset accounts
                                    foreach ($asset_codes as $code) {
                                        $asset = array_filter($assets, function($item) use ($code) {
                                            return $item['kode_akun'] == $code;
                                        });
                                        $asset = reset($asset); // Get the first match

                                        echo "<tr>
                                                <td>{$code}</td>
                                                <td>" . ($asset['nama_akun'] ?? '-') . "</td>
                                                <td>Rp. " . number_format($asset['total'] ?? 0, 2, ',', '.') . ",-</td>
                                              </tr>";
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="2" class="text-right font-weight-bold">Total Aset</td>
                                        <td class="font-weight-bold">Rp. <?php echo number_format($total_assets, 2, ',', '.'); ?>,-</td>
                                    </tr>
                                </tbody>
                            </table>

                            <h6 class="mt-4"><strong>Kewajiban</strong></h6>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kode Akun</th>
                                        <th>Nama Akun</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Display liability accounts
                                    foreach ($liability_codes as $code) {
                                        $liability = array_filter($liabilities, function($item) use ($code) {
                                            return $item['kode_akun'] == $code;
                                        });
                                        $liability = reset($liability); // Get the first match

                                        echo "<tr>
                                                <td>{$code}</td>
                                                <td>" . ($liability['nama_akun'] ?? '-') . "</td>
                                                <td>Rp. " . number_format($liability['total'] ?? 0, 2, ',', '.') . ",-</td>
                                              </tr>";
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="2" class="text-right font-weight-bold">Total Kewajiban</td>
                                        <td class="font-weight-bold">Rp. <?php echo number_format($total_liabilities, 2, ',', '.'); ?>,-</td>
                                    </tr>
                                </tbody>
                            </table>

                            <h6 class="mt-4"><strong>Ekuitas</strong></h6>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kode Akun</th>
                                        <th>Nama Akun</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Display equity accounts
                                    foreach ($equity_codes as $code) {
                                        $equity = array_filter($equities, function($item) use ($code) {
                                            return $item['kode_akun'] == $code;
                                        });
                                        $equity = reset($equity); // Get the first match

                                        echo "<tr>
                                                <td>{$code}</td>
                                                <td>" . ($equity['nama_akun'] ?? '-') . "</td>
                                                <td>Rp. " . number_format($equity['total'] ?? 0, 2, ',', '.') . ",-</td>
                                              </tr>";
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="2" class="text-right font-weight-bold">Total Ekuitas</td>
                                        <td class="font-weight-bold">Rp. <?php echo number_format($total_equities, 2, ',', '.'); ?>,-</td>
                                    </tr>
                                </tbody>
                            </table>

                            <h6 class="mt-4"><strong>Jumlah Kewajiban dan Ekuitas</strong></h6>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Total Kewajiban</th>
                                        <th>Total Ekuitas</th>
                                        <th>Total Kewajiban dan Ekuitas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Rp. <?php echo number_format($total_liabilities, 2, ',', '.'); ?>,-</td>
                                        <td>Rp. <?php echo number_format($total_equities, 2, ',', '.'); ?>,-</td>
                                        <td>Rp. <?php echo number_format($total_liabilities + $total_equities, 2, ',', '.'); ?>,-</td>
                                    </tr>
                                </tbody>
                            </table>

                            <h6 class="mt-4"><strong>Total Aset</strong></h6>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Total Aset</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Rp. <?php echo number_format($total_assets, 2, ',', '.'); ?>,-</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                    <?php endif; // End of data fetched check ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function printReport() {
        window.print();
    }
</script>

<?php include 'footer.php'; // Include footer ?>
