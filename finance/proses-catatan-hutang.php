<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

// Handle adding a new debt
if (isset($_POST['add_hutang'])) {
    $tanggal = $_POST['tanggal'];
    $nominal = $_POST['nominal'];
    $keterangan = $_POST['keterangan'];

    // Generate a unique code for the debt
    $sql = "SELECT COUNT(*) as total FROM hutang";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $total = $row['total'] + 1;
    $kode_hutang = 'HTG-' . str_pad($total, 4, '0', STR_PAD_LEFT);

    // Insert the debt record into the database
    $sql = "INSERT INTO hutang (kode_hutang, tanggal, nominal, keterangan) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssis', $kode_hutang, $tanggal, $nominal, $keterangan);

    if ($stmt->execute()) {
        header('Location: catatan-hutang.php?status=added');
    } else {
        header('Location: catatan-hutang.php?status=error');
    }
}
?>
