<?php
include '../config/db.php';
include '../config/session.php';
include 'header.php';
include 'navbar.php';

// Inisialisasi parameter pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Query dasar
$base_query = "FROM po_customer po 
               JOIN nota_penerimaan n ON po.kode_penerimaan = n.kode_penerimaan
               JOIN color_details cd ON po.color_detail_id = cd.id";

// Tambahkan kondisi pencarian jika ada
$where_clause = "";
if (!empty($search)) {
    $search = '%' . $search . '%';
    $where_clause = " WHERE po.nama_barang LIKE ? 
                      OR po.nama_motif LIKE ? 
                      OR po.warna_motif LIKE ?";
}

// Hitung total record untuk paginasi
$count_query = "SELECT COUNT(*) as total " . $base_query . $where_clause;
$stmt = $conn->prepare($count_query);
if (!empty($search)) {
    $stmt->bind_param("sss", $search, $search, $search);
}
$stmt->execute();
$total_records = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);

// Ambil data untuk halaman saat ini
$query = "SELECT po.id, po.kode_penerimaan, po.color_detail_id, 
          po.nama_barang, po.nama_motif, po.warna_motif, 
          po.gsm, po.width_cm, po.roll, po.roll_length, 
          po.small_roll, po.total_length, po.total_length_with_small_roll, 
          po.keterangan_barang, po.status " . 
          $base_query . $where_clause . 
          " ORDER BY po.id DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
if (!empty($search)) {
    $stmt->bind_param("sssii", $search, $search, $search, $per_page, $offset);
} else {
    $stmt->bind_param("ii", $per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="nxl-container">
    <div class="nxl-content">
        <!-- Header Halaman -->
        <div class="page-header">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto">
                    <h5 class="m-b-10">Data PO Customer</h5>
                </div>
                <div class="col-auto">
                    <a href="tambah-po.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah PO
                    </a>
                </div>
            </div>
        </div>

        <!-- Pesan Alert -->
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

        <!-- Pencarian -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" 
                                   value="<?php echo htmlspecialchars($search ?? ''); ?>" 
                                   placeholder="Cari nama barang, motif, atau warna...">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kode Penerimaan</th>
                                <th>Color Detail ID</th>
                                <th>Nama Barang</th>
                                <th>Nama Motif</th>
                                <th>Warna</th>
                                <th>GSM</th>
                                <th>Lebar (cm)</th>
                                <th>Roll</th>
                                <th>Panjang Roll (m)</th>
                                <th>Roll Kecil</th>
                                <th>Total Panjang (m)</th>
                                <th>Total dengan Roll Kecil (m)</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['kode_penerimaan']; ?></td>
                                        <td><?php echo $row['color_detail_id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_motif']); ?></td>
                                        <td><?php echo htmlspecialchars($row['warna_motif']); ?></td>
                                        <td><?php echo $row['gsm']; ?></td>
                                        <td><?php echo $row['width_cm']; ?></td>
                                        <td><?php echo number_format($row['roll'], 2); ?></td>
                                        <td><?php echo number_format($row['roll_length'], 2); ?></td>
                                        <td><?php echo $row['small_roll'] ? number_format($row['small_roll'], 2) : '-'; ?></td>
                                        <td><?php echo number_format($row['total_length'], 2); ?></td>
                                        <td><?php echo number_format($row['total_length_with_small_roll'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($row['keterangan_barang']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $row['status'] === 'pending' ? 'bg-warning' : 'bg-success'; ?>">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="detail-po.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit-po.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger"
                                                        onclick="confirmDelete(<?php echo $row['id']; ?>)"
                                                        title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="16" class="text-center">Tidak ada data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginasi -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>">
                                    Sebelumnya
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
                                    Selanjutnya
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
    if (confirm('Apakah Anda yakin ingin menghapus PO ini?')) {
        window.location.href = 'hapus-po.php?id=' + id;
    }
}

// Sembunyikan alert secara otomatis setelah 5 detik
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