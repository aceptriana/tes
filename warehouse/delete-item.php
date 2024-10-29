<?php
   include '../config/db.php';
   include '../config/session.php';

   // Cek apakah pengguna adalah admin
   if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
       echo "Anda tidak memiliki izin untuk menghapus item.";
       exit;
   }

   // Cek apakah ada ID yang dikirim
   if (isset($_POST['id']) && !empty($_POST['id'])) {
       $id = $_POST['id'];
       $table = $_POST['table']; // 'supplier', 'barang', atau 'transaksi'

       // Hapus item dari database
       $sql = "DELETE FROM $table WHERE id = ?";
       $stmt = $conn->prepare($sql);
       $stmt->bind_param("i", $id);

       if ($stmt->execute()) {
           echo "Item berhasil dihapus.";
       } else {
           echo "Gagal menghapus item: " . $conn->error;
       }

       $stmt->close();
   } else {
       echo "ID tidak valid.";
   }

   $conn->close();
   ?>