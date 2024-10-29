<?php
// process_tambah_nota.php
include '../config/db.php';
include '../config/session.php';

// Set error handling to throw exceptions for all errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Start transaction
$conn->begin_transaction();

try {
    // Validate file size (max 5MB) and allowed types
    $max_file_size = 5 * 1024 * 1024; // 5MB in bytes
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

    // Validate master data fields
    if (empty($_POST['kode_penerimaan']) || 
        empty($_POST['kode_pemasok']) || 
        empty($_POST['nama_barang']) || 
        !is_numeric($_POST['gsm']) || 
        !is_numeric($_POST['width_cm'])) {
        throw new Exception("Semua field master harus diisi dengan benar");
    }

    // Validate if at least one detail item exists
    if (empty($_POST['nama_motif']) || !is_array($_POST['nama_motif'])) {
        throw new Exception("Minimal harus ada satu detail barang");
    }

    // 1. Process master data
    $kode_penerimaan = $_POST['kode_penerimaan'];
    $kode_pemasok = $_POST['kode_pemasok'];
    $nama_barang = trim($_POST['nama_barang']);
    $gsm = (int)$_POST['gsm'];
    $width_cm = (int)$_POST['width_cm'];

    // Validate numeric values in master data
    if ($gsm <= 0 || $width_cm <= 0) {
        throw new Exception("GSM dan lebar harus lebih besar dari 0");
    }

    // Handle design image upload
    $design_image = '';
    if (!isset($_FILES['design_image']) || $_FILES['design_image']['error'] !== 0) {
        throw new Exception("Design image is required");
    }

    // Validate design image
    if ($_FILES['design_image']['size'] > $max_file_size) {
        throw new Exception("Ukuran file design image terlalu besar. Maksimal 5MB");
    }

    if (!in_array($_FILES['design_image']['type'], $allowed_types)) {
        throw new Exception("Tipe file design image tidak valid. Diperbolehkan: JPG, PNG, GIF");
    }

    // Create upload directory if it doesn't exist
    $upload_dir = '../uploads/designs/';
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            throw new Exception("Gagal membuat direktori upload");
        }
    }

    // Generate unique filename for design image
    $file_extension = strtolower(pathinfo($_FILES['design_image']['name'], PATHINFO_EXTENSION));
    $design_image = 'uploads/designs/' . $kode_penerimaan . '_design_' . uniqid() . '.' . $file_extension;

    // Move design image
    if (!move_uploaded_file($_FILES['design_image']['tmp_name'], "../" . $design_image)) {
        throw new Exception("Gagal mengupload design image");
    }

    // Insert master data
    $stmt = $conn->prepare("INSERT INTO nota_penerimaan (kode_penerimaan, kode_pemasok, nama_barang, gsm, width_cm, design_image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiis", $kode_penerimaan, $kode_pemasok, $nama_barang, $gsm, $width_cm, $design_image);

    if (!$stmt->execute()) {
        throw new Exception("Gagal menyimpan data master: " . $stmt->error);
    }

    // 2. Process detail data
    $stmt_detail = $conn->prepare("INSERT INTO color_details (kode_penerimaan, photo_path, nama_motif, warna_motif, roll, roll_length, small_roll) VALUES (?, ?, ?, ?, ?, ?, ?)");

    for ($i = 0; $i < count($_POST['nama_motif']); $i++) {
        // Validate detail fields
        if (empty($_POST['nama_motif'][$i]) || empty($_POST['warna_motif'][$i]) || 
            !isset($_POST['roll'][$i]) || !isset($_POST['roll_length'][$i])) {
            throw new Exception("Data detail #" . ($i + 1) . " tidak lengkap");
        }

        // Validate numeric values
        if (!is_numeric($_POST['roll'][$i]) || $_POST['roll'][$i] <= 0) {
            throw new Exception("Jumlah roll harus lebih besar dari 0 pada item #" . ($i + 1));
        }

        if (!is_numeric($_POST['roll_length'][$i]) || $_POST['roll_length'][$i] <= 0) {
            throw new Exception("Panjang roll harus lebih besar dari 0 pada item #" . ($i + 1));
        }

        // Validate color photo
        if (!isset($_FILES['photo_path']['tmp_name'][$i]) || $_FILES['photo_path']['error'][$i] !== 0) {
            throw new Exception("Foto warna #" . ($i + 1) . " harus diupload");
        }

        if ($_FILES['photo_path']['size'][$i] > $max_file_size) {
            throw new Exception("Ukuran file foto warna #" . ($i + 1) . " terlalu besar. Maksimal 5MB");
        }

        if (!in_array($_FILES['photo_path']['type'][$i], $allowed_types)) {
            throw new Exception("Tipe file foto warna #" . ($i + 1) . " tidak valid. Diperbolehkan: JPG, PNG, GIF");
        }

        // Process color photo upload
        $upload_dir = '../uploads/colors/';
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                throw new Exception("Gagal membuat direktori upload untuk foto warna");
            }
        }

        $file_extension = strtolower(pathinfo($_FILES['photo_path']['name'][$i], PATHINFO_EXTENSION));
        $photo_path = 'uploads/colors/' . $kode_penerimaan . '_color_' . ($i + 1) . '_' . uniqid() . '.' . $file_extension;

        if (!move_uploaded_file($_FILES['photo_path']['tmp_name'][$i], "../" . $photo_path)) {
            throw new Exception("Gagal mengupload foto warna #" . ($i + 1));
        }

        // Process small roll (optional)
        $small_roll = !empty($_POST['small_roll'][$i]) ? $_POST['small_roll'][$i] : 0;
        if ($small_roll < 0) {
            throw new Exception("Jumlah roll kecil tidak boleh negatif pada item #" . ($i + 1));
        }

        // Insert detail data
        $stmt_detail->bind_param("ssssddd",
            $kode_penerimaan,
            $photo_path,
            $_POST['nama_motif'][$i],
            $_POST['warna_motif'][$i],
            $_POST['roll'][$i],
            $_POST['roll_length'][$i],
            $small_roll
        );

        if (!$stmt_detail->execute()) {
            throw new Exception("Gagal menyimpan detail #" . ($i + 1) . ": " . $stmt_detail->error);
        }
    }

    // If everything is successful, commit the transaction
    if ($conn->commit()) {
        echo json_encode(['status' => 'success', 'message' => 'Nota penerimaan berhasil disimpan']);
    } else {
        throw new Exception("Gagal menyimpan nota penerimaan");
    }

} catch (Exception $e) {
    // If there's an error, rollback the transaction
    $conn->rollback();

    // Delete any uploaded files if they exist
    if (!empty($design_image) && file_exists("../" . $design_image)) {
        unlink("../" . $design_image);
    }

    // Clean up any color photos that might have been uploaded
    $upload_dir = '../uploads/colors/';
    $files = glob($upload_dir . $kode_penerimaan . '_color_*');
    foreach ($files as $file) {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// Restore error handler
restore_error_handler();

$conn->close();
?>