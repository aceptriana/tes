<?php
include '../config/db.php'; // Include the database connection
include '../config/session.php'; // Include session to check login status

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_nota = $_POST['kode_nota'];
    $kode_pemasok = $_POST['kode_pemasok'];
    $jenis_barang_dikirim = $_POST['jenis_barang_dikirim'];
    $tanggal_diterima = $_POST['tanggal_diterima'];
    $dikirim_oleh = $_POST['dikirim_oleh'];
    $diterima_oleh = $_POST['diterima_oleh'];
    $nama_barang = $_POST['nama_barang'];
    $jenis = $_POST['jenis'];
    $warna = $_POST['warna'];
    $jumlah = $_POST['jumlah'];
    $panjang = $_POST['panjang'];
    $roll = $_POST['roll'];
    $deskripsi_barang = $_POST['deskripsi_barang'];

    // Cek apakah ada gambar lama
    $gambar_lama = isset($_POST['gambar_lama']) ? $_POST['gambar_lama'] : '';

    // Inisialisasi variabel untuk gambar baru
    $gambar_baru = $gambar_lama; // Default ke gambar lama

    // Jika ada file baru yang diupload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $upload_dir = '../uploads/';
        $gambar_baru = uniqid() . '_' . $_FILES['gambar']['name'];
        $target_file = $upload_dir . $gambar_baru;

        // Proses upload
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            // Hapus gambar lama jika ada dan berbeda dari yang baru
            if ($gambar_lama && $gambar_lama !== $gambar_baru && file_exists($upload_dir . $gambar_lama)) {
                unlink($upload_dir . $gambar_lama);
            }
        } else {
            header("Location: data-nota.php?status=error&message=" . urlencode("Gagal mengunggah gambar"));
            exit;
        }
    }

    // Update query
    $sql = "UPDATE nota_penerimaan_barang SET 
                kode_pemasok = ?, 
                jenis_barang_dikirim = ?, 
                tanggal_diterima = ?, 
                dikirim_oleh = ?, 
                diterima_oleh = ?, 
                nama_barang = ?, 
                jenis = ?, 
                warna = ?, 
                jumlah = ?, 
                panjang = ?, 
                roll = ?, 
                deskripsi_barang = ?, 
                gambar_barang = ? 
            WHERE kode_nota = ?";

    // Eksekusi query
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        header("Location: data-nota.php?status=error&message=" . urlencode("Gagal mempersiapkan query: " . $conn->error));
        exit;
    }

    $stmt->bind_param(
        "ssssssssiddsss", 
        $kode_pemasok, 
        $jenis_barang_dikirim, 
        $tanggal_diterima, 
        $dikirim_oleh, 
        $diterima_oleh, 
        $nama_barang, 
        $jenis, 
        $warna, 
        $jumlah, 
        $panjang, 
        $roll, 
        $deskripsi_barang, 
        $gambar_baru, 
        $kode_nota
    );

    // Eksekusi query
    if ($stmt->execute()) {
        header("Location: data-nota.php?status=success&message=" . urlencode("Nota berhasil diupdate"));
    } else {
        header("Location: data-nota.php?status=error&message=" . urlencode("Gagal mengupdate nota: " . $stmt->error));
    }

    $stmt->close();
} else {
    header("Location: data-nota.php?status=error&message=" . urlencode("Metode request tidak valid"));
}

$conn->close();
?>