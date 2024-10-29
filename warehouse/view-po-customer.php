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
    $kode_preorder_customer = $_GET['id'];
    
    $sql = "SELECT * FROM preorder_customer WHERE kode_preorder_customer = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error menyiapkan pernyataan SQL: " . $conn->error);
    }

    $stmt->bind_param("s", $kode_preorder_customer);
    $stmt->execute();
    $result = $stmt->get_result();
    $preorder_customer = $result->fetch_assoc();
    
    if (!$preorder_customer) {
        die("Data pre-order tidak ditemukan.");
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
                    <h5 class="m-b-10">Lihat Detail Pre-Order Customer</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="data-po-customer.php">Data Pre-Order Customer</a></li>
                    <li class="breadcrumb-item active">Detail Pre-Order</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h5>Detail Pre-Order Customer</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Kode Pre-Order</th>
                            <td><?php echo htmlspecialchars($preorder_customer['kode_preorder_customer']); ?></td>
                        </tr>
                        <tr>
                            <th>Kode Stok Barang</th>
                            <td><?php echo htmlspecialchars($preorder_customer['kode_stok_barang']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Pesan</th>
                            <td><?php echo htmlspecialchars($preorder_customer['tanggal_pesan']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Dikirim</th>
                            <td><?php echo htmlspecialchars($preorder_customer['tanggal_dikirim']); ?></td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td><?php echo htmlspecialchars($preorder_customer['nama']); ?></td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td><?php echo htmlspecialchars($preorder_customer['jenis']); ?></td>
                        </tr>
                        <tr>
                            <th>Warna</th>
                            <td><?php echo htmlspecialchars($preorder_customer['warna']); ?></td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td><?php echo htmlspecialchars($preorder_customer['jumlah']); ?></td>
                        </tr>
                        <tr>
                            <th>Panjang</th>
                            <td><?php echo htmlspecialchars($preorder_customer['panjang']); ?></td>
                        </tr>
                        <tr>
                            <th>Roll</th>
                            <td><?php echo htmlspecialchars($preorder_customer['roll']); ?></td>
                        </tr>
                        <tr>
                            <th>Deskripsi Barang</th>
                            <td><?php echo htmlspecialchars($preorder_customer['deskripsi_barang']); ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><?php echo htmlspecialchars($preorder_customer['status']); ?></td>
                        </tr>
                        <tr>
                            <th>Gambar Barang</th>
                            <td>
                                <?php
                                $image_filename = $preorder_customer['gambar_barang'];
                                $web_path = '../uploads/' . $image_filename;

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
                    <a href="data-customer.php" class="btn btn-secondary">Kembali</a>
                    <a href="edit-po-customer.php?kode_preorder_customer=<?php echo $preorder_customer['kode_preorder_customer']; ?>" class="btn btn-primary">Edit</a>
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