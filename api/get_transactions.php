<?php
include '../config/db.php';

$sql_transaction = "SELECT t.id, b.nama_barang, c.customer_name, t.jumlah_beli, t.total_harga, t.alamat_kirim, t.tanggal_transaksi, t.status 
                    FROM transaksi_barang t
                    JOIN customers c ON t.customer_id = c.customer_id
                    JOIN penyimpanan_barang b ON t.kode_penyimpanan = b.kode_penyimpanan";
$result_transaction = $conn->query($sql_transaction);

$transactions = [];
while ($row = $result_transaction->fetch_assoc()) {
    $transactions[] = $row;
}

header('Content-Type: application/json');
echo json_encode($transactions);
?>
