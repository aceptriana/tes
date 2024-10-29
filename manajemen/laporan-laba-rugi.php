<?php
include '../config/db.php'; // Include your database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Initialize variables
$income = [];
$expenses = [];

// Define the account codes for income and expenses
$income_codes = [4000, 4010]; // Pendapatan Penjualan and Pendapatan Operasional Lainnya
$expense_codes = [
    5000, 5010, 5020, 5030, 5040,
    5050, 5060, 5070, 5080, 5090,
    5100, 5110
];

// Initialize date variables
$start_date = date('Y-m-01'); // Default to the first of the month
$end_date = date('Y-m-t'); // Default to the last day of the month
$data_fetched = false; // Flag to check if data has been fetched

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = isset($_POST['start_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $_POST['start_date']))) : date('Y-m-01');
    $end_date = isset($_POST['end_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $_POST['end_date']))) : date('Y-m-t');
    $data_fetched = true; // Set the flag to true since data will be fetched

    // Fetch data from COA for income
    $income_sql = "SELECT c.kode_akun, c.nama_akun, COALESCE(SUM(t.nominal), 0) as total 
                   FROM coa c
                   LEFT JOIN transactions t ON c.kode_akun = t.kode_coa AND t.jenis = 'Pemasukan' AND t.tanggal BETWEEN '$start_date' AND '$end_date'
                   WHERE c.kode_akun IN (" . implode(',', $income_codes) . ")
                   GROUP BY c.kode_akun";
    $income_result = $conn->query($income_sql);

    if (!$income_result) {
        die("Error fetching income: " . $conn->error);
    }

    while ($row = $income_result->fetch_assoc()) {
        $income[] = $row;
    }

    // Fetch data from COA for expenses
    $expense_sql = "SELECT c.kode_akun, c.nama_akun, COALESCE(SUM(t.nominal), 0) as total 
                    FROM coa c
                    LEFT JOIN transactions t ON c.kode_akun = t.kode_coa AND t.jenis = 'Pengeluaran' AND t.tanggal BETWEEN '$start_date' AND '$end_date'
                    WHERE c.kode_akun IN (" . implode(',', $expense_codes) . ")
                    GROUP BY c.kode_akun";
    $expense_result = $conn->query($expense_sql);

    if (!$expense_result) {
        die("Error fetching expenses: " . $conn->error);
    }

    while ($row = $expense_result->fetch_assoc()) {
        $expenses[] = $row;
    }

    // Calculate totals
    $total_income = array_sum(array_column($income, 'total'));
    $total_expenses = array_sum(array_column($expenses, 'total'));
    $net_profit_loss = $total_income - $total_expenses;
}
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Laporan Laba Rugi</h5>
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
                                <a href="print-laporan-laba-rugi.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-secondary">Print</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if ($data_fetched): // Only display data if it has been fetched ?>
            <div class="main-content">
                <div class="card mt-4">
                    <div class="card-body">
                        <h6><strong>Pendapatan</strong></h6>
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
                                // Display income accounts
                                foreach ($income_codes as $code) {
                                    $income_entry = array_filter($income, function($item) use ($code) {
                                        return $item['kode_akun'] == $code;
                                    });
                                    $income_entry = reset($income_entry); // Get the first match

                                    echo "<tr>
                                            <td>{$code}</td>
                                            <td>" . ($income_entry['nama_akun'] ?? '-') . "</td>
                                            <td>Rp. " . number_format($income_entry['total'] ?? 0, 2, ',', '.') . ",-</td>
                                          </tr>";
                                }
                                ?>
                                <tr>
                                    <td colspan="2" class="text-right font-weight-bold">Total Pendapatan</td>
                                    <td class="font-weight-bold">Rp. <?php echo number_format($total_income, 2, ',', '.'); ?>,-</td>
                                </tr>
                            </tbody>
                        </table>

                        <h6 class="mt-4"><strong>Pengeluaran</strong></h6>
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
                                // Display expense accounts
                                foreach ($expense_codes as $code) {
                                    $expense_entry = array_filter($expenses, function($item) use ($code) {
                                        return $item['kode_akun'] == $code;
                                    });
                                    $expense_entry = reset($expense_entry); // Get the first match

                                    echo "<tr>
                                            <td>{$code}</td>
                                            <td>" . ($expense_entry['nama_akun'] ?? '-') . "</td>
                                            <td>Rp. " . number_format($expense_entry['total'] ?? 0, 2, ',', '.') . ",-</td>
                                          </tr>";
                                }
                                ?>
                                <tr>
                                    <td colspan="2" class="text-right font-weight-bold">Total Pengeluaran</td>
                                    <td class="font-weight-bold">Rp. <?php echo number_format($total_expenses, 2, ',', '.'); ?>,-</td>
                                </tr>
                            </tbody>
                        </table>

                        <h6 class="mt-4"><strong>Laba/Rugi Bersih</strong></h6>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Laba/Rugi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Rp. <?php echo number_format($net_profit_loss, 2, ',', '.'); ?>,-</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        <?php endif; // End of data fetched check ?>
    </div>
</main>

<script>
    function printReport() {
        window.print();
    }
</script>

<?php include 'footer.php'; // Include footer ?>
