<?php
include '../config/db.php';

if(isset($_GET['query'])){
    $search = mysqli_real_escape_string($conn, $_GET['query']);
    $query = "SELECT kode_penyimpanan as id, CONCAT(kode_penyimpanan, ' - ', nama_barang) as text 
              FROM penyimpanan_barang 
              WHERE kode_penyimpanan LIKE '%{$search}%' OR nama_barang LIKE '%{$search}%' 
              LIMIT 10";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        error_log("MySQL Error: " . mysqli_error($conn));
        die("Error in query");
    }
    
    $data = array();
    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row;
    }
    
    error_log("Search Penyimpanan Result: " . json_encode($data)); // Debugging
    echo json_encode($data);
} else {
    error_log("No query parameter provided for search_penyimpanan.php");
    echo json_encode([]);
}
?>