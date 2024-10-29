<?php
//get_rak_data.php
include '../config/session.php';
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kode_penyimpanan'])) {
    $kode_penyimpanan = $_POST['kode_penyimpanan'];
    
    $sql = "SELECT kode_penyimpanan, lokasi_penyimpanan, nomer_penyimpanan, 
                   tanggal_masuk, tanggal_keluar, nama_barang, jenis, warna, 
                   jumlah, panjang, roll, keterangan_barang, gambar_barang
            FROM penyimpanan_barang 
            WHERE kode_penyimpanan = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $kode_penyimpanan);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Mengembalikan nama file gambar saja, bukan path lengkap
            $gambar_barang = $row['gambar_barang'] ? $row['gambar_barang'] : '';

            echo json_encode([
                'kode_penyimpanan' => $row['kode_penyimpanan'],
                'lokasi_penyimpanan' => $row['lokasi_penyimpanan'],
                'nomer_penyimpanan' => $row['nomer_penyimpanan'],
                'tanggal_masuk' => $row['tanggal_masuk'],
                'tanggal_keluar' => $row['tanggal_keluar'],
                'nama_barang' => $row['nama_barang'],
                'jenis' => $row['jenis'],
                'warna' => $row['warna'],
                'jumlah' => $row['jumlah'],
                'panjang' => $row['panjang'],
                'roll' => $row['roll'],
                'keterangan_barang' => $row['keterangan_barang'],
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