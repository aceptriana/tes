<?php
include '../config/db.php';
include '../config/session.php';
include 'header.php';
include 'navbar.php';


// Initialize column visibility settings from session or set defaults
if (!isset($_SESSION['column_settings'])) {
    $_SESSION['column_settings'] = [
        'id' => true,
        'kode_penerimaan' => true,
        'color_detail_id' => true,
        'nama_barang' => true,
        'nama_motif' => true,
        'warna_motif' => true,
        'gsm' => true,
        'width_cm' => true,
        'roll' => true,
        'roll_length' => true,
        'small_roll' => true,
        'total_length' => true,
        'total_length_with_small_roll' => true,
        'keterangan_barang' => true,
        'nomor_line' => true,
        'posisi_line' => true
    ];
}

// Initialize search parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Base query
$base_query = "FROM penyimpanan_barang pb 
               JOIN nota_penerimaan n ON pb.kode_penerimaan = n.kode_penerimaan
               JOIN color_details cd ON pb.color_detail_id = cd.id";

// Add search condition if search parameter exists
$where_clause = "";
if (!empty($search)) {
    $search = '%' . $search . '%';
    $where_clause = " WHERE pb.nama_barang LIKE ? 
                      OR pb.nama_motif LIKE ? 
                      OR pb.warna_motif LIKE ?
                      OR pb.nomor_line LIKE ?";
}

// Get total records for pagination
$count_query = "SELECT COUNT(*) as total " . $base_query . $where_clause;
$stmt = $conn->prepare($count_query);
if (!empty($search)) {
    $stmt->bind_param("ssss", $search, $search, $search, $search);
}
$stmt->execute();
$total_records = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);

// Get records for current page
$query = "SELECT pb.id, pb.kode_penerimaan, pb.color_detail_id, 
          pb.nama_barang, pb.nama_motif, pb.warna_motif, pb.gsm, 
          pb.width_cm, pb.roll, pb.roll_length, pb.small_roll,
          pb.total_length, pb.total_length_with_small_roll,
          pb.keterangan_barang, pb.nomor_line, pb.posisi_line " . 
          $base_query . $where_clause . 
          " ORDER BY pb.id DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
if (!empty($search)) {
    $stmt->bind_param("ssssii", $search, $search, $search, $search, $per_page, $offset);
} else {
    $stmt->bind_param("ii", $per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="nxl-container">
    <div class="nxl-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto">
                    <h5 class="m-b-10">Data Penyimpanan Barang</h5>
                </div>
                <div class="col-auto">
                    <a href="tambah-penyimpanan.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Penyimpanan
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_GET['status'])): ?>
            <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php 
                if ($_GET['status'] === 'success') {
                    echo "Data berhasil disimpan!";
                } else {
                    echo "Error: " . htmlspecialchars($_GET['message'] ?? 'Terjadi kesalahan');
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" 
                                   value="<?php echo htmlspecialchars($search ?? ''); ?>" 
                                   placeholder="Cari nama barang, motif, warna, atau nomor line...">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Penyimpanan</th>
                                <th>Kode Penerimaan</th>
                                <th>ID Detail Warna</th>
                                <th>Nama Barang</th>
                                <th>Nama Motif</th>
                                <th>Warna Motif</th>
                                <th>GSM</th>
                                <th>Width (cm)</th>
                                <th>Roll</th>
                                <th>Roll Length</th>
                                <th>Small Roll</th>
                                <th>Total Length</th>
                                <th>Total Length with Small Roll</th>
                                <th>Keterangan</th>
                                <th>Nomor Line</th>
                                <th>Posisi Line</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['kode_penerimaan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['color_detail_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_motif']); ?></td>
                                        <td><?php echo htmlspecialchars($row['warna_motif']); ?></td>
                                        <td><?php echo htmlspecialchars($row['gsm']); ?></td>
                                        <td><?php echo htmlspecialchars($row['width_cm']); ?></td>
                                        <td><?php echo number_format($row['roll'], 2); ?></td>
                                        <td><?php echo number_format($row['roll_length'], 2); ?></td>
                                        <td><?php echo $row['small_roll'] ? number_format($row['small_roll'], 2) : '-'; ?></td>
                                        <td><?php echo number_format($row['total_length'], 2); ?></td>
                                        <td><?php echo number_format($row['total_length_with_small_roll'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($row['keterangan_barang']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nomor_line']); ?></td>
                                        <td><?php echo htmlspecialchars($row['posisi_line']); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="detail-penyimpanan.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit-penyimpanan.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger"
                                                        onclick="confirmDelete(<?php echo $row['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="17" class="text-center">Tidak ada data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>">
                                    Previous
                                </a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>">
                                    Next
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data penyimpanan ini?')) {
        window.location.href = 'hapus-penyimpanan.php?id=' + id;
    }
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.getElementsByClassName('alert');
        for(let alert of alerts) {
            const closeButton = alert.querySelector('.btn-close');
            if(closeButton) {
                closeButton.click();
            }
        }
    }, 5000);
});
</script>

<?php 
include 'footer.php';
$conn->close();
?>