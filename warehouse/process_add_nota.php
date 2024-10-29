<?php
include '../config/db.php'; // Include the database connection
include '../config/session.php'; // Include session to check login status

// Proses upload gambar
$design_image = $_FILES['design_image']['name'];
$target_dir = "app/warehouse/uploads/";  // Path ke folder uploads
$target_file = $target_dir . basename($design_image);

if (move_uploaded_file($_FILES['design_image']['tmp_name'], $target_file)) {
    // Jika gambar berhasil diupload, masukkan data ke database
    $weight = $_POST['weight'];
    $width = $_POST['width'];
    $roll = $_POST['roll'];
    $roll_length = $_POST['roll_length'];
    $total_length = $roll * $roll_length;

    // Menggunakan MySQLi dengan prepared statement
    $stmt = $conn->prepare("INSERT INTO nota_penerimaan (design_image, weight, width, roll, roll_length, total_length) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $target_file, $weight, $width, $roll, $roll_length, $total_length);

    // Cek apakah query berhasil dieksekusi
    if ($stmt->execute()) {
        header("Location: view_nota.php");  // Kembali ke halaman tampil data
        exit();
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    $stmt->close(); // Tutup statement
} else {
    echo "Gagal mengupload gambar.";
}
$conn->close(); // Tutup koneksi
?>
