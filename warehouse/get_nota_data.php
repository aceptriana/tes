<?php
//get_nota_data
include '../config/session.php';
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kode_nota'])) {
    $kode_nota = $_POST['kode_nota'];
    
    $sql = "SELECT nama_barang, jenis_barang_dikirim, warna, jumlah, panjang, roll,
     deskripsi_barang, gambar_barang
            FROM nota_penerimaan_barang 
            WHERE kode_nota = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $kode_nota);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Assuming the gambar_barang column contains the path to the image
            $gambar_path = $row['gambar_barang'] ? '../uploads/' . $row['gambar_barang'] : '';

            echo json_encode([
                'nama' => $row['nama_barang'],
                'jenis' => $row['jenis_barang_dikirim'],
                'warna' => $row['warna'],
                'jumlah' => $row['jumlah'],
                'panjang' => $row['panjang'],
                'roll' => $row['roll'],
                'deskripsi_barang' => $row['deskripsi_barang'],
                'gambar_barang' => $gambar_path
            ]);
        } else {
            echo json_encode(['error' => 'Data not found']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Database error']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$conn->close();
?>