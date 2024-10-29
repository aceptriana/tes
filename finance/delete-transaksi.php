<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

// Check if kode_transaksi is set
if (isset($_GET['kode_transaksi'])) {
    $kode_transaksi = $_GET['kode_transaksi'];

    // Prepare SQL statement to delete the transaction
    $sql = "DELETE FROM transaksi_barang WHERE kode_transaksi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $kode_transaksi);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo '<script>alert("Transaksi berhasil dihapus!"); window.location.href="riwayat-transaksi.php";</script>';
    } else {
        echo '<script>alert("Terjadi kesalahan saat menghapus transaksi!"); window.location.href="riwayat-transaksi.php";</script>';
    }

    $stmt->close();
} else {
    echo '<script>alert("Kode transaksi tidak ditemukan!"); window.location.href="riwayat-transaksi.php";</script>';
}
?>
