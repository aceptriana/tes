<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Check login session

// Handle form submission
if (isset($_POST['submit'])) {
    $customer_id = $_POST['customer_id'];
    $alamat_kirim = $_POST['alamat_kirim'];
    $id_rekening = $_POST['id_rekening'];
    $total_keseluruhan = $_POST['total_keseluruhan']; // Total harga semua barang
    $dp = $_POST['dp']; // Nilai yang dibayarkan
    $status_pembayaran = ''; // Variabel untuk menyimpan status pembayaran

    // Validasi pembayaran
    if ($dp >= $total_keseluruhan) {
        $status_pembayaran = 'sudah lunas';
    } elseif ($dp > 0 && $dp < $total_keseluruhan) {
        // Jika pembayaran tidak sesuai total keseluruhan
        $status_pembayaran = isset($_POST['status_pembayaran']) ? $_POST['status_pembayaran'] : 'belum lunas';
    } else {
        $status_pembayaran = 'tempo';
    }

    // Insert ke dalam tabel transaksi_barang
    $insert_transaksi_sql = "INSERT INTO transaksi_barang (customer_id, alamat_kirim, total_harga, dp, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_transaksi_sql);
    $stmt->bind_param("ssdds", $customer_id, $alamat_kirim, $total_keseluruhan, $dp, $status_pembayaran);

    if ($stmt->execute()) {
        // Update saldo rekening jika transaksi berhasil
        $rekening_sql = "UPDATE rekening_bank SET saldo_akhir = saldo_akhir + ? WHERE id = ?";
        $stmt_rekening = $conn->prepare($rekening_sql);
        $stmt_rekening->bind_param("di", $dp, $id_rekening);
        $stmt_rekening->execute();

        echo "<script>
                alert('Transaksi berhasil diproses.');
                window.location.href = 'transaksi-barang.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal memproses transaksi.');
                window.location.href = 'transaksi-barang.php';
              </script>";
    }
}
?>
