<?php
$host = 'localhost';  // Atau gunakan alamat server database Anda
$dbname = 'revisi_app';  // Ganti dengan nama database Anda
$username = 'root';  // Ganti dengan username database Anda
$password = '';  // Ganti dengan password database Anda

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mengatur charset agar mendukung karakter UTF-8
$conn->set_charset("utf8");
?>
