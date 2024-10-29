<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Initialize search query variable
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch transactions based on search query
$sql_transaction = "SELECT kode_prapesan, kode_pemasok, nama_barang, jumlah, harga, status_manajemen 
                   FROM transaksi_pre_order 
                   WHERE nama_barang LIKE ? OR kode_prapesan LIKE ? OR kode_pemasok LIKE ?";

$search_param = "%{$search_query}%";
$stmt_transaction = $conn->prepare($sql_transaction);
$stmt_transaction->bind_param('sss', $search_param, $search_param, $search_param);
$stmt_transaction->execute();
$result_transaction = $stmt_transaction->get_result();
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <h5 class="m-b-10">Riwayat Transaksi Pre-Order</h5>
        </div>

        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="paymentList">
                                    <thead>
                                        <tr>
                                            <th class="wd-30">
                                                <div class="btn-group mb-1">
                                                    <div class="custom-control custom-checkbox ms-1">
                                                        <input type="checkbox" class="custom-control-input" id="checkAllPayment">
                                                        <label class="custom-control-label" for="checkAllPayment"></label>
                                                    </div>
                                                </div>
                                            </th>
                                            <th>Kode Prapesan</th>
                                            <th>Kode Pemasok</th>
                                            <th>Nama Barang</th>
                                            <th>Jumlah</th>
                                            <th>Harga</th>
                                            <th>Status Verifikasi</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result_transaction->num_rows > 0): ?>
                                            <?php while ($row = $result_transaction->fetch_assoc()): ?>
                                                <tr class="single-item">
                                                    <td>
                                                        <div class="item-checkbox ms-1">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input checkbox" id="checkBox_<?php echo $row['kode_prapesan']; ?>">
                                                                <label class="custom-control-label" for="checkBox_<?php echo $row['kode_prapesan']; ?>"></label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><a href="invoice-view.html" class="fw-bold"><?php echo htmlspecialchars($row['kode_prapesan']); ?></a></td>
                                                    <td><?php echo htmlspecialchars($row['kode_pemasok']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['jumlah']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['harga']); ?></td>
                                                    <td>
                                                        <div class="badge bg-soft-success text-success"><?php echo htmlspecialchars($row['status_manajemen']); ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="hstack gap-2 justify-content-end">
                                                            <a href="invoice-view.html" class="avatar-text avatar-md">
                                                                <i class="feather feather-eye"></i>
                                                            </a>
                                                            <div class="dropdown">
                                                                <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                                    <i class="feather feather-more-horizontal"></i>
                                                                </a>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <a class="dropdown-item" href="javascript:void(0)">
                                                                            <i class="feather feather-edit-3 me-3"></i>
                                                                            <span>Edit</span>
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item printBTN" href="javascript:void(0)">
                                                                            <i class="feather feather-printer me-3"></i>
                                                                            <span>Cetak</span>
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item" href="javascript:void(0)">
                                                                            <i class="feather feather-archive me-3"></i>
                                                                            <span>Arsipkan</span>
                                                                        </a>
                                                                    </li>
                                                                    <li class="dropdown-divider"></li>
                                                                    <li>
                                                                        <a class="dropdown-item" href="javascript:void(0)">
                                                                            <i class="feather feather-trash-2 me-3"></i>
                                                                            <span>Hapus</span>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center">Tidak ada transaksi Pre-Order</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
