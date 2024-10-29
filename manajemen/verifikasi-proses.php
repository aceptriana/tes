<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

// Cek apakah ada ID transaksi dan status yang diterima
if (isset($_POST['transaction_id']) && isset($_POST['status'])) {
    $transaction_id = $_POST['transaction_id'];
    $status = $_POST['status'];

    // Update status transaksi
    $sql_verify = "UPDATE transaksi_barang SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_verify);
    $stmt->bind_param('si', $status, $transaction_id);

    if ($stmt->execute()) {
        echo '<script>alert("Transaksi berhasil diperbarui menjadi ' . $status . '!"); window.location.href="verifikasi-transaksi-barang.php";</script>';
    } else {
        echo '<script>alert("Terjadi kesalahan saat memperbarui transaksi!"); window.location.href="verifikasi-transaksi-barang.php";</script>';
    }
    $stmt->close();
} else {
    echo '<script>alert("ID transaksi atau status tidak ditemukan!"); window.location.href="verifikasi-transaksi-barang.php";</script>';
}
?>
