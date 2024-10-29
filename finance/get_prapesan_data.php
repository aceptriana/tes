<?php
include '../config/db.php'; // Include database connection

if (isset($_GET['kode_prapesan'])) {
    $kode_prapesan = $_GET['kode_prapesan'];

    $sql = "SELECT kode_pemasok, nama, jumlah, status FROM prapesan WHERE kode_prapesan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kode_prapesan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode([]);
    }
}
?>
