<?php 
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management

// Check if the ID is set in the URL
if (isset($_GET['id'])) {
    $coa_id = $_GET['id'];

    // Prepare the SQL statement to delete the COA record
    $sql = "DELETE FROM coa WHERE coa_id = ?";
    $stmt = $conn->prepare($sql);
    
    // Check if the statement was prepared correctly
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param("i", $coa_id);

    // Execute the delete statement
    if ($stmt->execute()) {
        // Redirect with success message
        header("Location: data-coa.php?status=deleted");
        exit();
    } else {
        // Handle error and redirect with error message
        header("Location: data-coa.php?status=error");
        exit();
    }
} else {
    // Redirect if ID is not set
    header("Location: data-coa.php?status=error");
    exit();
}
?>
