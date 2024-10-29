<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

// Check if the id is provided in the URL
if (isset($_GET['id'])) {
    $delete_id = $_GET['id'];

    // Prepare the delete statement
    $delete_sql = "DELETE FROM rekening_bank WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Redirect back to rekening-bank.php with a success status
        header('Location: rekening-bank.php?status=deleted');
        exit();
    } else {
        // Redirect back to rekening-bank.php with an error status
        header('Location: rekening-bank.php?status=error');
        exit();
    }
} else {
    // If no id provided, redirect with error
    header('Location: rekening-bank.php?status=error');
    exit();
}
?>
