<?php
include '../config/db.php';
include '../config/session.php';

header('Content-Type: application/json');

if (!isset($_GET['kode_penerimaan'])) {
    echo json_encode(['error' => 'Kode penerimaan tidak ditemukan']);
    exit;
}

try {
    $kode_penerimaan = $_GET['kode_penerimaan'];
    
    // Query untuk mengambil detail warna berdasarkan kode_penerimaan
    $sql = "SELECT cd.* 
            FROM color_details cd
            JOIN nota_penerimaan np ON cd.kode_penerimaan = np.kode_penerimaan
            WHERE np.kode_penerimaan = ?
            AND NOT EXISTS (
                SELECT 1 
                FROM penyimpanan_barang pb 
                WHERE pb.color_detail_id = cd.id
            )";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $kode_penerimaan);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $color_details = [];
    
    while ($row = $result->fetch_assoc()) {
        $color_details[] = [
            'id' => $row['id'],
            'nama_motif' => $row['nama_motif'],
            'warna_motif' => $row['warna_motif'],
            'roll' => $row['roll'],
            'roll_length' => $row['roll_length'],
            'small_roll' => $row['small_roll']
        ];
    }
    
    echo json_encode($color_details);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>