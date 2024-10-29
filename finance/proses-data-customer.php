<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

// Handle adding a new customer
if (isset($_POST['add_customer'])) {
    $customer_name = $_POST['customer_name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $credit_limit = $_POST['credit_limit']; // Get the credit limit from the form

    // Update the SQL query to include the credit limit
    $sql = "INSERT INTO customers (customer_name, contact_person, phone, address, credit_limit) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssd', $customer_name, $contact_person, $phone, $address, $credit_limit); // Bind the credit limit as a double

    if ($stmt->execute()) {
        header('Location: data-customer.php?status=added');
    } else {
        header('Location: data-customer.php?status=error');
    }
}

// Handle delete customer
if (isset($_GET['delete_id'])) {
    $customer_id = $_GET['delete_id'];

    $sql = "DELETE FROM customers WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $customer_id);

    if ($stmt->execute()) {
        header('Location: data-customer.php?status=deleted');
    } else {
        header('Location: data-customer.php?status=error');
    }
}
?>
