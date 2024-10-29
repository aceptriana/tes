<?php
// Include required files
include '../config/session.php';
include '../config/db.php';

// Function to upload and resize image
function uploadAndResizeImage($file, $target_dir, $max_width = 800, $max_height = 600) {
    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is an actual image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return "File bukan gambar.";
    }

    // Check file size (max 5MB)
    if ($file["size"] > 5000000) {
        return "Maaf, file terlalu besar.";
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        return "Maaf, hanya file JPG, JPEG, PNG & GIF yang diizinkan.";
    }

    // Read image
    switch ($imageFileType) {
        case 'jpg':
        case 'jpeg':
            $source = imagecreatefromjpeg($file["tmp_name"]);
            break;
        case 'png':
            $source = imagecreatefrompng($file["tmp_name"]);
            break;
        case 'gif':
            $source = imagecreatefromgif($file["tmp_name"]);
            break;
        default:
            return "Format gambar tidak didukung.";
    }

    // Get original image dimensions
    $width = imagesx($source);
    $height = imagesy($source);
      
    // Calculate aspect ratio
    $ratio = min($max_width / $width, $max_height / $height);
    
    // Calculate new dimensions
    $new_width = $width * $ratio;
    $new_height = $height * $ratio;
    
    // Create new image with calculated dimensions
    $new_image = imagecreatetruecolor($new_width, $new_height);
    
    // Copy and resize old image to new image
    imagecopyresampled($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Save image
    $new_filename = $target_dir . uniqid() . "." . $imageFileType;
    switch ($imageFileType) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($new_image, $new_filename, 90);
            break;
        case 'png':
            imagepng($new_image, $new_filename, 9);
            break;
        case 'gif':
            imagegif($new_image, $new_filename);
            break;
    }

    // Clear memory
    imagedestroy($source);
    imagedestroy($new_image);
    
    return basename($new_filename);
}

// Check if form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get user input
    $kode_preorder_customer = htmlspecialchars(trim($_POST['kode_preorder_customer']));
    $kode_stok_barang = htmlspecialchars(trim($_POST['kode_stok_barang']));
    $tanggal_pesan = htmlspecialchars(trim($_POST['tanggal_pesan']));
    $tanggal_dikirim = !empty($_POST['tanggal_dikirim']) ? htmlspecialchars(trim($_POST['tanggal_dikirim'])) : null;
    $nama = htmlspecialchars(trim($_POST['nama']));
    $jenis = htmlspecialchars(trim($_POST['jenis']));
    $warna = htmlspecialchars(trim($_POST['warna']));
    $jumlah = !empty($_POST['jumlah']) ? (int)$_POST['jumlah'] : null;
    $panjang = !empty($_POST['panjang']) ? (float)$_POST['panjang'] : null;
    $roll = !empty($_POST['roll']) ? (int)$_POST['roll'] : null;
    $deskripsi_barang = htmlspecialchars(trim($_POST['deskripsi_barang']));

    // Handle file upload
    $gambar_barang = "";
    
    // Check if an image was uploaded
    if (isset($_FILES['gambar_barang']) && $_FILES['gambar_barang']['error'] === UPLOAD_ERR_OK) {
        // Verify file extension
        $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
        $filename = $_FILES["gambar_barang"]["name"];
        $filetype = $_FILES["gambar_barang"]["type"];
        $filesize = $_FILES["gambar_barang"]["size"];

        // Verify file extension
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!array_key_exists($ext, $allowed)) {
            die("Error: Silakan pilih format file yang valid (JPG, JPEG, PNG, atau GIF).");
        }

        // Verify file size - max 5MB
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            die("Error: Ukuran file lebih besar dari batas yang diizinkan (5MB).");
        }

        // Verify MIME type
        if (in_array($filetype, $allowed)) {
            // Use uploadAndResizeImage function
            $upload_dir = __DIR__ . "/../uploads/";
            $gambar_barang = uploadAndResizeImage($_FILES["gambar_barang"], $upload_dir);
            
            if (is_string($gambar_barang) && !empty($gambar_barang)) {
                // Upload successful, $gambar_barang contains new filename
            } else {
                die("Error: " . $gambar_barang);
            }
        } else {
            die("Error: Terjadi masalah saat mengupload file Anda. Silakan coba lagi.");
        }
    } elseif (isset($_POST['gambar_barang_hidden']) && !empty($_POST['gambar_barang_hidden'])) {
        // Get image from hidden input if no file was uploaded
        $gambar_barang = htmlspecialchars(trim($_POST['gambar_barang_hidden']));
    } else {
        // No file uploaded and no image from hidden input
        $gambar_barang = "";
    }

    // Validation
    if (empty($kode_preorder_customer) || empty($kode_stok_barang) || 
        empty($tanggal_pesan) || empty($nama) || 
        empty($jenis) || empty($warna) || $jumlah === null || $panjang === null || $roll === null) {
        header("Location: tambah-po-customer.php?status=error&message=Semua field wajib diisi dengan benar");
        exit;
    }

    // Prepared statement to prevent SQL Injection
    $sql = "INSERT INTO preorder_customer (kode_preorder_customer, kode_stok_barang, tanggal_pesan, tanggal_dikirim, nama, jenis, warna, jumlah, panjang, roll, deskripsi_barang, gambar_barang) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('sssssssiidss', 
            $kode_preorder_customer, 
            $kode_stok_barang, 
            $tanggal_pesan, 
            $tanggal_dikirim, 
            $nama, 
            $jenis, 
            $warna, 
            $jumlah, 
            $panjang, 
            $roll, 
            $deskripsi_barang, 
            $gambar_barang
        );

        if ($stmt->execute()) {
            header("Location: data-customer.php?status=success&message=Data pre-order customer berhasil ditambahkan");
            exit;
        } else {
            header("Location: tambah-po-customer.php?status=error&message=Gagal menambahkan data pre-order customer");
            exit;
        }

        $stmt->close();
    } else {
        header("Location: tambah-po-customer.php?status=error&message=Terjadi kesalahan dalam memproses data");
        exit;
    }
}

// Close database connection
$conn->close();
?>