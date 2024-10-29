<?php
// Include required files
//process_tambah_rak.php
include '../config/session.php';
include '../config/db.php';

// Function to upload and resize image (from process_tambah_barang.php)
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

    // Izinkan format file tertentu
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        return "Maaf, hanya file JPG, JPEG, PNG & GIF yang diizinkan.";
    }

    // Baca gambar
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

    // Dapatkan dimensi gambar asli
    $width = imagesx($source);
    $height = imagesy($source);
      
    // Hitung rasio aspek
    $ratio = min($max_width / $width, $max_height / $height);
    
    // Hitung dimensi baru
    $new_width = $width * $ratio;
    $new_height = $height * $ratio;
    
    // Buat gambar baru dengan dimensi yang dihitung
    $new_image = imagecreatetruecolor($new_width, $new_height);
    
    // Salin dan resize gambar lama ke gambar baru
    imagecopyresampled($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Simpan gambar
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

    // Bersihkan memori
    imagedestroy($source);
    imagedestroy($new_image);
    
    return basename($new_filename);
}

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi dan ambil input pengguna
    $kode_penyimpanan = htmlspecialchars(trim($_POST['kode_penyimpanan']));
    $kode_stok_barang = htmlspecialchars(trim($_POST['kode_stok_barang']));
    $lokasi_penyimpanan = htmlspecialchars(trim($_POST['lokasi_penyimpanan']));
    $nomer_penyimpanan = htmlspecialchars(trim($_POST['nomer_penyimpanan']));
    $tanggal_masuk = date('Y-m-d');
    $tanggal_keluar = !empty($_POST['tanggal_keluar']) ? htmlspecialchars(trim($_POST['tanggal_keluar'])) : null;
    $nama_barang = htmlspecialchars(trim($_POST['nama']));
    $jenis = htmlspecialchars(trim($_POST['jenis']));
    $warna = htmlspecialchars(trim($_POST['warna']));
    $jumlah = !empty($_POST['jumlah']) ? (int)$_POST['jumlah'] : null;
    $panjang = !empty($_POST['panjang']) ? (float)$_POST['panjang'] : null;
    $roll = !empty($_POST['roll']) ? (int)$_POST['roll'] : null;
    $keterangan_barang = htmlspecialchars(trim($_POST['deskripsi_barang']));

    // Tangani upload file
    $gambar_barang = "";

    
    // Cek apakah ada gambar yang diupload
    if (isset($_FILES['gambar_barang']) && $_FILES['gambar_barang']['error'] === UPLOAD_ERR_OK) {
        // Verifikasi ekstensi file
        $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
        $filename = $_FILES["gambar_barang"]["name"];
        $filetype = $_FILES["gambar_barang"]["type"];
        $filesize = $_FILES["gambar_barang"]["size"];

        // Verifikasi ekstensi file
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!array_key_exists($ext, $allowed)) {
            die("Error: Silakan pilih format file yang valid (JPG, JPEG, PNG, atau GIF).");
        }

        // Verify file size - max 5MB
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            die("Error: Ukuran file lebih besar dari batas yang diizinkan (5MB).");
        }

        // Verifikasi tipe MIME
        if (in_array($filetype, $allowed)) {
            // Gunakan fungsi uploadAndResizeImage
            $upload_dir = __DIR__ . "/../uploads/";
            $gambar_barang = uploadAndResizeImage($_FILES["gambar_barang"], $upload_dir);
            
            if (is_string($gambar_barang) && !empty($gambar_barang)) {
                // Upload berhasil, $gambar_barang berisi nama file baru
            } else {
                die("Error: " . $gambar_barang);
            }
                } else {
                    die("Error: Terjadi masalah saat mengupload file Anda. Silakan coba lagi.");
                }
            } elseif (isset($_POST['gambar_barang_hidden']) && !empty($_POST['gambar_barang_hidden'])) {
                // Ambil gambar dari input hidden jika tidak ada file yang diupload
                $gambar_barang = htmlspecialchars(trim($_POST['gambar_barang_hidden']));
            } else {
                // Tidak ada file yang diupload dan tidak ada gambar dari hidden input
                $gambar_barang = "";
            }

    // Validasi
    if (empty($kode_penyimpanan) || empty($kode_stok_barang) || empty($lokasi_penyimpanan) || 
        empty($nomer_penyimpanan) || empty($nama_barang) || empty($jenis) || 
        empty($warna) || $jumlah === null || $panjang === null || $roll === null) {
        header("Location: tambah-rak.php?status=error&message=Semua field wajib diisi dengan benar");
        exit;
    }

// Prepared statement untuk mencegah SQL Injection
$sql = "INSERT INTO penyimpanan_barang (kode_penyimpanan, kode_stok, lokasi_penyimpanan, nomer_penyimpanan, tanggal_masuk, tanggal_keluar, nama_barang, jenis, warna, jumlah, panjang, roll, keterangan_barang, gambar_barang) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('sssssssssiidss', $kode_penyimpanan, $kode_stok_barang, $lokasi_penyimpanan, $nomer_penyimpanan, $tanggal_masuk, $tanggal_keluar, $nama_barang, $jenis, $warna, $jumlah, $panjang, $roll, $keterangan_barang, $gambar_barang);

    if ($stmt->execute()) {
        header("Location: data-rak.php?status=success&message=Data penyimpanan barang berhasil ditambahkan");
        exit;
    } else {
        header("Location: tambah-rak.php?status=error&message=Gagal menambahkan data penyimpanan barang");
        exit;
    }

    $stmt->close();
} else {
    header("Location: tambah-rak.php?status=error&message=Terjadi kesalahan dalam memproses data");
    exit;
}
}

// Tutup koneksi database
$conn->close();
?>