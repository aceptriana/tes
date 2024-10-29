<?php 
include '../config/db.php';
include '../config/session.php';

define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
define('UPLOAD_DIR', BASE_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);

function ensureDirectoryExists($path) {
    if (!file_exists($path)) {
        if (!mkdir($path, 0755, true)) {
            die("Gagal membuat direktori: " . $path);
        }
    } elseif (!is_writable($path)) {
        die("Direktori tidak dapat ditulis: " . $path);
    }
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

ensureDirectoryExists(UPLOAD_DIR);

if (isset($_GET['id'])) {
    $kode_stok_barang = $_GET['id'];
    
    $sql = "SELECT * FROM stok_barang WHERE kode_stok_barang = ?"; 
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error menyiapkan pernyataan SQL: " . $conn->error);
    }

    $stmt->bind_param("s", $kode_stok_barang);
    $stmt->execute();
    $result = $stmt->get_result();
    $stok_barang = $result->fetch_assoc();
    
    if (!$stok_barang) {
        die("Data Stok Barang tidak ditemukan.");
    }
} else {
    die("Permintaan tidak valid.");
}

include 'header.php'; 
include 'navbar.php'; 
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Lihat Detail Barang</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="data-barang.php">Data Barang</a></li>
                    <li class="breadcrumb-item active">Detail Barang</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h5>Detail Stok Barang</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Kode Stok Barang</th>
                            <td><?php echo htmlspecialchars($stok_barang['kode_stok_barang']); ?></td>
                        </tr>
                        <tr>
                            <th>Kode Nota</th>
                            <td><?php echo htmlspecialchars($stok_barang['kode_nota']); ?></td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td><?php echo htmlspecialchars($stok_barang['nama']); ?></td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td><?php echo htmlspecialchars($stok_barang['jenis']); ?></td>
                        </tr>
                        <tr>
                            <th>Warna</th>
                            <td><?php echo htmlspecialchars($stok_barang['warna']); ?></td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td><?php echo htmlspecialchars($stok_barang['jumlah']); ?></td>
                        </tr>
                        <tr>
                            <th>Panjang</th>
                            <td><?php echo htmlspecialchars($stok_barang['panjang']); ?></td>
                        </tr>
                        <tr>
                            <th>Roll</th>
                            <td><?php echo htmlspecialchars($stok_barang['roll']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Masuk Gudang</th>
                            <td><?php echo htmlspecialchars($stok_barang['tanggal_masuk_gudang']); ?></td>
                        </tr>
                        <tr>
                            <th>Deskripsi Barang</th>
                            <td><?php echo htmlspecialchars($stok_barang['deskripsi_barang']); ?></td>
                        </tr>
                        <tr>
                            <th>Gambar Barang</th>
                            <td>
                                <?php
                                $image_filename = $stok_barang['gambar_barang'];
                                $web_path = '../uploads/' . $image_filename; // Sesuaikan path

                                // Output path untuk debugging
                                //echo "Path: " . $_SERVER['DOCUMENT_ROOT'] . '/app/uploads/' . $image_filename . "<br>";

                                // Cek apakah file gambar tersedia di folder uploads
                                if (!empty($image_filename) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/app/uploads/' . $image_filename)):
                                ?>
                                    <img src="<?php echo $web_path; ?>" class="img-fluid" alt="Gambar Barang">
                                <?php else: ?>
                                    <p>Tidak ada gambar tersedia atau file tidak ditemukan</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="data-barang.php" class="btn btn-secondary">Kembali</a>
                    <a href="edit-barang.php?kode_stok_barang=<?php echo $stok_barang['kode_stok_barang']; ?>" class="btn btn-primary">Edit</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php 
include 'footer.php';
$stmt->close();
$conn->close();
?>
