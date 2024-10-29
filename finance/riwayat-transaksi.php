<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Initialize variables
$search_query = '';

// Handle form submission for the transaction
if (isset($_POST['transact'])) {
    $customer_id = $_POST['customer_id'];
    $kode_penerimaan = $_POST['kode_penerimaan'];
    $jumlah_beli = $_POST['jumlah'];
    $alamat_kirim = $_POST['alamat'];

    // Fetch the price and current stock from penyimpanan_barang
    $sql = "SELECT jumlah, harga_barang, nama_barang FROM penyimpanan_barang WHERE kode_penerimaan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $kode_penerimaan);
    $stmt->execute();
    $stmt->bind_result($current_stock, $harga_barang, $nama_barang);
    $stmt->fetch();
    $stmt->close();

    // Check stock availability
    if ($jumlah_beli > $current_stock) {
        echo '<script>alert("Stok tidak cukup!"); window.location.href="transaksi-barang.php";</script>';
        exit();
    } else {
        // Reduce stock in penyimpanan_barang
        $new_stock = $current_stock - $jumlah_beli;
        $sql_update_stock = "UPDATE penyimpanan_barang SET jumlah = ? WHERE kode_penerimaan = ?";
        $stmt_update = $conn->prepare($sql_update_stock);
        $stmt_update->bind_param('is', $new_stock, $kode_penerimaan);
        $stmt_update->execute();
        $stmt_update->close();

        // Log the transaction
        $sql_insert_transaction = "INSERT INTO transaksi_barang (customer_id, kode_penerimaan, nama_barang, jumlah_beli, alamat_kirim, total_harga, tanggal_transaksi, kode_transaksi) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
        $total_harga = $jumlah_beli * $harga_barang;

        // Generate a unique kode_transaksi
        $kode_transaksi_query = "SELECT IFNULL(MAX(CAST(SUBSTRING(kode_transaksi, 9) AS UNSIGNED)), 0) + 1 FROM transaksi_barang";
        $result = $conn->query($kode_transaksi_query);
        $row = $result->fetch_row();
        $kode_transaksi = "TRX-" . str_pad($row[0], 6, '0', STR_PAD_LEFT); // Format: TRX-000001

        $stmt_insert = $conn->prepare($sql_insert_transaction);
        $stmt_insert->bind_param('sssisi', $customer_id, $kode_penerimaan, $nama_barang, $jumlah_beli, $alamat_kirim, $total_harga, $kode_transaksi);
        if ($stmt_insert->execute()) {
            echo '<script>alert("Transaksi berhasil!"); window.location.href="transaksi-barang.php";</script>';
        } else {
            echo '<script>alert("Terjadi kesalahan saat menyimpan transaksi!"); window.location.href="transaksi-barang.php";</script>';
        }
        $stmt_insert->close();
        exit();
    }
}

// Fetch available items and customers for the form
$sql_barang = "SELECT kode_penerimaan, nama_barang, jumlah, harga_barang FROM penyimpanan_barang";
$result_barang = $conn->query($sql_barang);

$sql_customer = "SELECT customer_id, customer_name FROM customers";
$result_customer = $conn->query($sql_customer);

// Fetch transactions based on search query
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}
$sql_transaction = "SELECT 
                        t.kode_transaksi, 
                        GROUP_CONCAT(b.nama_barang SEPARATOR ', ') AS nama_barang, 
                        MAX(c.customer_name) AS customer_name,
                        SUM(t.jumlah_beli) AS total_jumlah, 
                        SUM(t.total_harga) AS total_harga,
                        MAX(t.alamat_kirim) AS alamat_kirim, 
                        MAX(t.tanggal_transaksi) AS tanggal_transaksi, 
                        t.status,  -- Use the status directly
                        MAX(t.keterangan) AS keterangan 
                    FROM transaksi_barang t
                    JOIN customers c ON t.customer_id = c.customer_id
                    JOIN penyimpanan_barang b ON t.kode_penerimaan = b.kode_penerimaan
                    WHERE c.customer_name LIKE ? OR b.nama_barang LIKE ? OR t.alamat_kirim LIKE ?
                    GROUP BY t.kode_transaksi
                    ORDER BY MAX(t.tanggal_transaksi) DESC";

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
                    <h5 class="m-b-10">Riwayat Transaksi</h5>
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
                                <label for="search" class="form-label">Cari Transaksi</label>
                                <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info">Cari</button>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="paymentList">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Transaksi</th>
                                            <th>Nama Customer</th>
                                        
                                            <th>Total Harga</th>
                                            <th>Alamat Kirim</th>
                                            <th>Tanggal Transaksi</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1; // Initialize the numbering
                                        while ($row = $result_transaction->fetch_assoc()) {
                                            // Determine badge classes for status
                                            switch ($row['status']) {
                                                case 'Menunggu':
                                                    $status_badge = 'bg-soft-warning text-warning';
                                                    break;
                                                case 'Ditolak':
                                                    $status_badge = 'bg-soft-danger text-danger';
                                                    break;
                                                case 'Disetujui':
                                                    $status_badge = 'bg-soft-success text-success';
                                                    break;
                                                default:
                                                    $status_badge = 'bg-soft-secondary text-secondary'; // Fallback if needed
                                            }

                                            // Determine badge classes for keterangan
                                            switch ($row['keterangan']) {
                                                case 'sudah lunas':
                                                    $keterangan_badge = 'bg-soft-success text-success';
                                                    break;
                                                case 'belum lunas':
                                                    $keterangan_badge = 'bg-soft-warning text-warning';
                                                    break;
                                                case 'tempo':
                                                    $keterangan_badge = 'bg-soft-danger text-danger';
                                                    break;
                                                default:
                                                    $keterangan_badge = 'bg-soft-secondary text-secondary'; // Fallback if needed
                                            }

                                            echo "<tr class='single-item'>
                                                    <td>{$no}</td>
                                                    <td><a href='invoice-view.php?kode_transaksi={$row['kode_transaksi']}' class='fw-bold'>#{$row['kode_transaksi']}</a></td>
                                                    <td>{$row['customer_name']}</td>
                                                   
                                                    <td>Rp. " . number_format($row['total_harga'], 0, ',', '.') . "</td>
                                                    <td>{$row['alamat_kirim']}</td>
                                                    <td>{$row['tanggal_transaksi']}</td>
                                                    <td><div class='badge $status_badge'>{$row['status']}</div></td>
                                                    <td><div class='badge $keterangan_badge'>{$row['keterangan']}</div></td>
                                                    <td>
                                                        <div class='hstack gap-2 justify-content-end'>
                                                            <a href='view-transaksi.php?kode_transaksi={$row['kode_transaksi']}' class='avatar-text avatar-md'>
                                                                <i class='feather feather-eye'></i>
                                                            </a>
                                                            <div class='dropdown'>
                                                                <a href='javascript:void(0)' class='avatar-text avatar-md' data-bs-toggle='dropdown' data-bs-offset='0,21'>
                                                                    <i class='feather feather-more-horizontal'></i>
                                                                </a>
                                                                <ul class='dropdown-menu'>
                                                                    <li>
                                                                        <a class='dropdown-item' href='invoice-transaksi.php?kode_transaksi={$row['kode_transaksi']}'>
                                                                            <i class='feather feather-printer me-3'></i>
                                                                            <span>Prin Invoice</span>
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class='dropdown-item printBTN' href='invoice-transaksi-logo.php?kode_transaksi={$row['kode_transaksi']}'>
                                                                            <i class='feather feather-printer me-3'></i>
                                                                            <span>Print Invoice Logo</span>
                                                                        </a>
                                                                    </li>
  
                                                                    <li class='dropdown-divider'></li>
                                                                    <li>
                                                                        <a class='dropdown-item' href='delete-transaksi.php?kode_transaksi={$row['kode_transaksi']}'>
                                                                            <i class='feather feather-trash-2 me-3'></i>
                                                                            <span>Delete</span>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>";
                                            $no++; // Increment the row number
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
    </div>
</main>

<?php include 'footer.php'; ?>
