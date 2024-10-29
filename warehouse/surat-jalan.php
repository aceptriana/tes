<?php
include '../config/db.php';
include '../config/session.php';
include 'header.php';
include 'navbar.php';

// Ambil daftar kode_penerimaan yang tersedia
$sql_available_kode = "SELECT DISTINCT kode_penerimaan FROM penyimpanan_barang ORDER BY kode_penerimaan DESC";
$result_available_kode = $conn->query($sql_available_kode);


// Fetch details if kode_penerimaan is selected
$selected_kode = isset($_GET['kode_penerimaan']) ? $_GET['kode_penerimaan'] : null;
$barang_details = null;
if ($selected_kode) {
    $sql_barang_details = "SELECT 
    cd.id as color_detail_id,
    pb.nama_barang,
    pb.nama_motif,
    pb.gsm,
    pb.width_cm as width,
    pb.roll,
    pb.roll_length,
    pb.small_roll,
    pb.total_length,
    pb.total_length_with_small_roll
FROM penyimpanan_barang pb
JOIN color_details cd ON pb.color_detail_id = cd.id
WHERE pb.kode_penerimaan = ?
ORDER BY cd.id";
    
    $stmt = $conn->prepare($sql_barang_details);
    $stmt->bind_param("i", $selected_kode);
    $stmt->execute();
    $barang_details = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <main class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left">
                    <h5>Surat Jalan</h5>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">Dokumen Pengiriman</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="GET" action="generate_surat_jalan.php" target="_blank">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="kode_penerimaan" class="form-label">Pilih Kode Penerimaan</label>
                                <select name="kode_penerimaan" id="kode_penerimaan" class="form-select" required>
                                    <option value="">-- Pilih Kode Penerimaan --</option>
                                    <?php while($kode = $result_available_kode->fetch_assoc()): ?>
                                        <option value="<?php echo $kode['kode_penerimaan']; ?>">
                                            Kode Penerimaan #<?php echo $kode['kode_penerimaan']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Generate PDF</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'footer.php'; ?>