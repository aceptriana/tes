<?php
include '../config/db.php';
include '../config/session.php';
include 'header.php';
include 'navbar.php';

// Initialize search parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Base query
$base_query = "FROM nota_penerimaan n 
               JOIN pemasok p ON n.kode_pemasok = p.kode_pemasok
               LEFT JOIN (
                   SELECT kode_penerimaan, 
                          COUNT(*) as total_motif,
                          SUM(total_length_with_small_roll) as total_panjang
                   FROM color_details 
                   GROUP BY kode_penerimaan
               ) cd ON n.kode_penerimaan = cd.kode_penerimaan";

// Add search condition if search parameter exists
$where_clause = "";
if (!empty($search)) {
    $search = '%' . $search . '%';
    $where_clause = " WHERE n.kode_penerimaan LIKE ? 
                      OR n.nama_barang LIKE ? 
                      OR p.nama LIKE ?";
}

// Get total records for pagination
$count_query = "SELECT COUNT(*) as total " . $base_query . $where_clause;
$stmt = $conn->prepare($count_query);
if (!empty($search)) {
    $stmt->bind_param("sss", $search, $search, $search);
}
$stmt->execute();
$total_records = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);

// Get records for current page
$query = "SELECT n.*, p.nama as supplier_name, 
          COALESCE(cd.total_motif, 0) as total_motif,
          COALESCE(cd.total_panjang, 0) as total_panjang,
          DATE_FORMAT(n.created_at, '%d/%m/%Y') as tanggal " . 
          $base_query . $where_clause . 
          " ORDER BY n.created_at DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
if (!empty($search)) {
    $stmt->bind_param("sssii", $search, $search, $search, $per_page, $offset);
} else {
    $stmt->bind_param("ii", $per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Sweet Alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<main class="nxl-container">
    <div class="nxl-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto">
                    <h5 class="m-b-10">Data Nota Penerimaan</h5>
                </div>
                <div class="col-auto">
                    <a href="tambah-nota.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Nota
                    </a>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="main-content">
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" 
                                       value="<?php echo htmlspecialchars($search ?? ''); ?>" 
                                       placeholder="Cari kode nota, nama barang, atau supplier...">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kode Nota</th>
                                    <th>Tanggal</th>
                                    <th>Supplier</th>
                                    <th>Nama Barang</th>
                                    <th>GSM</th>
                                    <th>Lebar (cm)</th>
                                    <th>Total Motif</th>
                                    <th>Total Panjang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['kode_penerimaan']); ?></td>
                                            <td><?php echo $row['tanggal']; ?></td>
                                            <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                            <td><?php echo $row['gsm']; ?></td>
                                            <td><?php echo $row['width_cm']; ?></td>
                                            <td><?php echo $row['total_motif']; ?></td>
                                            <td><?php echo number_format($row['total_panjang'], 2); ?> m</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="detail-nota.php?kode=<?php echo $row['kode_penerimaan']; ?>" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="edit-nota.php?kode=<?php echo $row['kode_penerimaan']; ?>" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger"
                                                            onclick="confirmDelete('<?php echo $row['kode_penerimaan']; ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Tidak ada data</td>
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
    </div>
</main>

<script>
function confirmDelete(kodeNota) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form element
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'hapus-nota.php';
            
            // Create input for kode
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'kode';
            input.value = kodeNota;
            
            // Add input to form
            form.appendChild(input);
            
            // Add form to document and submit
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Handle success/error messages
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message');

    if (status === 'success') {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: message || 'Data berhasil dihapus!',
            timer: 2000,
            showConfirmButton: false
        });
    } else if (status === 'error') {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: message || 'Terjadi kesalahan',
            timer: 2000,
            showConfirmButton: false
        });
    }

    // Remove status and message from URL
    if (status) {
        const url = new URL(window.location.href);
        url.searchParams.delete('status');
        url.searchParams.delete('message');
        window.history.replaceState({}, document.title, url);
    }
});
</script>
<?php 
include 'footer.php';
$conn->close();
?>