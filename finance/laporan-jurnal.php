<?php
include '../config/db.php'; // Include your database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Fetch filter parameters if set
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$kode_coa = $_GET['kode_coa'] ?? '';

// Initialize empty data for transactions
$transactions = [];
$total_pemasukan = 0;
$total_pengeluaran = 0;
$saldo_awal = 0;

// Fetch total_saldo_awal from rekening_bank
$saldo_awal_sql = "SELECT total_saldo_awal FROM rekening_bank LIMIT 1";
$saldo_awal_result = $conn->query($saldo_awal_sql);
if ($saldo_awal_result->num_rows > 0) {
    $saldo_awal_row = $saldo_awal_result->fetch_assoc();
    $saldo_awal = $saldo_awal_row['total_saldo_awal'];
}

// Fetch data if the filter is applied
if ($start_date && $end_date) {
    // Build the SQL query based on the filters
    $sql = "SELECT t.*, c.nama_akun 
            FROM transactions t
            JOIN coa c ON t.kode_coa = c.kode_akun
            WHERE t.tanggal BETWEEN ? AND ?";

    $params = [$start_date, $end_date];

    if ($kode_coa !== '') {
        $sql .= " AND t.kode_coa = ?";
        $params[] = $kode_coa;
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the transactions based on the filters
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;

        // Calculate totals
        if ($row['jenis'] === 'Pemasukan') {
            $total_pemasukan += $row['nominal'];
        } elseif ($row['jenis'] === 'Pengeluaran') {
            $total_pengeluaran += $row['nominal'];
        }
    }
}
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Laporan Jurnal</h5>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="laporan-jurnal.php" method="GET">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">Mulai Tanggal</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">Sampai Tanggal</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="kode_coa" class="form-label">Kategori</label>
                                <select class="form-control" id="kode_coa" name="kode_coa">
                                    <option value="">Semua Kategori</option>
                                    <?php 
                                    // Fetch categories from COA table
                                    $coa_sql = "SELECT kode_akun, nama_akun FROM coa";
                                    $coa_result = $conn->query($coa_sql);
                                    while ($row = $coa_result->fetch_assoc()) {
                                        echo "<option value='{$row['kode_akun']}'>{$row['kode_akun']} - {$row['nama_akun']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Display Filter Information -->
        <div class="main-content">
            <div class="card mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <h6>Dari Tanggal:</h6>
                            <p><?php echo $start_date; ?></p>
                        </div>
                        <div class="col-md-3">
                            <h6>Sampai Tanggal:</h6>
                            <p><?php echo $end_date; ?></p>
                        </div>
                        <div class="col-md-3">
                            <h6>Kategori:</h6>
                            <p><?php echo $kode_coa !== '' ? $kode_coa : 'Semua Kategori'; ?></p>
                        </div>
                        <div class="col-md-3">
                            <h6>Saldo Awal:</h6>
                            <p>Rp. <?php echo number_format($saldo_awal, 0, ',', '.'); ?>,-</p>
                        </div>
                    </div>
                    <a href="print-laporan-jurnal.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&kode_coa=<?php echo $kode_coa; ?>" class="btn btn-secondary">PRINT</a>
                </div>
            </div>

            <!-- Table for displaying transactions -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="table-responsive"> 
                        <table class="table table-striped table-bordered table-hover"> 
                            <thead class="thead-light"> 
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Keterangan</th>
                                    <th>Jenis</th>
                                    <th>Pengeluaran</th>
                                    <th>Pemasukan</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (!empty($transactions)) {
                                $no = 1;
                                $total_pengeluaran = 0;
                                $total_pemasukan = 0;

                                foreach ($transactions as $row) {
                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>{$row['tanggal']}</td>
                                            <td>{$row['nama_akun']}</td>
                                            <td>{$row['keterangan']}</td>
                                            <td>{$row['jenis']}</td>
                                            <td>" . ($row['jenis'] === 'Pengeluaran' ? number_format($row['nominal'], 2, ',', '.') : '-') . "</td>
                                            <td>" . ($row['jenis'] === 'Pemasukan' ? number_format($row['nominal'], 2, ',', '.') : '-') . "</td>
                                          </tr>";

                                    if ($row['jenis'] === 'Pengeluaran') {
                                        $total_pengeluaran += $row['nominal'];
                                    } elseif ($row['jenis'] === 'Pemasukan') {
                                        $total_pemasukan += $row['nominal'];
                                    }

                                    $no++;
                                }

                                // Tampilkan total di samping kanan pengeluaran
                                echo "<tr>
                                        <td colspan='5' class='text-right font-weight-bold'>Total</td>
                                        <td class='font-weight-bold'>Rp. " . number_format($total_pengeluaran, 2, ',', '.') . ",-</td>
                                        <td class='font-weight-bold'>Rp. " . number_format($total_pemasukan, 2, ',', '.') . ",-</td>
                                      </tr>";

                                // Tampilkan saldo akhir
                                $saldo_akhir = $saldo_awal + $total_pemasukan - $total_pengeluaran; 
                                echo "<tr>
                                        <td colspan='5' class='text-right font-weight-bold'>Saldo Akhir</td>
                                        <td class='font-weight-bold' colspan='2'>Rp. " . number_format($saldo_akhir, 2, ',', '.') . ",-</td>
                                      </tr>";
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>Silahkan filter terlebih dahulu</td></tr>";
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
