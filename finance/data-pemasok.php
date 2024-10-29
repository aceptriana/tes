<?php
include '../config/db.php'; // File untuk koneksi database
include '../config/session.php'; // Cek login
include 'header.php';
include 'navbar.php';

?>
<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Supplier</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Data Supplier</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'success') {
                    echo "<div class='alert alert-success'>Supplier successfully added.</div>";
                } elseif ($_GET['status'] == 'updated') {
                    echo "<div class='alert alert-success'>Supplier successfully updated.</div>";
                } elseif ($_GET['status'] == 'deleted') {
                    echo "<div class='alert alert-success'>Supplier successfully deleted.</div>";
                } elseif ($_GET['status'] == 'error') {
                    echo "<div class='alert alert-danger'>An error occurred.</div>";
                }
            }

            // Search functionality
            $search_query = isset($_GET['search']) ? $_GET['search'] : '';
            $sql = "SELECT * FROM pemasok WHERE nama LIKE '%$search_query%'"; // Adjust the search condition as needed
            $result = $conn->query($sql);
            ?>

            <!-- Search Supplier -->
            <div class="card mt-3">
                <div class="card-body">
                    <form action="data-supplier.php" method="GET">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="search" class="form-label">Cari Supplier</label>
                                <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info">Cari</button>
                    </form>
                </div>
            </div>

            <!-- Supplier Table -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Supplier</th>
                                    <th>Nama Supplier</th>
                                    <th>Kontak Person</th>
                                    <th>Telepon</th>
                                    <th>Email</th>
                                    <th>Alamat</th>
                               
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$no}</td>
                                                <td>{$row['kode_pemasok']}</td>
                                                <td>{$row['nama']}</td>
                                                <td>{$row['kontak']}</td>
                                                <td><a href='https://api.whatsapp.com/send?phone={$row['telepon']}' target='_blank'>{$row['telepon']}</a></td>
                                                <td><a href='mailto:{$row['email']}'>{$row['email']}</a></td>
                                                <td>{$row['alamat']}</td>
                                              
                                              </tr>";
                                        $no++;
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center'>No suppliers found.</td></tr>";
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
