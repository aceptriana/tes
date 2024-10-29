<?php
include '../config/db.php'; // Include koneksi database
include '../config/session.php'; // Include session untuk memeriksa login status

// Ambil data dari tabel nota_penerimaan
$stmt = $conn->query("SELECT * FROM nota_penerimaan");

// Cek jika query berhasil
if ($stmt === false) {
    echo "Error: " . $conn->error;
    exit();
}

$notas = $stmt->fetch_all(MYSQLI_ASSOC); // Mengambil semua data sebagai array associative
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Nota Penerimaan</title>
</head>
<body>
    <h2>Data Nota Penerimaan</h2>
    <table border="1">
        <tr>
            <th>Kode Penerimaan</th>
            <th>Design Image</th>
            <th>Weight</th>
            <th>Width</th>
            <th>Roll</th>
            <th>Roll Length</th>
            <th>Total Length</th>
            <th>Action</th>
        </tr>

        <?php if (count($notas) > 0): ?>
            <?php foreach ($notas as $nota): ?>
            <tr>
                <td><?php echo $nota['kode_penerimaan']; ?></td>
                <td><img src="<?php echo $nota['design_image']; ?>" alt="Design Image" width="100"></td>
                <td><?php echo $nota['weight']; ?></td>
                <td><?php echo $nota['width']; ?></td>
                <td><?php echo $nota['roll']; ?></td>
                <td><?php echo $nota['roll_length']; ?></td>
                <td><?php echo $nota['total_length']; ?></td>
                <td><a href="add_nota.php">Tambah Nota</a></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">Tidak ada data nota penerimaan yang tersedia.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>

<?php
$conn->close(); // Tutup koneksi
?>
