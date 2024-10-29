<?php
// process_tambah_supplier.php
include '../config/db.php'; // File untuk koneksi database
include '../config/session.php'; // Cek login

function generateKodePemasok($conn) {
    $sql = "SELECT MAX(CAST(SUBSTRING(kode_pemasok, 2) AS UNSIGNED)) as max_kode FROM pemasok";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $max_kode = $row['max_kode'];

    if ($max_kode === null) {
        return 'P001';
    } else {
        $next_number = $max_kode + 1;
        return 'P' . str_pad($next_number, 3, '0', STR_PAD_LEFT);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $kode_pemasok = generateKodePemasok($conn);
    $nama = htmlspecialchars($_POST['nama'], ENT_QUOTES, 'UTF-8');
    $kontak = htmlspecialchars($_POST['kontak'], ENT_QUOTES, 'UTF-8');
    $telepon = htmlspecialchars($_POST['telepon'], ENT_QUOTES, 'UTF-8');
    $wechat = !empty($_POST['wechat']) ? htmlspecialchars($_POST['wechat'], ENT_QUOTES, 'UTF-8') : null;
    $email = !empty($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : null;
    $alamat = htmlspecialchars($_POST['alamat'], ENT_QUOTES, 'UTF-8');

    // Query untuk menambahkan supplier
    $sql = "INSERT INTO pemasok (kode_pemasok, nama, kontak, telepon, wechat, email, alamat) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $kode_pemasok, $nama, $kontak, $telepon, $wechat, $email, $alamat);

    // Cek apakah eksekusi berhasil
    if ($stmt->execute()) {
        // Set session untuk SweetAlert sukses
        $_SESSION['sweet_alert'] = [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Supplier berhasil ditambahkan.'
        ];
    } else {
        // Set session untuk SweetAlert error
        $_SESSION['sweet_alert'] = [
            'type' => 'error',
            'title' => 'Gagal!',
            'text' => 'Terjadi kesalahan saat menambahkan supplier.'
        ];
    }
    header("Location: data-supplier.php");
    exit();
}
?>