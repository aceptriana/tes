<?php
include '../config/db.php'; // Include the database connection
include '../config/session.php'; // Include session to check login status

// Function to log errors
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, '../logs/error.log');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_nota = $_POST['kode_nota'];
    
    // Start transaction
    $conn->begin_transaction();

    try {
        // Hapus nota utama
        $sql = "DELETE FROM nota_penerimaan_barang WHERE kode_nota = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $kode_nota);
        
        // Eksekusi penghapusan nota
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        // Check if any rows were affected
        if ($stmt->affected_rows == 0) {
            throw new Exception("Nota tidak ditemukan atau sudah dihapus");
        }

        // If everything is fine, commit the transaction
        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Nota berhasil dihapus"]);
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        $conn->rollback();
        logError("Error deleting nota: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "Gagal menghapus nota: " . $e->getMessage()]);
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
} else {
    echo json_encode(["status" => "error", "message" => "Metode request tidak valid"]);
}
?>