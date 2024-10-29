<?php
include '../config/db.php';

if (isset($_GET['kode_nota'])) {
    $kode_nota = $conn->real_escape_string($_GET['kode_nota']);

    $sql = "SELECT jenis_barang_dikirim, nama_barang, jenis, warna, jumlah, panjang, roll, deskripsi_barang 
            FROM nota_penerimaan_barang 
            WHERE kode_nota = '$kode_nota'";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Data nota tidak ditemukan']);
    }
} else {
    echo json_encode(['error' => 'Kode nota tidak diberikan']);
}

$conn->close();
?>