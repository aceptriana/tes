<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Handle search
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql = "SELECT * FROM customers WHERE customer_name LIKE ? OR contact_person LIKE ?";
    $search_value = "%" . $search_query . "%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $search_value, $search_value);
} else {
    $sql = "SELECT * FROM customers";
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
                    <h5 class="m-b-10">Customer Management</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Tambah Customer</li>
                </ul>
            </div>
        </div>

        <!-- Form to Add New Customer -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="proses-data-customer.php" method="POST">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="customer_name" class="form-label">Nama Customer</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contact_person" class="form-label">Kontak Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="phone" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="credit_limit" class="form-label">Limit Piutang</label>
                                <input type="number" class="form-control" id="credit_limit" name="credit_limit" step="0.01" min="0" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="add_customer">Tambah Customer</button>
                    </form>
                </div>
            </div>

            <!-- Search Customer -->
            <div class="card mt-3">
                <div class="card-body">
                    <form action="data-customer.php" method="GET">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="search" class="form-label">Cari Customer</label>
                                <input type="text" class="form-control" id="search" name="search" value="<?php echo $search_query; ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info">Cari</button>
                    </form>
                </div>
            </div>

            <!-- Customer Table -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Customer</th>
                                    <th>Kontak Person</th>
                                    <th>Telepon</th>
                                    <th>Alamat</th>
                                    <th>Limit Piutang</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>{$row['customer_name']}</td>
                                            <td>{$row['contact_person']}</td>
                                            <td>{$row['phone']}</td>
                                            <td>{$row['address']}</td>
                                           <td>" . 'Rp ' . number_format($row['credit_limit'], 0, ',', '.') . "</td>
                                            <td>
                                                <a href='edit-customer.php?id={$row['customer_id']}' class='btn btn-warning btn-sm'>Edit</a>
                                                <a href='proses-data-customer.php?delete_id={$row['customer_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus customer ini?\");'>Hapus</a>
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
