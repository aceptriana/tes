<?php
//process_edit_rak.php
include '../config/session.php';
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get user input
    $kode_penyimpanan = trim($_POST['kode_penyimpanan']);
    $kode_stok = trim($_POST['kode_stok']);
    $lokasi_penyimpanan = trim($_POST['lokasi_penyimpanan']);
    $nomer_penyimpanan = trim($_POST['nomer_penyimpanan']);
    $tanggal_masuk = trim($_POST['tanggal_masuk']);
    $tanggal_keluar = !empty($_POST['tanggal_keluar']) ? trim($_POST['tanggal_keluar']) : null;
    $nama_barang = trim($_POST['nama_barang']);
    $jenis = trim($_POST['jenis']);
    $warna = trim($_POST['warna']);
    $jumlah = (int)$_POST['jumlah'];
    $panjang = !empty($_POST['panjang']) ? (int)$_POST['panjang'] : null;
    $roll = !empty($_POST['roll']) ? (int)$_POST['roll'] : null;
    $keterangan_barang = trim($_POST['keterangan_barang']);
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
        // SQL query to update the record
        $sql = "UPDATE penyimpanan_barang SET 
                kode_stok = ?, 
                lokasi_penyimpanan = ?, 
                nomer_penyimpanan = ?, 
                tanggal_masuk = ?, 
                tanggal_keluar = ?, 
                nama_barang = ?, 
                jenis = ?, 
                warna = ?, 
                jumlah = ?, 
                panjang = ?, 
                roll = ?, 
                keterangan_barang = ?, 
                gambar_barang = ?
                WHERE kode_penyimpanan = ?";

        // Prepare and bind the statement
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssssssssiiisss", 
            $kode_stok, 
            $lokasi_penyimpanan, 
            $nomer_penyimpanan, 
            $tanggal_masuk, 
            $tanggal_keluar, 
            $nama_barang, 
            $jenis, 
            $warna, 
            $jumlah,
            $panjang,
            $roll,
            $keterangan_barang,
            $gambar_barang,
            $kode_penyimpanan
        );

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        header("Location: data-rak.php?status=success&message=Rak berhasil diupdate");
        exit;

    } catch (Exception $e) {
        error_log($e->getMessage());
        header("Location: edit-rak.php?kode_penyimpanan=$kode_penyimpanan&status=error&message=Gagal mengupdate rak: " . urlencode($e->getMessage()));
        exit;
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
} else {
    header("Location: data-rak.php");
    exit;
}
?>