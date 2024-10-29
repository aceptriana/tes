<?php
$password = 'Sahara123`'; // Password asli
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password; // Simpan hash ini ke dalam database
?>
