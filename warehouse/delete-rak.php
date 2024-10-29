<?php
include '../config/db.php'; // Include the database connection
include '../config/session.php'; // Include session to check login status

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_penyimpanan = $_POST['kode_penyimpanan'];
    
    // Prepare SQL statement to delete the rak
    $sql = "DELETE FROM penyimpanan_barang WHERE kode_penyimpanan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kode_penyimpanan);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Deletion successful
        echo json_encode(["status" => "success", "message" => "Rak berhasil dihapus"]);
    } else {
        // Deletion failed
        echo json_encode(["status" => "error", "message" => "Gagal menghapus rak: " . $conn->error]);
    }
    
    $stmt->close();
} else {
    // If not a POST request, return an error
    echo json_encode(["status" => "error", "message" => "Metode request tidak valid"]);
}

$conn->close();
