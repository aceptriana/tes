<?php
include '../config/db.php'; // Include the database connection
include '../config/session.php'; // Include session to check login status

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_stok_barang = $_POST['kode_stok_barang'];
    
    // Prepare SQL statement to delete the nota
    $sql = "DELETE FROM stok_barang WHERE kode_stok_barang = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kode_stok_barang);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Deletion successful
        echo json_encode(["status" => "success", "message" => "Stok Barang berhasil dihapus"]);
    } else {
        // Deletion failed
        echo json_encode(["status" => "error", "message" => "Gagal menghapus Stok Barang: " . $conn->error]);
    }
    
    $stmt->close();
} else {
    // If not a POST request, return an error
    echo json_encode(["status" => "error", "message" => "Metode request tidak valid"]);
}

$conn->close();