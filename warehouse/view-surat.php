<?php 
include '../config/db.php'; // File untuk koneksi database 
include '../config/session.php'; // Cek login 
include 'header.php'; 
include 'navbar.php'; 

if (isset($_GET['id'])) {
    $kode_surat_jalan = trim($_GET['id']);
    
    // Validasi input ID surat jalan
    if (empty($kode_surat_jalan)) {
        header("Location: data-surat.php?status=invalid_id");
        exit;
    }

    $sql = "SELECT * FROM surat_jalan WHERE kode_surat_jalan = ?"; 
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error menyiapkan pernyataan SQL: " . $conn->error);
    }

    $stmt->bind_param("s", $kode_surat_jalan);
    $stmt->execute();
    $result = $stmt->get_result();
    $surat_jalan = $result->fetch_assoc();
    
    if (!$surat_jalan) {
        header("Location: data-surat.php?status=not_found");
        exit;
    }

    // Tutup statement setelah digunakan
    $stmt->close();
} else {
    header("Location: data-surat.php?status=invalid_request");
    exit;
}

?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Lihat Detail Surat Jalan</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="data-surat.php">Data Surat Jalan</a></li>
                    <li class="breadcrumb-item active">Detail Surat Jalan</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h5>Detail Surat Jalan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Kode Surat Jalan</th>
                            <td><?php echo htmlspecialchars($surat_jalan['kode_surat_jalan']); ?></td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td><?php echo htmlspecialchars($surat_jalan['nama']); ?></td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td><?php echo htmlspecialchars($surat_jalan['jenis']); ?></td>
                        </tr>
                        <tr>
                            <th>Warna</th>
                            <td><?php echo htmlspecialchars($surat_jalan['warna']); ?></td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td><?php echo htmlspecialchars($surat_jalan['jumlah']); ?></td>
                        </tr>
                        <tr>
                            <th>Panjang</th>
                            <td><?php echo htmlspecialchars($surat_jalan['panjang']); ?></td>
                        </tr>
                        <tr>
                            <th>Roll</th>
                            <td><?php echo htmlspecialchars($surat_jalan['roll']); ?></td>
                        </tr>
                        <tr>
                            <th>Keterangan Barang</th>
                            <td><?php echo htmlspecialchars($surat_jalan['keterangan_barang']); ?></td>
                        </tr>
                        <tr>
                            <th>Dipesan Oleh</th>
                            <td><?php echo htmlspecialchars($surat_jalan['dipesan_oleh']); ?></td>
                        </tr>
                        <tr>
                            <th>Dikirim Oleh</th>
                            <td><?php echo htmlspecialchars($surat_jalan['dikirim_oleh']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Pengiriman</th>
                            <td><?php echo htmlspecialchars($surat_jalan['tanggal_pengiriman']); ?></td>
                        </tr>
                        <tr>
                            <th>Waktu Pengiriman</th>
                            <td><?php echo htmlspecialchars($surat_jalan['waktu_pengiriman']); ?></td>
                        </tr>
                        <tr>
                            <th>Lokasi Pengiriman</th>
                            <td><?php echo htmlspecialchars($surat_jalan['lokasi_pengiriman']); ?></td>
                        </tr>
                        <tr>
                            <th>Nama Penerima</th>
                            <td><?php echo htmlspecialchars($surat_jalan['nama_penerima']); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="data-surat.php" class="btn btn-secondary">Kembali</a>
                    <a href="edit-surat.php?kode_surat_jalan=<?php echo htmlspecialchars($surat_jalan['kode_surat_jalan']); ?>" class="btn btn-primary">Edit</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php 
include 'footer.php'; 
$conn->close();
?>
