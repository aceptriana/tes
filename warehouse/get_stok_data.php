<?php
//get_stok_data.php
include '../config/session.php';
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kode_stok_barang'])) {
    $kode_stok_barang = $_POST['kode_stok_barang'];
    
    $sql = "SELECT kode_stok_barang, nama, jenis, warna, jumlah, panjang, roll, 
                   deskripsi_barang, tanggal_masuk_gudang, gambar_barang
            FROM stok_barang 
            WHERE kode_stok_barang = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $kode_stok_barang);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Ubah ini untuk mengembalikan nama file saja, bukan path lengkap
            $gambar_barang = $row['gambar_barang'] ? $row['gambar_barang'] : '';

            echo json_encode([
                'kode_stok_barang' => $row['kode_stok_barang'],
                'nama' => $row['nama'],
                'jenis' => $row['jenis'],
                'warna' => $row['warna'],
                'jumlah' => $row['jumlah'],
                'panjang' => $row['panjang'],
                'roll' => $row['roll'],
                'deskripsi_barang' => $row['deskripsi_barang'],
                'tanggal_masuk_gudang' => $row['tanggal_masuk_gudang'],
                'gambar_barang' => $gambar_barang
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