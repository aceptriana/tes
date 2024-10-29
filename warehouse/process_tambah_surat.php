<?php
include '../config/session.php'; // Check login session
include '../config/db.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize inputs
    $kode_surat_jalan = trim($_POST['kode_surat_jalan']);
    $nama = trim($_POST['nama']);
    $jenis = trim($_POST['jenis']);
    $warna = trim($_POST['warna']);
    $jumlah = (int) $_POST['jumlah'];
    $panjang = isset($_POST['panjang']) ? (float) $_POST['panjang'] : null; // Null if not provided
    $roll = isset($_POST['roll']) ? (int) $_POST['roll'] : null; // Null if not provided
    $keterangan_barang = trim($_POST['keterangan_barang']);
    $dipesan_oleh = trim($_POST['dipesan_oleh']);
    $dikirim_oleh = trim($_POST['dikirim_oleh']);
    $tanggal_pengiriman = $_POST['tanggal_pengiriman']; // Assume valid date format from input
    $waktu_pengiriman = $_POST['waktu_pengiriman']; // Assume valid time format from input
    $lokasi_pengiriman = trim($_POST['lokasi_pengiriman']);
    $nama_penerima = trim($_POST['nama_penerima']);

    // Validate required fields
    if (empty($kode_surat_jalan) || empty($nama) || empty($jenis) || empty($jumlah) || empty($dipesan_oleh) || empty($dikirim_oleh) || empty($tanggal_pengiriman) || empty($waktu_pengiriman) || empty($lokasi_pengiriman) || empty($nama_penerima)) {
        // Redirect with error if any required field is missing
        header("Location: tambah-surat.php?status=missing_fields");
        exit;
    }

    // Prepare SQL statement
    $sql = "INSERT INTO surat_jalan (kode_surat_jalan, nama, jenis, warna, jumlah, panjang, roll, keterangan_barang, dipesan_oleh, dikirim_oleh, tanggal_pengiriman, waktu_pengiriman, lokasi_pengiriman, nama_penerima) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param('ssssiddsssssss', $kode_surat_jalan, $nama, $jenis, $warna, $jumlah, $panjang, $roll, $keterangan_barang, $dipesan_oleh, $dikirim_oleh, $tanggal_pengiriman, $waktu_pengiriman, $lokasi_pengiriman, $nama_penerima);
        
        // Execute the query
        if ($stmt->execute()) {
            // Redirect to the data-surat page with a success message
            header("Location: data-surat.php?status=success");
            exit;
        } else {
            // If execution fails, redirect to the data-surat page with an error message
            header("Location: data-surat.php?status=error&message=" . urlencode($stmt->error));
            exit;
        }
        // Close the statement
        $stmt->close();
    } else {
        // If the statement couldn't be prepared
        header("Location: tambah-surat.php?status=error&message=" . urlencode("Error preparing the SQL statement."));
        exit;
    }
}
// Close the database connection
$conn->close();
?>
