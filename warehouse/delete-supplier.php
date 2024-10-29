<?php
include '../config/db.php';
include '../config/session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_pemasok = $_POST['kode_pemasok'];
    
    // Prepare SQL statement to delete the supplier
    $sql = "DELETE FROM pemasok WHERE kode_pemasok = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kode_pemasok);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Deletion successful
        echo json_encode(["status" => "success", "message" => "Supplier berhasil dihapus"]);
    } else {
        // Deletion failed
        echo json_encode(["status" => "error", "message" => "Gagal menghapus supplier: " . $conn->error]);
    }
    
    $stmt->close();
} else {
    // If not a POST request, return an error
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>