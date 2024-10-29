<?php
include '../config/db.php'; // Include the database connection
include '../config/session.php'; // Include session to check login status

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_preorder_supplier = $_POST['kode_preorder_supplier'];
    
    // Prepare SQL statement to delete the pre-order supplier
    $sql = "DELETE FROM preorder_supplier WHERE kode_preorder_supplier = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kode_preorder_supplier);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Deletion successful
        echo json_encode(["status" => "success", "message" => "Pre-order supplier berhasil dihapus"]);
    } else {
        // Deletion failed
        echo json_encode(["status" => "error", "message" => "Gagal menghapus pre-order supplier: " . $conn->error]);
    }
    
    $stmt->close();
} else {
    // If not a POST request, return an error
    echo json_encode(["status" => "error", "message" => "Metode request tidak valid"]);
}

$conn->close();
?>
