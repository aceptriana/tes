<?php 
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Check login session

// Definisikan path dengan cara yang lebih baik
define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
define('UPLOAD_DIR', BASE_PATH . '/uploads/');
define('UPLOAD_URL', '../uploads/'); // URL relatif untuk akses web

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
    $kode_penyimpanan = $_GET['id'];
    
    $sql = "SELECT * FROM penyimpanan_barang WHERE kode_penyimpanan = ?"; 
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error menyiapkan pernyataan SQL: " . $conn->error);
    }

    $stmt->bind_param("s", $kode_penyimpanan);
    $stmt->execute();
    $result = $stmt->get_result();
    $penyimpanan_barang = $result->fetch_assoc();
    
    if (!$penyimpanan_barang) {
        die("Data penyimpanan tidak ditemukan.");
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
                    <h5 class="m-b-10">Lihat Detail Rak</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="data-rak.php">Data Rak</a></li>
                    <li class="breadcrumb-item active">Detail Rak</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h5>Detail Penyimpanan Barang</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Kode Penyimpanan</th>
                            <td><?php echo htmlspecialchars($penyimpanan_barang['kode_penyimpanan']); ?></td>
                        </tr>
                        <tr>
                            <th>Kode Stok</th>
                            <td><?php echo htmlspecialchars($penyimpanan_barang['kode_stok']); ?></td>
                        </tr>
                        <tr>
                            <th>Lokasi Penyimpanan</th>
                            <td><?php echo htmlspecialchars($penyimpanan_barang['lokasi_penyimpanan']); ?></td>
                        </tr>
                        <tr>
                            <th>Nomer Penyimpanan</th>
                            <td><?php echo htmlspecialchars($penyimpanan_barang['nomer_penyimpanan']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Masuk</th>
                            <td><?php echo htmlspecialchars($penyimpanan_barang['tanggal_masuk']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Keluar</th>
                            <td>
                                <?php 
                                if (empty($penyimpanan_barang['tanggal_keluar'])) {
                                    echo "Barang belum keluar gudang";
                                } else {
                                    echo htmlspecialchars($penyimpanan_barang['tanggal_keluar']);
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td><?php echo htmlspecialchars($penyimpanan_barang['nama_barang']); ?></td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td><?php echo htmlspecialchars($penyimpanan_barang['jenis']); ?></td>
                        </tr>
                        <tr>
                            <th>Warna</th>
                            <td><?php echo htmlspecialchars($penyimpanan_barang['warna']); ?></td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td><?php echo htmlspecialchars($penyimpanan_barang['jumlah']); ?></td>
                        </tr>
                        <tr>
                            <th>Panjang</th>
                            <td><?php echo htmlspecialchars($penyimpanan_barang['panjang']); ?></td>
                        </tr>
                        <tr>
                            <th>Roll</th>
                            <td><?php echo htmlspecialchars($penyimpanan_barang['roll']); ?></td>
                        </tr>
                        <tr>
                            <th>Keterangan Barang</th>
                            <td><?php echo htmlspecialchars($penyimpanan_barang['keterangan_barang']); ?></td>
                        </tr>

                        <tr>
    <th>Gambar Barang</th>
    <td>
        <?php
        // Ambil nama file gambar dari database
        $image_filename = $penyimpanan_barang['gambar_barang'];
        
        if (!empty($image_filename)) {
            // Path absolut untuk pemeriksaan file
            $server_path = UPLOAD_DIR . $image_filename;
            // Path relatif untuk tampilan web
            $web_path = UPLOAD_URL . $image_filename;
            
            // Cek apakah file ada
            if (file_exists($server_path)) {
                echo '<img src="' . htmlspecialchars($web_path) . '" class="img-fluid" alt="Gambar Barang">';
            } else {
                echo "<p>File gambar tidak ditemukan: " . htmlspecialchars($image_filename) . "</p>";
            }
        } else {
            echo "<p>Tidak ada gambar tersedia</p>";
        }
        ?>
    </td>
</tr>


                    </table>
                </div>
                <div class="card-footer">
                    <a href="data-rak.php" class="btn btn-secondary">Kembali</a>
                    <a href="edit-rak.php?kode_penyimpanan=<?php echo $penyimpanan_barang['kode_penyimpanan']; ?>" class="btn btn-primary">Edit</a>
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