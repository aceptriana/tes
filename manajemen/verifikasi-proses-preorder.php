<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $transaction_id = $_POST['transaction_id'];
    $status_manajemen = $_POST['status_manajemen'];

    // Update the status_manajemen in the database
    $sql_update = "UPDATE transaksi_pre_order SET status_manajemen = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("si", $status_manajemen, $transaction_id);
    
    if ($stmt->execute()) {
        // Redirect back to the verification page with a success message
        header("Location: verifikasi-transaksi-preorder.php?success=Status manajemen berhasil diperbarui");
        exit;
    } else {
        // Handle error
        header("Location: verifikasi-transaksi-preorder.php?error=Gagal memperbarui status manajemen");
        exit;
    }
}
?>
