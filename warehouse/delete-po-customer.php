<?php
include '../config/db.php'; // Include the database connection
include '../config/session.php'; // Include session to check login status

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_preorder_customer = $_POST['kode_preorder_customer'];
    
    // Prepare SQL statement to delete the pre-order customer
    $sql = "DELETE FROM preorder_customer WHERE kode_preorder_customer = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kode_preorder_customer);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Deletion successful
        echo json_encode(["status" => "success", "message" => "Pre-order customer berhasil dihapus"]);
    } else {
        // Deletion failed
        echo json_encode(["status" => "error", "message" => "Gagal menghapus pre-order customer: " . $conn->error]);
    }
    
    $stmt->close();
} else {
    // If not a POST request, return an error
    echo json_encode(["status" => "error", "message" => "Metode request tidak valid"]);
}

$conn->close();
?>
