<?php
// Include database connection and session management
include '../config/db.php'; 
include '../config/session.php'; 
include 'header.php';
include 'navbar.php';
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="main-content">

            <!-- Fetch transactions data -->
            <?php
            // Initialize sums
            $today_income = 0;
            $today_expense = 0;
            $monthly_income = 0;
            $monthly_expense = 0;
            $yearly_income = 0;
            $yearly_expense = 0;
            $total_income = 0;
            $total_expense = 0;

            // Get today's date
            $today = date('Y-m-d');

            // Fetch transactions
            $query = "SELECT * FROM transactions";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                $nominal = $row['nominal'];
                $jenis = $row['jenis'];
                $tanggal = $row['tanggal'];

                // Calculate sums based on jenis
                if ($jenis === 'Pemasukan') {
                    $total_income += $nominal;

                    if ($tanggal === $today) {
                        $today_income += $nominal;
                    }
                    if (date('Y-m', strtotime($tanggal)) === date('Y-m')) {
                        $monthly_income += $nominal;
                    }
                    if (date('Y', strtotime($tanggal)) === date('Y')) {
                        $yearly_income += $nominal;
                    }
                } elseif ($jenis === 'Pengeluaran') {
                    $total_expense += $nominal;

                    if ($tanggal === $today) {
                        $today_expense += $nominal;
                    }
                    if (date('Y-m', strtotime($tanggal)) === date('Y-m')) {
                        $monthly_expense += $nominal;
                    }
                    if (date('Y', strtotime($tanggal)) === date('Y')) {
                        $yearly_expense += $nominal;
                    }
                }
            }
            ?>

            <!-- Row 1: Pemasukan -->
            <div class="row">
                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="text-start">
                                <h4 class="text-reset">Rp <?php echo number_format($today_income, 0, ',', '.'); ?></h4>
                                <p class="text-reset m-0">Pemasukan Hari Ini</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="text-start">
                                <h4 class="text-reset">Rp <?php echo number_format($monthly_income, 0, ',', '.'); ?></h4>
                                <p class="text-reset m-0">Pemasukan Bulan Ini</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="text-start">
                                <h4 class="text-reset">Rp <?php echo number_format($yearly_income, 0, ',', '.'); ?></h4>
                                <p class="text-reset m-0">Pemasukan Tahun Ini</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="text-start">
                                <h4 class="text-reset">Rp <?php echo number_format($total_income, 0, ',', '.'); ?></h4>
                                <p class="text-reset m-0">Seluruh Pemasukan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Pengeluaran -->
            <div class="row">
                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="text-start">
                                <h4 class="text-reset">Rp <?php echo number_format($today_expense, 0, ',', '.'); ?></h4>
                                <p class="text-reset m-0">Pengeluaran Hari Ini</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <div class="text-start">
                                <h4 class="text-reset">Rp <?php echo number_format($monthly_expense, 0, ',', '.'); ?></h4>
                                <p class="text-reset m-0">Pengeluaran Bulan Ini</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <div class="text-start">
                                <h4 class="text-reset">Rp <?php echo number_format($yearly_expense, 0, ',', '.'); ?></h4>
                                <p class="text-reset m-0">Pengeluaran Tahun Ini</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="text-start">
                                <h4 class="text-reset">Rp <?php echo number_format($total_expense, 0, ',', '.'); ?></h4>
                                <p class="text-reset m-0">Seluruh Pengeluaran</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Chart: Pemasukan & Pengeluaran -->
            <div class="col-xxl-8">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Grafik Data Pemasukan & Pengeluaran Per Bulan</h5>
                    </div>
                    <div class="card-body">
                        <div id="payment-records-chart"></div>
                    </div>
                </div>
            </div>

            <?php
            // Initialize arrays to hold income and expense data
            $incomeData = array_fill(0, 12, 0); // 12 months
            $expenseData = array_fill(0, 12, 0); // 12 months

            // Query to fetch transactions grouped by month and type
            $query = "SELECT MONTH(tanggal) AS month, jenis, SUM(nominal) AS total 
                      FROM transactions 
                      WHERE YEAR(tanggal) = YEAR(CURRENT_DATE)
                      GROUP BY month, jenis";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                $month = $row['month'] - 1; // Adjust for zero-indexed array
                if ($row['jenis'] === 'Pemasukan') {
                    $incomeData[$month] = $row['total'];
                } else {
                    $expenseData[$month] = $row['total'];
                }
            }

            // Convert PHP arrays to JavaScript arrays
            $incomeDataJson = json_encode($incomeData);
            $expenseDataJson = json_encode($expenseData);
            ?>

            <script>
            // Chart.js code to display income and expense data
            var ctx = document.getElementById('payment-records-chart').getContext('2d');
            var paymentRecordsChart = new Chart(ctx, {
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
        </div>
    </div>

    
</main>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="main-content">

            <!-- Section: Customer Debts Overview -->
            <div class="row">
                <div class="col-xxl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Overview of Customer Debts</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama Customer</th>
                                        <th>Total Piutang (Rp)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch total debts for each customer
                                    $query = "
                                        SELECT c.customer_name, 
                                               SUM(p.nominal) AS total_piutang, 
                                               CASE 
                                                   WHEN SUM(p.nominal) > 0 THEN 'Piutang Aktif' 
                                                   ELSE 'Piutang Macet' 
                                               END AS status
                                        FROM piutang p
                                        JOIN customers c ON p.customer_id = c.customer_id
                                        GROUP BY c.customer_name";
                                    
                                    // Execute the query
                                    $result = mysqli_query($conn, $query);

                                    // Check for errors
                                    if (!$result) {
                                        echo "<tr><td colspan='3'>Error executing query: " . mysqli_error($conn) . "</td></tr>";
                                    } else {
                                        // Process results
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td>' . htmlspecialchars($row['customer_name']) . '</td>';
                                            echo '<td>' . number_format($row['total_piutang'], 0, ',', '.') . '</td>';
                                            echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                                            echo '</tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Total Transactions -->
            <div class="row">
                <div class="col-xxl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Total Transactions Overview</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tanggal Transaksi</th>
                                        <th>Jenis Transaksi</th>
                                        <th>Total (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch total transactions
                                    $transaction_query = "
                                        SELECT tanggal, 
                                               jenis, 
                                               SUM(nominal) AS total 
                                        FROM transactions 
                                        GROUP BY tanggal, jenis";
                                    
                                    // Execute the query
                                    $transaction_result = mysqli_query($conn, $transaction_query);

                                    // Check for errors
                                    if (!$transaction_result) {
                                        echo "<tr><td colspan='3'>Error executing query: " . mysqli_error($conn) . "</td></tr>";
                                    } else {
                                        // Process results
                                        while ($transaction_row = mysqli_fetch_assoc($transaction_result)) {
                                            echo '<tr>';
                                            echo '<td>' . htmlspecialchars($transaction_row['tanggal']) . '</td>';
                                            echo '<td>' . htmlspecialchars($transaction_row['jenis']) . '</td>';
                                            echo '<td>' . number_format($transaction_row['total'], 0, ',', '.') . '</td>';
                                            echo '</tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>
<?php include 'footer.php'; ?>
