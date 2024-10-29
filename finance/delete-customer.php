<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

// Check if the customer ID is set in the URL
if (isset($_GET['id'])) {
    $customer_id = $_GET['id'];

    // Prepare the SQL statement to delete the customer
    $sql = "DELETE FROM customers WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: data-customer.php?status=deleted");
        exit();
    } else {
        header("Location: data-customer.php?status=error");
        exit();
    }
} else {
    header("Location: data-customer.php?status=error");
    exit();
}
?>
