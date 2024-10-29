<?php
include '../config/db.php';
include '../config/session.php';
include 'header.php';
include 'navbar.php';

// Fetch available kode_penerimaan for dropdown
$sql_available_kode = "SELECT DISTINCT kode_penerimaan FROM penyimpanan_barang ORDER BY kode_penerimaan DESC";
$result_available_kode = $conn->query($sql_available_kode);

// Fetch details if kode_penerimaan is selected
$selected_kode = isset($_GET['kode_penerimaan']) ? $_GET['kode_penerimaan'] : null;
$barang_details = null;

if ($selected_kode) {
    $sql_barang_details = "SELECT 
        kode_penerimaan,
        color_detail_id,
        nama_barang,
        nama_motif,
        gsm,
        width_cm,
        roll,
        roll_length,
        small_roll,
        total_length,
        total_length_with_small_roll
    FROM penyimpanan_barang
    WHERE kode_penerimaan = ?
    ORDER BY color_detail_id";
    
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
    <style>
        .surat-jalan { margin: 20px; padding: 20px; }
        .table th, .table td { vertical-align: middle; }
        @media print {
            .no-print { display: none; }
            .surat-jalan { margin: 0; padding: 0; }
        }
    </style>
</head>
<body>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header no-print">
            <div class="page-header-left">
                <h5>Surat Jalan</h5>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Dokumen Pengiriman</li>
                </ul>
            </div>
        </div>

        <div class="no-print">
            <form method="GET" action="" class="row g-3 align-items-end mb-4">
                <div class="col-md-6">
                    <label for="kode_penerimaan" class="form-label">Pilih Kode Penerimaan</label>
                    <select name="kode_penerimaan" id="kode_penerimaan" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Pilih Kode Penerimaan --</option>
                        <?php while($kode = $result_available_kode->fetch_assoc()): ?>
                            <option value="<?php echo $kode['kode_penerimaan']; ?>" 
                                    <?php echo $selected_kode == $kode['kode_penerimaan'] ? 'selected' : ''; ?>>
                                Kode Penerimaan #<?php echo $kode['kode_penerimaan']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary" onclick="window.print()">Cetak Surat Jalan</button>
                </div>
            </form>
        </div>

        <?php if ($selected_kode && $barang_details && $barang_details->num_rows > 0): ?>
        <div class="surat-jalan">
            <h4 class="text-center mb-4">SURAT JALAN</h4>
            <p class="mb-3">Kode Penerimaan: <?php echo $selected_kode; ?></p>
            
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Color Detail</th>
                        <th>Nama Barang</th>
                        <th>Nama Motif</th>
                        <th>GSM</th>
                        <th>Width (cm)</th>
                        <th>Roll</th>
                        <th>Roll Length (m)</th>
                        <th>Small Roll (m)</th>
                        <th>Total Length (m)</th>
                        <th>Total Length with Small Roll (m)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = $barang_details->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($row['color_detail_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_motif']); ?></td>
                        <td><?php echo htmlspecialchars($row['gsm']); ?></td>
                        <td><?php echo htmlspecialchars($row['width_cm']); ?></td>
                        <td><?php echo htmlspecialchars($row['roll']); ?></td>
                        <td><?php echo htmlspecialchars($row['roll_length']); ?></td>
                        <td><?php echo htmlspecialchars($row['small_roll'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['total_length']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_length_with_small_roll']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="row mt-5">
                <div class="col-4 text-center">
                    <p>Dibuat oleh:</p>
                    <br><br><br>
                    <p>(_____________)</p>
                </div>
                <div class="col-4 text-center">
                    <p>Diperiksa oleh:</p>
                    <br><br><br>
                    <p>(_____________)</p>
                </div>
                <div class="col-4 text-center">
                    <p>Diterima oleh:</p>
                    <br><br><br>
                    <p>(_____________)</p>
                </div>
            </div>
        </div>
        <?php elseif ($selected_kode): ?>
            <div class="alert alert-info">
                Tidak ada data untuk kode penerimaan yang dipilih.
            </div>
        <?php endif; ?>
    </div>
</main>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'footer.php'; ?>