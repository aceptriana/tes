<?php
include '../config/db.php';

if(isset($_POST['kode_penyimpanan'])) {
    $kode_penyimpanan = $_POST['kode_penyimpanan'];
    
    $stmt = $conn->prepare("SELECT nama_barang, jenis, warna, panjang, roll, keterangan_barang FROM penyimpanan_barang WHERE kode_penyimpanan = ?");
    $stmt->bind_param("s", $kode_penyimpanan);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    echo json_encode($data);
    
    $stmt->close();
}

$conn->close();
?>