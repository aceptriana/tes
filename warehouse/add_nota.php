<?php
include '../config/session.php'; // Check login session
include '../config/db.php'; // Include your database connection file

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Nota Penerimaan</title>
</head>
<body>
    <h2>Tambah Nota Penerimaan</h2>
    <form action="process_add_nota.php" method="post" enctype="multipart/form-data">
        <label>Design Image:</label>
        <input type="file" name="design_image" required><br><br>

        <label>Weight:</label>
        <input type="text" name="weight" required><br><br>

        <label>Width:</label>
        <input type="text" name="width" required><br><br>

        <label>Roll:</label>
        <input type="text" name="roll" required><br><br>

        <label>Roll Length (meters):</label>
        <input type="text" name="roll_length" required><br><br>

        <input type="submit" value="Tambah Nota">
    </form>
</body>
</html>
