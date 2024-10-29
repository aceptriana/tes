<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Fetch data for financial summaries
$today_income = $today_expense = $monthly_income = $monthly_expense = $yearly_income = $yearly_expense = $total_income = $total_expense = 0;
$today = date('Y-m-d');

// Fetch transaction data for financial summaries
$query = "SELECT * FROM transactions";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $nominal = $row['nominal'];
    $jenis = $row['jenis'];
    $tanggal = $row['tanggal'];

    if ($jenis === 'Pemasukan') {
        $total_income += $nominal;
        if ($tanggal === $today) $today_income += $nominal;
        if (date('Y-m', strtotime($tanggal)) === date('Y-m')) $monthly_income += $nominal;
        if (date('Y', strtotime($tanggal)) === date('Y')) $yearly_income += $nominal;
    } elseif ($jenis === 'Pengeluaran') {
        $total_expense += $nominal;
        if ($tanggal === $today) $today_expense += $nominal;
        if (date('Y-m', strtotime($tanggal)) === date('Y-m')) $monthly_expense += $nominal;
        if (date('Y', strtotime($tanggal)) === date('Y')) $yearly_expense += $nominal;
    }
}

// Fetch transaction verification status counts for customers
$sql_customer_verification = "SELECT status, COUNT(*) as total FROM transaksi_barang GROUP BY status";
$result_customer_verification = $conn->query($sql_customer_verification);
$customer_verification = ['Menunggu' => 0, 'Ditolak' => 0, 'Disetujui' => 0];

while ($row = $result_customer_verification->fetch_assoc()) {
    $customer_verification[$row['status']] = $row['total'];
}

// Fetch transaction verification status counts for pre-orders
$sql_preorder_verification = "SELECT status_manajemen, COUNT(*) as total FROM transaksi_pre_order GROUP BY status_manajemen";
$result_preorder_verification = $conn->query($sql_preorder_verification);
$preorder_verification = ['Menunggu' => 0, 'Ditolak' => 0, 'Disetujui' => 0];

while ($row = $result_preorder_verification->fetch_assoc()) {
    $preorder_verification[$row['status_manajemen']] = $row['total'];
}

// Fetch data for chart (monthly income/expense data)
$incomeData = array_fill(0, 12, 0);
$expenseData = array_fill(0, 12, 0);
$query = "SELECT MONTH(tanggal) AS month, jenis, SUM(nominal) AS total 
          FROM transactions 
          WHERE YEAR(tanggal) = YEAR(CURRENT_DATE) 
          GROUP BY month, jenis";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $month = $row['month'] - 1;
    if ($row['jenis'] === 'Pemasukan') {
        $incomeData[$month] = $row['total'];
    } else {
        $expenseData[$month] = $row['total'];
    }
}

$incomeDataJson = json_encode($incomeData);
$expenseDataJson = json_encode($expenseData);

?>

<main class="nxl-container">
    <div class="nxl-content">
        <!-- Financial Summary -->
        <div class="main-content">
            <h5>Financial Summary</h5>
            <div class="row">
                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h4>Rp <?php echo number_format($today_income, 0, ',', '.'); ?></h4>
                            <p>Pemasukan Hari Ini</p>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h4>Rp <?php echo number_format($today_expense, 0, ',', '.'); ?></h4>
                            <p>Pengeluaran Hari Ini</p>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h4>Rp <?php echo number_format($monthly_income, 0, ',', '.'); ?></h4>
                            <p>Pemasukan Bulan Ini</p>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <h4>Rp <?php echo number_format($monthly_expense, 0, ',', '.'); ?></h4>
                            <p>Pengeluaran Bulan Ini</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Verification Summary -->
            <h5>Verifikasi Transaksi</h5>
            <div class="row">
                <!-- Customer Transactions Verification -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Verifikasi Transaksi Customer</div>
                        <div class="card-body">
                            <ul>
                                <li>Total Menunggu: <?php echo $customer_verification['Menunggu']; ?></li>
                                <li>Total Ditolak: <?php echo $customer_verification['Ditolak']; ?></li>
                                <li>Total Disetujui: <?php echo $customer_verification['Disetujui']; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Preorder Transactions Verification -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Verifikasi Preorder</div>
                        <div class="card-body">
                            <ul>
                                <li>Total Menunggu: <?php echo $preorder_verification['Menunggu']; ?></li>
                                <li>Total Ditolak: <?php echo $preorder_verification['Ditolak']; ?></li>
                                <li>Total Disetujui: <?php echo $preorder_verification['Disetujui']; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <h5>Grafik Data Pemasukan & Pengeluaran Per Bulan</h5>
            <div class="card">
                <div class="card-body">
                    <canvas id="payment-records-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var ctx = document.getElementById('payment-records-chart').getContext('2d');
var paymentChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        datasets: [{
            label: 'Pemasukan',
            data: <?php echo $incomeDataJson; ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }, {
            label: 'Pengeluaran',
            data: <?php echo $expenseDataJson; ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.6)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include 'footer.php'; ?>
