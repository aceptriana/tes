<?php
// Include database connection and session management
include '../config/db.php'; 
include '../config/session.php'; 

// Handle form submission
if (isset($_POST['submit'])) {
    // Get form inputs
    $kode_prapesan = filter_input(INPUT_POST, 'kode_prapesan', FILTER_SANITIZE_STRING);
    $kode_pemasok = filter_input(INPUT_POST, 'kode_pemasok', FILTER_SANITIZE_STRING);
    $nama_barang = filter_input(INPUT_POST, 'nama_barang', FILTER_SANITIZE_STRING);
    $jumlah = filter_input(INPUT_POST, 'jumlah', FILTER_SANITIZE_NUMBER_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $harga = filter_input(INPUT_POST, 'harga', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Calculate total price
    $total_harga = $jumlah * $harga;

    // Check if data is valid
    if (!$kode_prapesan || !$kode_pemasok || !$nama_barang || !$jumlah || !$harga) {
        die('Error: Please fill in all fields.');
    }

    // Insert transaction into transaksi_barang
    $transaksi_sql = "INSERT INTO transaksi_barang (kode_penyimpanan, customer_id, jumlah_beli, total_harga, alamat_kirim, tanggal_transaksi, nama_barang) 
                      VALUES (?, ?, ?, ?, ?, NOW(), ?)";
    $stmt_transaksi = $conn->prepare($transaksi_sql);
    $stmt_transaksi->bind_param("siisss", $kode_prapesan, $kode_pemasok, $jumlah, $total_harga, $alamat_kirim, $nama_barang);

    if ($stmt_transaksi->execute()) {
        // Fetch saldo_akhir from rekening_bank (assuming rekening_id is passed via POST)
        $id_rekening = filter_input(INPUT_POST, 'id_rekening', FILTER_SANITIZE_NUMBER_INT);
        $balance_query = "SELECT saldo_akhir FROM rekening_bank WHERE id = ?";
        $balance_stmt = $conn->prepare($balance_query);
        $balance_stmt->bind_param("i", $id_rekening);
        $balance_stmt->execute();
        $balance_stmt->bind_result($current_saldo_akhir);
        $balance_stmt->fetch();
        $balance_stmt->close();

        // Update saldo_akhir based on transaction type (in this case, adding total_harga to saldo_akhir)
        $update_rekening_sql = "UPDATE rekening_bank SET saldo_akhir = saldo_akhir + ? WHERE id = ?";
        $update_rekening_stmt = $conn->prepare($update_rekening_sql);
        $update_rekening_stmt->bind_param("di", $total_harga, $id_rekening);

        if ($update_rekening_stmt->execute()) {
            header("Location: riwayat-transaksi.php?status=success");
        } else {
            die('Error updating bank saldo_akhir: ' . htmlspecialchars($update_rekening_stmt->error));
        }

        $update_rekening_stmt->close();
    } else {
        die('Error inserting transaction: ' . htmlspecialchars($stmt_transaksi->error));
    }

    // Close the statement
    $stmt_transaksi->close();
}
