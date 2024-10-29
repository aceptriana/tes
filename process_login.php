<?php
session_start();
include 'config/db.php'; // Koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cari user di database
    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);

    // Cek apakah prepare berhasil
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect berdasarkan role
            switch ($user['role']) {
                case 'admin':
                    header('Location: /app/warehouse/index.php');
                    break;
                case 'finance':
                    header('Location: /app/finance/index.php');
                    break;
                case 'manajemen':
                    header('Location: /app/manajemen/index.php');
                    break;
                default:
                    header('Location: index.php?error=' . urlencode("Role tidak dikenali!"));
                    exit;
            }
            exit;
        } else {
            header('Location: index.php?error=' . urlencode("Password salah!"));
            exit;
        }
    } else {
        header('Location: index.php?error=' . urlencode("User tidak ditemukan!"));
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
