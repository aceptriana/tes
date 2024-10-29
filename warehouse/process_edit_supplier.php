<?php
include '../config/db.php'; // File untuk koneksi database
include '../config/session.php'; // Cek login

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize all inputs
    $kode_pemasok = sanitize_input($_POST['kode_pemasok']);
    $nama = sanitize_input($_POST['nama']);
    $kontak = sanitize_input($_POST['kontak']);
    $telepon = sanitize_input($_POST['telepon']);
    $wechat = !empty($_POST['wechat']) ? sanitize_input($_POST['wechat']) : null;
    $email = !empty($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : null;
    $alamat = sanitize_input($_POST['alamat']);

    // Validate required fields
    if (empty($kode_pemasok) || empty($nama) || empty($kontak) || empty($telepon) || empty($alamat)) {
        $_SESSION['sweet_alert'] = [
            'type' => 'error',
            'title' => 'Validasi Gagal!',
            'text' => 'Semua field yang wajib harus diisi.'
        ];
        header("Location: data-supplier.php");
        exit();
    }

    // Validate email if provided
    if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['sweet_alert'] = [
            'type' => 'error',
            'title' => 'Format Email Salah!',
            'text' => 'Format email yang dimasukkan tidak valid.'
        ];
        header("Location: data-supplier.php");
        exit();
    }

    // Prepare SQL statement
    $sql = "UPDATE pemasok SET nama=?, kontak=?, telepon=?, wechat=?, email=?, alamat=? WHERE kode_pemasok=?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $_SESSION['sweet_alert'] = [
            'type' => 'error',
            'title' => 'Error Database!',
            'text' => 'Gagal mempersiapkan statement database.'
        ];
        header("Location: data-supplier.php");
        exit();
    }

    // Bind parameters
    $stmt->bind_param("sssssss", $nama, $kontak, $telepon, $wechat, $email, $alamat, $kode_pemasok);

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['sweet_alert'] = [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data supplier berhasil diperbarui.'
        ];
    } else {
        $_SESSION['sweet_alert'] = [
            'type' => 'error',
            'title' => 'Gagal!',
            'text' => 'Gagal memperbarui data supplier: ' . $stmt->error
        ];
    }

    $stmt->close();
} else {
    $_SESSION['sweet_alert'] = [
        'type' => 'error',
        'title' => 'Metode Tidak Valid!',
        'text' => 'Metode request tidak valid.'
    ];
}

$conn->close();
header("Location: data-supplier.php");
exit();
?>