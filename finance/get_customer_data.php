<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];

    // Query untuk mendapatkan data customer
    $query = "SELECT address FROM customers WHERE customer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $customer_data = $result->fetch_assoc();
        echo json_encode([
            'status' => 'success',
            'address' => $customer_data['address']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Customer tidak ditemukan'
        ]);
    }
}
?>