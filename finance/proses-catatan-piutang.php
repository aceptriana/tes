<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

if (isset($_POST['add_piutang'])) {
    $tanggal = $_POST['tanggal'];
    $nominal = $_POST['nominal'];
    $keterangan = $_POST['keterangan'];
    $jenis_piutang = $_POST['jenis_piutang'];
    $customer_id = $_POST['customer_id']; // Retrieve customer_id from the form

    // Generate unique kode_piutang
    $sql = "SELECT COUNT(*) as count FROM piutang";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $count = $row['count'] + 1; // Count the number of existing piutang
    $kode_piutang = "PTG-" . str_pad($count, 6, "0", STR_PAD_LEFT);

    // Check if the kode_piutang already exists
    $check_sql = "SELECT kode_piutang FROM piutang WHERE kode_piutang = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('s', $kode_piutang);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    // If the kode_piutang already exists, generate a new one
    if ($check_result->num_rows > 0) {
        // Increment count until a unique kode_piutang is found
        do {
            $count++;
            $kode_piutang = "PTG-" . str_pad($count, 6, "0", STR_PAD_LEFT);
            $check_stmt->bind_param('s', $kode_piutang);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
        } while ($check_result->num_rows > 0);
    }

    // Insert into piutang table with customer_id
    $sql = "INSERT INTO piutang (kode_piutang, tanggal, nominal, keterangan, jenis_piutang, customer_id) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error); // Error handling if prepare fails
    }

    // Bind parameters
    $stmt->bind_param('ssdssi', $kode_piutang, $tanggal, $nominal, $keterangan, $jenis_piutang, $customer_id);

    if ($stmt->execute()) {
        // Redirect to catatan-piutang.php with success status
        header('Location: catatan-piutang.php?status=added');
        exit; // Exit to prevent further script execution
    } else {
        // Debugging error - display the error if insertion fails
        die("Error: " . $stmt->error); // This will help catch any issue
    }
}
?>
