<?php
include '../config/session.php';
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_stok_barang = $_POST['kode_stok_barang'];
    $kode_nota = $_POST['kode_nota'];
    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'];
    $warna = $_POST['warna'];
    $jumlah = $_POST['jumlah'];
    $panjang = $_POST['panjang'];
    $roll = $_POST['roll'];
    $deskripsi_barang = $_POST['deskripsi_barang'];
    $tanggal_masuk_gudang = $_POST['tanggal_masuk_gudang'];
    $gambar_lama = $_POST['gambar_lama'];

    // Handle file upload
    if ($_FILES['gambar']['error'] == 0) {
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
        } else {
            header("Location: edit-barang.php?kode_stok_barang=$kode_stok_barang&status=error&message=Gagal mengupload gambar");
            exit;
        }
    } else {
        $gambar_barang = $gambar_lama;
    }

    $sql = "UPDATE stok_barang SET 
            kode_nota = ?, 
            nama = ?, 
            jenis = ?, 
            warna = ?, 
            jumlah = ?, 
            panjang = ?, 
            roll = ?, 
            deskripsi_barang = ?, 
            tanggal_masuk_gudang = ?, 
            gambar_barang = ? 
            WHERE kode_stok_barang = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiidssss", $kode_nota, $nama, $jenis, $warna, $jumlah, $panjang, $roll, $deskripsi_barang, $tanggal_masuk_gudang, $gambar_barang, $kode_stok_barang);

    if ($stmt->execute()) {
        header("Location: data-barang.php?status=success&message=Data barang berhasil diupdate");
    } else {
        header("Location: edit-barang.php?kode_stok_barang=$kode_stok_barang&status=error&message=Gagal mengupdate data barang");
    }

    $stmt->close();
} else {
    header("Location: data-barang.php");
}

$conn->close();
?>