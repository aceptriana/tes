<?php
include '../config/db.php'; // Include the database connection
include '../config/session.php'; // Include session to check login status

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_surat_jalan = $_POST['kode_surat_jalan'];

    // Prepare SQL statement to delete the surat jalan
    $sql = "DELETE FROM surat_jalan WHERE kode_surat_jalan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kode_surat_jalan);

    // Execute the statement
    if ($stmt->execute()) {
        // Deletion successful
        echo json_encode(["status" => "success", "message" => "Surat jalan berhasil dihapus"]);
    } else {
        // Deletion failed
        echo json_encode(["status" => "error", "message" => "Gagal menghapus surat jalan: " . $conn->error]);
    }

    $stmt->close();
} else {
    // If not a POST request, return an error
    echo json_encode(["status" => "error", "message" => "Metode request tidak valid"]);
}

$conn->close();
?>