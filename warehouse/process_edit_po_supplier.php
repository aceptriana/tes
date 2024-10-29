<?php
//process_edit_po_supplier.php
include '../config/session.php';
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get user input
    $kode_preorder_supplier = trim($_POST['kode_preorder_supplier']);
    $kode_penyimpanan = trim($_POST['kode_penyimpanan']);
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
        $sql = "UPDATE preorder_supplier SET 
                kode_penyimpanan = ?, 
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
        WHERE kode_preorder_supplier = ?";

        // Prepare the statement
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param(
            "ssssssiiisss",
            $kode_penyimpanan,
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
            $kode_preorder_supplier
        );

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Check if any rows were affected
        if ($stmt->affected_rows === 0) {
            throw new Exception("No rows were updated. Please check if the kode_preorder_supplier exists.");
        }

        // Redirect to success page
        header("Location: data-po-supplier.php?status=success&message=PO Supplier berhasil diupdate");
        exit;

    } catch (Exception $e) {
        error_log($e->getMessage());
        header("Location: edit-po-supplier.php?kode_preorder_supplier=$kode_preorder_supplier&status=error&message=Gagal mengupdate PO Supplier: " . urlencode($e->getMessage()));
        exit;
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
} else {
    header("Location: data-po-supplier.php");
    exit;
}
