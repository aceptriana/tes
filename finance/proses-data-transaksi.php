<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Check login session

// Handle adding a new transaction
if (isset($_POST['submit'])) {
    $tanggal = $_POST['tanggal'];
    $jenis = $_POST['jenis'];
    $kode_coa = $_POST['kode_coa'];
    $nominal = $_POST['nominal'];
    $keterangan = $_POST['keterangan'];
    $nama_rekening = $_POST['nama_rekening'];

    // Check current saldo before updating
    $balance_query = "SELECT saldo, saldo_akhir FROM rekening_bank WHERE nama_rekening = ?";
    $balance_stmt = $conn->prepare($balance_query);
    $balance_stmt->bind_param("s", $nama_rekening);
    $balance_stmt->execute();
    $balance_stmt->bind_result($current_balance, $current_saldo_akhir);
    $balance_stmt->fetch();
    $balance_stmt->close();

    // Prevent transaction if insufficient balance for 'Pengeluaran'
    if ($jenis === 'Pengeluaran' && $nominal > $current_saldo_akhir) {
        header("Location: data-transaksi.php?status=insufficient_balance");
        exit();
    }

    // Insert new transaction
    $sql = "INSERT INTO transactions (tanggal, jenis, kode_coa, nominal, keterangan, nama_saldo) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $tanggal, $jenis, $kode_coa, $nominal, $keterangan, $nama_rekening);
    
    if ($stmt->execute()) {
        // Update saldo_akhir based on jenis transaksi
        if ($jenis === 'Pemasukan') {
            $update_sql = "UPDATE rekening_bank SET saldo_akhir = saldo_akhir + ? WHERE nama_rekening = ?";
        } else if ($jenis === 'Pengeluaran') {
            $update_sql = "UPDATE rekening_bank SET saldo_akhir = saldo_akhir - ? WHERE nama_rekening = ?";
        }

        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ds", $nominal, $nama_rekening);
        $update_stmt->execute();

        header("Location: data-transaksi.php?status=success");
    } else {
        header("Location: data-transaksi.php?status=error");
    }
}
?>
