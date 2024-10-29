<?php
//process_edit_po_customer.php
include '../config/session.php';
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get user input
    $kode_preorder_customer = trim($_POST['kode_preorder_customer']);
    $kode_stok_barang = trim($_POST['kode_stok_barang']);
    $tanggal_pesan = trim($_POST['tanggal_pesan']);
    $tanggal_dikirim = !empty($_POST['tanggal_dikirim']) ? trim($_POST['tanggal_dikirim']) : null;
    $nama = trim($_POST['nama']);
    $jenis = trim($_POST['jenis']);
    $warna = trim($_POST['warna']);
    $jumlah = (int)$_POST['jumlah'];
    $panjang = !empty($_POST['panjang']) ? (int)$_POST['panjang'] : null;
    $roll = !empty($_POST['roll']) ? (int)$_POST['roll'] : null;
    $deskripsi_barang = trim($_POST['deskripsi_barang']);
    $gambar_lama = trim($_POST['gambar_lama']);

    // Handle file upload
    $gambar_barang = $gambar_lama; // Default to old image
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            // Delete old image if exists
            if (!empty($gambar_lama) && file_exists("../uploads/" . $gambar_lama)) {
                unlink("../uploads/" . $gambar_lama);
            }
            $gambar_barang = $new_filename;
        }
    }

    try {
        // SQL query to update the record using MySQLi prepared statement syntax
        $sql = "UPDATE preorder_customer SET 
                kode_stok_barang = ?, 
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
                WHERE kode_preorder_customer = ?";

        // Prepare the statement
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param(
            "ssssssiiisss",
            $kode_stok_barang,
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
            $kode_preorder_customer
        );

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Check if any rows were affected
        if ($stmt->affected_rows === 0) {
            throw new Exception("No rows were updated. Please check if the kode_preorder_customer exists.");
        }

        header("Location: data-customer.php?status=success&message=PO Customer berhasil diupdate");
        exit;

    } catch (Exception $e) {
        error_log($e->getMessage());
        header("Location: edit-po-customer.php?kode_preorder_customer=$kode_preorder_customer&status=error&message=Gagal mengupdate PO Customer: " . urlencode($e->getMessage()));
        exit;
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
} else {
    header("Location: data-customer.php");
    exit;
}
?>