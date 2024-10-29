<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

if (isset($_GET['id'])) {
    $kode_piutang = $_GET['id'];

    // Query to delete the record from piutang table
    $sql = "DELETE FROM piutang WHERE kode_piutang = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $kode_piutang);

    if ($stmt->execute()) {
        // Redirect to catatan-piutang.php with success status
        header('Location: catatan-piutang.php?status=deleted');
    } else {
        // Redirect to catatan-piutang.php with error status
        header('Location: catatan-piutang.php?status=error');
    }
} else {
    // If no id is provided, redirect back to catatan-piutang.php
    header('Location: catatan-piutang.php');
}
?>
