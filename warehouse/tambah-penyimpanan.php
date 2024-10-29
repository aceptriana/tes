<?php
include '../config/db.php';
include '../config/session.php';
include 'header.php';
include 'navbar.php';

// Fetch available nota numbers for dropdown
$sql_available_nota = "SELECT DISTINCT np.kode_penerimaan, np.nama_barang, np.created_at, p.nama as nama_pemasok
                      FROM nota_penerimaan np
                      JOIN pemasok p ON np.kode_pemasok = p.kode_pemasok
                      JOIN color_details cd ON np.kode_penerimaan = cd.kode_penerimaan
                      LEFT JOIN (
                          SELECT color_detail_id, SUM(roll) as total_roll_used
                          FROM (
                              SELECT color_detail_id, roll FROM penyimpanan_barang
                              UNION ALL
                              SELECT color_detail_id, roll FROM po_customer
                          ) combined
                          GROUP BY color_detail_id
                      ) used ON cd.id = used.color_detail_id
                      WHERE used.total_roll_used IS NULL OR used.total_roll_used < cd.roll
                      ORDER BY np.created_at DESC";
$result_available_nota = $conn->query($sql_available_nota);

// Fetch details if nota is selected
$selected_nota = isset($_GET['nota']) ? $_GET['nota'] : null;
$nota_details = null;

if ($selected_nota) {
    $sql_nota_details = "SELECT 
        np.kode_penerimaan, np.nama_barang, np.created_at, p.nama as nama_pemasok,
        np.gsm, np.width_cm, cd.id as color_detail_id, cd.nama_motif, cd.warna_motif,
        cd.roll as total_roll, cd.roll_length, cd.small_roll,
        COALESCE(used.total_roll_used, 0) as roll_used,
        (cd.roll - COALESCE(used.total_roll_used, 0)) as roll_available,
        (cd.roll * cd.roll_length) as total_length,
        (cd.roll * cd.roll_length + IFNULL(cd.small_roll, 0)) as total_length_with_small_roll
    FROM nota_penerimaan np
    JOIN color_details cd ON np.kode_penerimaan = cd.kode_penerimaan
    JOIN pemasok p ON np.kode_pemasok = p.kode_pemasok
    LEFT JOIN (
        SELECT color_detail_id, SUM(roll) as total_roll_used
        FROM (
            SELECT color_detail_id, roll FROM penyimpanan_barang
            UNION ALL
            SELECT color_detail_id, roll FROM po_customer
        ) combined
        GROUP BY color_detail_id
    ) used ON cd.id = used.color_detail_id
    WHERE np.kode_penerimaan = ? AND (used.total_roll_used IS NULL OR used.total_roll_used < cd.roll)";
    
    $stmt = $conn->prepare($sql_nota_details);
    $stmt->bind_param("i", $selected_nota);
    $stmt->execute();
    $nota_details = $stmt->get_result();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyimpanan Barang</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .nota-selection { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .data-card { box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .detail-row { padding: 15px; border-bottom: 1px solid #eee; }
        .detail-row:last-child { border-bottom: none; }
        .distribution-form { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 10px; }
    </style>
</head>
<body>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left">
                <h5>Penyimpanan Barang</h5>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Tambah Penyimpanan</li>
                </ul>
            </div>
        </div>

        <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
        <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
            <?php echo htmlspecialchars(urldecode($_GET['message'])); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="nota-selection">
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="nota" class="form-label">Pilih Nota Penerimaan</label>
                    <select name="nota" id="nota" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Pilih Nota --</option>
                        <?php while($nota = $result_available_nota->fetch_assoc()): ?>
                            <option value="<?php echo $nota['kode_penerimaan']; ?>" 
                                    <?php echo $selected_nota == $nota['kode_penerimaan'] ? 'selected' : ''; ?>>
                                Nota #<?php echo $nota['kode_penerimaan']; ?> - 
                                <?php echo htmlspecialchars($nota['nama_barang']); ?> 
                                (<?php echo date('d/m/Y', strtotime($nota['created_at'])); ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </form>
        </div>

        <?php if ($selected_nota && $nota_details && $nota_details->num_rows > 0): ?>
            <?php 
            $first_row = $nota_details->fetch_assoc();
            $nota_details->data_seek(0);
            ?>
            <div class="data-card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Nota #<?php echo $first_row['kode_penerimaan']; ?> - 
                        <?php echo htmlspecialchars($first_row['nama_barang']); ?></h6>
                    <small>Pemasok: <?php echo htmlspecialchars($first_row['nama_pemasok']); ?> 
                        (<?php echo date('d/m/Y', strtotime($first_row['created_at'])); ?>)</small>
                </div>

                <?php while ($row = $nota_details->fetch_assoc()): ?>
                    <div class="detail-row">
                        <form class="distribution-form" action="process_tambah_penyimpanan.php" method="POST">
                            <input type="hidden" name="kode_penerimaan" value="<?php echo $row['kode_penerimaan']; ?>">
                            <input type="hidden" name="color_detail_id" value="<?php echo $row['color_detail_id']; ?>">
                            
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <strong>Motif/Warna:</strong>
                                    <p class="mb-0"><?php echo htmlspecialchars($row['nama_motif']); ?> - 
                                        <?php echo htmlspecialchars($row['warna_motif']); ?></p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Roll Tersedia:</strong>
                                    <p class="mb-0"><?php echo $row['roll_available']; ?> roll @ <?php echo $row['roll_length']; ?>m</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Small Roll:</strong>
                                    <p class="mb-0"><?php echo $row['small_roll'] ?? '0'; ?>m</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Total Panjang:</strong>
                                    <p class="mb-0"><?php echo $row['total_length_with_small_roll']; ?>m</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Data untuk PO Customer</h6>
                                    <div class="form-group mb-3">
                                        <label>Jumlah Roll untuk PO</label>
                                        <input type="number" class="form-control roll-po" name="roll_po" 
                                               max="<?php echo $row['roll_available']; ?>" step="0.01"
                                               data-max-roll="<?php echo $row['roll_available']; ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6>Data untuk Gudang</h6>
                                    <div class="storage-details">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label>Nomor Line</label>
                                                <input type="text" class="form-control" name="nomor_line">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label>Posisi Line</label>
                                                <input type="text" class="form-control" name="posisi_line">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Simpan Distribusi</button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php elseif ($selected_nota): ?>
            <div class="alert alert-info">
                Tidak ada data detail yang tersedia untuk nota yang dipilih.
            </div>
        <?php endif; ?>
    </div>
</main>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.roll-po').forEach(input => {
    input.addEventListener('input', function() {
        const maxRoll = parseFloat(this.dataset.maxRoll);
        const rollPO = parseFloat(this.value) || 0;

        if (rollPO > maxRoll) {
            alert('Jumlah roll PO tidak boleh melebihi total roll tersedia (' + maxRoll + ')');
            this.value = maxRoll;
        }
    });
});

document.querySelectorAll('.distribution-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const rollPO = parseFloat(this.querySelector('.roll-po').value) || 0;
        const maxRoll = parseFloat(this.querySelector('.roll-po').dataset.maxRoll);
        const nomorLine = this.querySelector('[name="nomor_line"]').value;
        const posisiLine = this.querySelector('[name="posisi_line"]').value;

        if (rollPO === maxRoll && (nomorLine || posisiLine)) {
            e.preventDefault();
            alert('Jika semua roll digunakan untuk PO, tidak perlu mengisi data gudang');
        } else if (rollPO < maxRoll && (!nomorLine || !posisiLine)) {
            e.preventDefault();
            alert('Harap isi nomor line dan posisi line untuk sisa roll yang akan disimpan di gudang');
        }
    });
});
</script>

</body>
</html>

<?php include 'footer.php'; ?>