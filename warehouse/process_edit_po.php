<?php
// process_edit_po.php
include '../config/session.php';
include '../config/db.php';

// Check if kode_prapesan is provided
if (!isset($_POST['kode_prapesan']) || empty($_POST['kode_prapesan'])) {
    header("Location: edit-po.php?kode_prapesan=" . $_POST['kode_prapesan'] . "&status=error&message=" . urlencode("Kode prapesan tidak valid"));
    exit;
}

$kode_prapesan = $_POST['kode_prapesan'];
$kode_pemasok = $_POST['kode_pemasok'];
$tanggal_pesan = $_POST['tanggal_pesan'];
$tanggal_dikirim = $_POST['tanggal_dikirim'];
$nama = $_POST['nama'];
$jenis = $_POST['jenis'];
$warna = $_POST['warna'];
$jumlah = $_POST['jumlah'];
$panjang = $_POST['panjang'];
$roll = $_POST['roll'];
$deskripsi_barang = $_POST['deskripsi_barang'];
$gambar_lama = $_POST['gambar_lama'];

// File upload handling (if there's a new image uploaded)
if (!empty($_FILES['gambar']['name'])) {
    $gambar_barang = $_FILES['gambar']['name'];
    move_uploaded_file($_FILES['gambar']['tmp_name'], "../uploads/" . $gambar_barang);
} else {
    $gambar_barang = $gambar_lama; // Keep the old image if no new one is uploaded
}

// SQL Query to update the prapesan record
$sql = "UPDATE prapesan SET 
            kode_pemasok = ?, 
            tanggal_pesan = ?, 
            tanggal_dikirim = ?, 
            nama = ?, 
            jenis = ?, 
            warna = ?, 
            jumlah = ?, 
            panjang = ?, 
            roll = ?, 
            deskripsi_barang = ?, 
            gambar_barang = ? 
        WHERE kode_prapesan = ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    header("Location: edit-po.php?kode_prapesan=" . $kode_prapesan . "&status=error&message=" . urlencode("Failed to prepare statement"));
    exit;
}

$stmt->bind_param(
    "ssssssiddsss",
    $kode_pemasok, 
    $tanggal_pesan, 
    $tanggal_dikirim, 
    $nama, 
    $jenis, 
    $warna, 
    $jumlah, 
    $panjang, 
    $roll, 
    $deskripsi_barang, 
    $gambar_barang,
    $kode_prapesan
);

if ($stmt->execute()) {
    header("Location: data-po.php?status=success&message=" . urlencode("Prapesan berhasil diperbarui"));
} else {
    header("Location: edit-po.php?kode_prapesan=" . $kode_prapesan . "&status=error&message=" . urlencode("Gagal mengupdate prapesan: " . $stmt->error));
}

$stmt->close();
$conn->close();
?>
