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
    $kode_nota = $_GET['id'];
    
    $sql = "SELECT * FROM nota_penerimaan_barang WHERE kode_nota = ?"; 
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error menyiapkan pernyataan SQL: " . $conn->error);
    }

    $stmt->bind_param("s", $kode_nota);
    $stmt->execute();
    $result = $stmt->get_result();
    $nota_penerimaan_barang = $result->fetch_assoc();
    
    if (!$nota_penerimaan_barang) {
        die("Nota Penerimaan Barang tidak ditemukan.");
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
                    <h5 class="m-b-10">Lihat Detail Nota</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="data-nota.php">Data Nota</a></li>
                    <li class="breadcrumb-item active">Detail Nota</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h5>Detail Nota Penerimaan Barang</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Kode Nota</th>
                            <td><?php echo htmlspecialchars($nota_penerimaan_barang['kode_nota']); ?></td>
                        </tr>
                        
                        <tr>
                            <th>Kode Pemasok</th>
                            <td><?php echo htmlspecialchars($nota_penerimaan_barang['kode_pemasok']); ?></td>
                        </tr>
                        <tr>
                            <th>Jenis Barang Diterima</th>
                            <td><?php echo htmlspecialchars($nota_penerimaan_barang['jenis_barang_dikirim']); ?></td>
                        </tr>
                        <tr>
                            <th>Nomer Penyimpanan</th>
                            <td><?php echo htmlspecialchars($nota_penerimaan_barang['tanggal_diterima']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Masuk</th>
                            <td><?php echo htmlspecialchars($nota_penerimaan_barang['dikirim_oleh']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Keluar</th>
                            <td><?php echo htmlspecialchars($nota_penerimaan_barang['diterima_oleh']); ?></td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td><?php echo htmlspecialchars($nota_penerimaan_barang['nama_barang']); ?></td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td><?php echo htmlspecialchars($nota_penerimaan_barang['jenis']); ?></td>
                        </tr>
                        <tr>
                            <th>Warna</th>
                            <td>
                                <?php 
                                if (!empty($nota_penerimaan_barang['warna'])) {
                                    echo htmlspecialchars($nota_penerimaan_barang['warna']);
                                } else {
                                    echo "Anda belum memasukkan warna barang";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td><?php echo htmlspecialchars($nota_penerimaan_barang['jumlah']); ?></td>
                        </tr>
                        <tr>
                            <th>Panjang</th>
                            <td><?php echo htmlspecialchars($nota_penerimaan_barang['panjang']); ?></td>
                        </tr>
                        <tr>
                            <th>Roll</th>
                            <td><?php echo htmlspecialchars($nota_penerimaan_barang['roll']); ?></td>
                        </tr>
                        <tr>
                            <th>Deskripsi Barang</th>
                            <td><?php echo htmlspecialchars($nota_penerimaan_barang['deskripsi_barang']); ?></td>
                        </tr>
                            <th>Gambar Barang</th>
                            <td> 
                                 <?php
                    $image_filename = $nota_penerimaan_barang['gambar_barang'];
                    $web_path = '../uploads/' . $image_filename;

                    if (!empty($image_filename) && file_exists(UPLOAD_DIR . $image_filename)):
                    ?>
                        <img src="<?php echo $web_path; ?>" class="img-fluid" alt="Gambar Barang">
                    <?php else: ?>
                        <p>Tidak ada gambar tersedia atau file tidak ditemukan</p>
                    <?php endif; ?>
                </td>
                
                    </table>
                </div>

                <div class="card-footer">
                    <a href="data-nota.php" class="btn btn-secondary">Kembali</a>
                    <a href="edit-nota.php?kode_nota=<?php echo $nota_penerimaan_barang['kode_nota']; ?>" class="btn btn-primary">Edit</a>
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