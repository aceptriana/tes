<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/session.php';

// Check if kode_transaksi is set
if (isset($_GET['kode_transaksi'])) {
    $kode_transaksi = $_GET['kode_transaksi'];
    
    try {
        // Prepare SQL statement to fetch transaction details
        $sql = "SELECT 
                    t.kode_transaksi, 
                    t.customer_id, 
                    b.nama_barang, 
                    b.warna_motif, 
                    t.jumlah_beli, 
                    t.total_harga, 
                    t.alamat_kirim, 
                    t.tanggal_transaksi, 
                    c.customer_name, 
                    t.status,
                    t.keterangan
                FROM transaksi_barang t
                JOIN customers c ON t.customer_id = c.customer_id
                JOIN penyimpanan_barang b ON t.kode_penerimaan = b.kode_penerimaan
                WHERE t.kode_transaksi = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $kode_transaksi);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $transaction_details = $result->fetch_all(MYSQLI_ASSOC);
            } else {
                echo '<script>alert("Transaksi tidak ditemukan!"); window.location.href="riwayat-transaksi.php";</script>';
                exit();
            }
            $stmt->close();
        } else {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
    } catch (Exception $e) {
        echo '<script>alert("Error: ' . $e->getMessage() . '"); window.location.href="riwayat-transaksi.php";</script>';
        exit();
    }
} else {
    echo '<script>alert("Kode transaksi tidak ditemukan!"); window.location.href="riwayat-transaksi.php";</script>';
    exit();
}
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <h5 class="m-b-10">Detail Transaksi #<?php echo htmlspecialchars($transaction_details[0]['kode_transaksi']); ?></h5>
        </div>
        <div class="main-content">
            <div class="card mt-3">
                <div class="card-body">
                    <h6>Informasi Customer</h6>
                    <p><strong>Nama Customer:</strong> <?php echo htmlspecialchars($transaction_details[0]['customer_name']); ?></p>
                    <p><strong>Alamat Kirim:</strong> <?php echo htmlspecialchars($transaction_details[0]['alamat_kirim']); ?></p>
                    <hr>
                    
                    <h6>Detail Transaksi</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Warna Motif</th>
                                <th>Jumlah Beli</th>
                                <th>Total Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transaction_details as $detail): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($detail['nama_barang']); ?></td>
                                    <td><?php echo htmlspecialchars($detail['warna_motif']); ?></td>
                                    <td><?php echo htmlspecialchars($detail['jumlah_beli']); ?></td>
                                    <td>Rp. <?php echo number_format($detail['total_harga'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p><strong>Tanggal Transaksi:</strong> <?php echo date('d-m-Y H:i:s', strtotime($transaction_details[0]['tanggal_transaksi'])); ?></p>
                    <p><strong>Status:</strong> <span class="badge <?php echo getStatusBadge($transaction_details[0]['status']); ?>"><?php echo htmlspecialchars($transaction_details[0]['status']); ?></span></p>
                    <p><strong>Keterangan:</strong> <span class="badge <?php echo getKeteranganBadge($transaction_details[0]['keterangan']); ?>"><?php echo htmlspecialchars($transaction_details[0]['keterangan']); ?></span></p>
                </div>
            </div>
            <div class="text-end mt-3">
                <a href="riwayat-transaksi.php" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

<?php
function getStatusBadge($status) {
    switch ($status) {
        case 'Menunggu':
            return 'bg-soft-warning text-warning';
        case 'Ditolak':
            return 'bg-soft-danger text-danger';
        case 'Disetujui':
            return 'bg-soft-success text-success';
        default:
            return 'bg-soft-secondary text-secondary';
    }
}

function getKeteranganBadge($keterangan) {
    switch (strtolower($keterangan)) {
        case 'sudah lunas':
            return 'bg-soft-success text-success';
        case 'belum lunas':
            return 'bg-soft-warning text-warning';
        case 'tempo':
            return 'bg-soft-danger text-danger';
        default:
            return 'bg-soft-secondary text-secondary';
    }
}
?>