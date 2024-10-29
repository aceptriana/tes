<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Initialize search query variable
$search_query = '';

// Fetch transactions based on search query
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}
$sql_transaction = "SELECT kode_prapesan, kode_pemasok, nama_barang, jumlah, harga, status_manajemen  , status_verifikasi
                   FROM transaksi_pre_order 
                   WHERE nama_barang LIKE ? OR kode_prapesan LIKE ? OR kode_pemasok LIKE ?";

$search_param = "%{$search_query}%";
$stmt_transaction = $conn->prepare($sql_transaction);
$stmt_transaction->bind_param('sss', $search_param, $search_param, $search_param);
$stmt_transaction->execute();
$result_transaction = $stmt_transaction->get_result();
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Riwayat Transaksi Pre-Order</h5>
                </div>
            </div>
        </div>

        <div class="main-content">
            <!-- Search Transactions -->
            <div class="card mt-3">
                <div class="card-body">
                    <form action="riwayat-transaksi.php" method="GET">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="search" class="form-label">Cari Riwayat</label>
                                <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info">Cari</button>
                    </form>
                </div>
            </div>

            <!-- Transaction Table -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kode Prapesan</th>
                                    <th>Kode Pemasok</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Keuangan</th>
                                    <th>Manajemen</th>
                        
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result_transaction->num_rows > 0) {
                                    while ($row = $result_transaction->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$row['kode_prapesan']}</td>
                                                <td>{$row['kode_pemasok']}</td>
                                                <td>{$row['nama_barang']}</td>
                                                <td>{$row['jumlah']}</td>
                                                <td>{$row['harga']}</td>
                                                <td>{$row['status_verifikasi']}</td>
                                                <td>{$row['status_manajemen']}</td>
                   
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>Tidak ada transaksi Pre-Order</td></tr>";
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
