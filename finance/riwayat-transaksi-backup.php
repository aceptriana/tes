<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Initialize variables
$products = [];
$total_price = 0;
$search_query = '';

// Handle form submission for the transaction
if (isset($_POST['transact'])) {
    $customer_id = $_POST['customer_id'];
    $kode_penyimpanan = $_POST['kode_penyimpanan'];
    $jumlah_beli = $_POST['jumlah'];
    $alamat_kirim = $_POST['alamat'];

    // Fetch the price and current stock from penyimpanan_barang
    $sql = "SELECT jumlah, harga_barang, nama_barang FROM penyimpanan_barang WHERE kode_penyimpanan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $kode_penyimpanan);
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
        $sql_update_stock = "UPDATE penyimpanan_barang SET jumlah = ? WHERE kode_penyimpanan = ?";
        $stmt_update = $conn->prepare($sql_update_stock);
        $stmt_update->bind_param('is', $new_stock, $kode_penyimpanan);
        $stmt_update->execute();
        $stmt_update->close();

        // Log the transaction
        $sql_insert_transaction = "INSERT INTO transaksi_barang (customer_id, kode_penyimpanan, nama_barang, jumlah_beli, alamat_kirim, total_harga, tanggal_transaksi) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $total_harga = $jumlah_beli * $harga_barang;
        $stmt_insert = $conn->prepare($sql_insert_transaction);
        $stmt_insert->bind_param('sssisi', $customer_id, $kode_penyimpanan, $nama_barang, $jumlah_beli, $alamat_kirim, $total_harga);
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
$sql_barang = "SELECT kode_penyimpanan, nama_barang, jenis, warna, jumlah, harga_barang FROM penyimpanan_barang";
$result_barang = $conn->query($sql_barang);

$sql_customer = "SELECT customer_id, customer_name FROM customers";
$result_customer = $conn->query($sql_customer);

// Fetch transactions based on search query
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}
$sql_transaction = "SELECT t.id, b.nama_barang, c.customer_name, t.jumlah_beli, t.total_harga, t.alamat_kirim, t.tanggal_transaksi 
                   FROM transaksi_barang t
                   JOIN customers c ON t.customer_id = c.customer_id
                   JOIN penyimpanan_barang b ON t.kode_penyimpanan = b.kode_penyimpanan
                   WHERE c.customer_name LIKE ? OR b.nama_barang LIKE ? OR t.alamat_kirim LIKE ?";

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
                    <h5 class="m-b-10">Verifikasi Transaksi</h5>
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

            <!-- Transaction Table -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                        <thead>
    <tr>
        <th>No</th>
        <th>Nama Barang</th> <!-- Tambahkan kolom nama_barang -->
        <th>Nama Customer</th>
        <th>Jumlah Beli</th>
        <th>Total Harga</th>
        <th>Alamat Kirim</th>
        <th>Tanggal Transaksi</th>
        <th>Opsi</th>
    </tr>
</thead>
<tbody>
    <?php
    $no = 1;
    while ($row = $result_transaction->fetch_assoc()) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['nama_barang']}</td> <!-- Tampilkan nama_barang -->
                <td>{$row['customer_name']}</td>
                <td>{$row['jumlah_beli']}</td>
                <td>{$row['total_harga']}</td>
                <td>{$row['alamat_kirim']}</td>
                <td>{$row['tanggal_transaksi']}</td>
                <td>
                    <a href='invoice-transaksi.php?id={$row['id']}' class='btn btn-warning btn-sm'>Invoice</a>
                    <a href='delete-transaksi.php?id={$row['id']}' class='btn btn-danger btn-sm'>Hapus</a>
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