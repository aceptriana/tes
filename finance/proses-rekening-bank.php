<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

if (isset($_POST['tambah_rekening'])) {
    $nama_rekening = $_POST['nama_rekening'];
    $nama_pemilik = $_POST['nama_pemilik'];
    $nomor_rekening = $_POST['nomor_rekening'];
    $saldo_awal = $_POST['saldo_awal'];

    // Set saldo_akhir to be the same as saldo_awal initially
    $saldo_akhir = $saldo_awal;

    // Insert the new account into the database
    $insert_sql = "INSERT INTO rekening_bank (nama_rekening, nama_pemilik, nomor_rekening, saldo, saldo_akhir) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param('ssssd', $nama_rekening, $nama_pemilik, $nomor_rekening, $saldo_awal, $saldo_akhir);

    if ($insert_stmt->execute()) {
        // Update total_saldo_awal after successfully inserting the new record
        $update_total_sql = "UPDATE rekening_bank SET total_saldo_awal = (SELECT SUM(saldo) FROM rekening_bank)";
        if ($conn->query($update_total_sql) === TRUE) {
            // Redirect back to rekening-bank.php with success message
            header("Location: rekening-bank.php?feedback=success");
            exit();
        } else {
            // If there's an error updating the total saldo, redirect with error message
            header("Location: rekening-bank.php?feedback=update_error");
            exit();
        }
    } else {
        // Redirect back to rekening-bank.php with error message if insertion fails
        header("Location: rekening-bank.php?feedback=error");
        exit();
    }
}
?>
