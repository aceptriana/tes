<?php
include '../config/db.php'; // Include the database connection
include '../config/session.php'; // Include session to check login status

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_prapesan = $_POST['kode_prapesan'];
    
    // Prepare SQL statement to delete the pre-order (PO)
    $sql = "DELETE FROM prapesan WHERE kode_prapesan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kode_prapesan);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Deletion successful
        echo json_encode(["status" => "success", "message" => "Pre-order berhasil dihapus"]);
    } else {
        // Deletion failed
        echo json_encode(["status" => "error", "message" => "Gagal menghapus pre-order: " . $conn->error]);
    }
    
    $stmt->close();
} else {
    // If not a POST request, return an error
    echo json_encode(["status" => "error", "message" => "Metode request tidak valid"]);
}

$conn->close();