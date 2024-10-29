<?php
require_once '../config/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to return JSON
header('Content-Type: application/json');

if (isset($_POST['kode_penerimaan'])) {
    $kode_penerimaan = mysqli_real_escape_string($conn, $_POST['kode_penerimaan']);
    
    $query = "SELECT 
                color_detail_id,
                nama_barang,
                nama_motif,
                gsm,
                width_cm,
                roll,
                roll_length,
                small_roll,
                total_length,
                total_length_with_small_roll
              FROM penyimpanan_barang 
              WHERE kode_penerimaan = ?
              ORDER BY color_detail_id";
    
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $kode_penerimaan);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $data = array();
            
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Execute failed: ' . mysqli_stmt_error($stmt)]);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['error' => 'Prepare failed: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['error' => 'No kode_penerimaan provided']);
}

mysqli_close($conn);
?>