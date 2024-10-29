<?php
include '../config/db.php';

if (isset($_POST['transaction_id']) && isset($_POST['status'])) {
    $transaction_id = $_POST['transaction_id'];
    $status = $_POST['status'];

    $sql_verify = "UPDATE transaksi_barang SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_verify);
    $stmt->bind_param('si', $status, $transaction_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Transaction updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating transaction!']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request!']);
}
?>
