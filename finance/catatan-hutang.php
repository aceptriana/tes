<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Handle search
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql = "SELECT * FROM hutang WHERE keterangan LIKE ?";
    $search_value = "%" . $search_query . "%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $search_value);
} else {
    $sql = "SELECT * FROM hutang";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Catatan Hutang</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Data Hutang</li>
                </ul>
            </div>
        </div>

        <!-- Form to Add New Debt -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="proses-catatan-hutang.php" method="POST">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="nominal" class="form-label">Nominal</label>
                                <input type="number" class="form-control" id="nominal" name="nominal" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="add_hutang">Tambah Hutang</button>
                    </form>
                </div>
            </div>

            <!-- Search Hutang -->
            <div class="card mt-3">
                <div class="card-body">
                    <form action="catatan-hutang.php" method="GET">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="search" class="form-label">Cari Hutang</label>
                                <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info">Cari</button>
                    </form>
                </div>
            </div>

            <!-- Hutang Table -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Hutang</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Nominal</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>{$row['kode_hutang']}</td>
                                            <td>{$row['tanggal']}</td>
                                            <td>{$row['keterangan']}</td>
                                            <td>Rp " . number_format($row['nominal'], 2, ',', '.') . "</td>
                                            <td>
                                                <a href='edit-hutang.php?id={$row['kode_hutang']}' class='btn btn-warning btn-sm'>Edit</a>
                                                <a href='delete-hutang.php?id={$row['kode_hutang']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus hutang ini?\");'>Hapus</a>
                                            </td>
                                          </tr>";
                                    $no++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
