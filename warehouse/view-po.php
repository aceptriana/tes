<?php 
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Check login session

define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
define('UPLOAD_DIR', BASE_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);

// Fungsi untuk memastikan direktori ada dan memiliki izin tulis
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

// Pastikan direktori 'uploads' ada
ensureDirectoryExists(UPLOAD_DIR);

if (isset($_GET['id'])) {
    $kode_prapesan = $_GET['id'];
    
    $sql = "SELECT * FROM prapesan WHERE kode_prapesan = ?"; 
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error menyiapkan pernyataan SQL: " . $conn->error);
    }

    $stmt->bind_param("s", $kode_prapesan);
    $stmt->execute();
    $result = $stmt->get_result();
    $prapesan = $result->fetch_assoc();
    
    if (!$prapesan) {
        die("Pre-order tidak ditemukan.");
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
                    <h5 class="m-b-10">Lihat Detail Pre-order</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="data-po.php">Data Pre-order</a></li>
                    <li class="breadcrumb-item active">Detail Pre-order</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h5>Detail Pre-order</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Kode Pre-order</th>
                            <td><?php echo htmlspecialchars($prapesan['kode_prapesan']); ?></td>
                        </tr>
                        <tr>
                            <th>Kode Pemasok</th>
                            <td><?php echo htmlspecialchars($prapesan['kode_pemasok']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Pesan</th>
                            <td><?php echo htmlspecialchars($prapesan['tanggal_pesan']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Dikirim</th>
                            <td><?php echo htmlspecialchars($prapesan['tanggal_dikirim']); ?></td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td><?php echo htmlspecialchars($prapesan['nama']); ?></td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td><?php echo htmlspecialchars($prapesan['jenis']); ?></td>
                        </tr>
                        <tr>
                            <th>Warna</th>
                            <td><?php echo htmlspecialchars($prapesan['warna']); ?></td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td><?php echo htmlspecialchars($prapesan['jumlah']); ?></td>
                        </tr>
                        <tr>
                            <th>Panjang</th>
                            <td><?php echo htmlspecialchars($prapesan['panjang']); ?></td>
                        </tr>
                        <tr>
                            <th>Roll</th>
                            <td><?php echo htmlspecialchars($prapesan['roll']); ?></td>
                        </tr>
                        <tr>
                            <th>Deskripsi Barang</th>
                            <td><?php echo htmlspecialchars($prapesan['deskripsi_barang']); ?></td>
                        </tr>
                        <tr>
                            <th>Gambar Barang</th>
                            <td>
                                <?php
                                $image_filename = $prapesan['gambar_barang'];
                                $web_path = '../uploads/' . $image_filename; // Path untuk gambar barang

                                // Cek apakah file gambar ada di direktori uploads
                                if (!empty($image_filename) && file_exists(UPLOAD_DIR . $image_filename)):
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
                    <a href="data-po.php" class="btn btn-secondary">Kembali</a>
                    <a href="edit-po.php?kode_prapesan=<?php echo $prapesan['kode_prapesan']; ?>" class="btn btn-primary">Edit</a>
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
