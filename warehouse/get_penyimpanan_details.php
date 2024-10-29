<?php
include '../config/db.php';

if(isset($_POST['kode_penyimpanan'])){
    $kode_penyimpanan = mysqli_real_escape_string($conn, $_POST['kode_penyimpanan']);
    $query = "SELECT nama_barang, jenis, warna, panjang, roll, keterangan_barang 
              FROM penyimpanan_barang 
              WHERE kode_penyimpanan = '{$kode_penyimpanan}'";
    
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        echo json_encode($row);
    } else {
        echo json_encode(array('error' => 'Data tidak ditemukan'));
    }
} else {
    echo json_encode(array('error' => 'Kode penyimpanan tidak diberikan'));
}
?>