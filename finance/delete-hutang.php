<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

// Check if the ID is provided in the URL
if (isset($_GET['id'])) {
    $kode_hutang = $_GET['id'];

    // Prepare the SQL statement to delete the record
    $sql = "DELETE FROM hutang WHERE kode_hutang = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $kode_hutang);

    // Execute the statement and check if deletion was successful
    if ($stmt->execute()) {
        header('Location: catatan-hutang.php?status=deleted'); // Redirect with success message
        exit();
    } else {
        header('Location: catatan-hutang.php?status=error'); // Redirect with error message
        exit();
    }
} else {
    // Redirect if no ID is provided
    header('Location: catatan-hutang.php?status=error');
    exit();
}
?>
