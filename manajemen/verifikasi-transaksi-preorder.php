<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Fetch transactions for verification including status
$sql_transaction = "SELECT id, kode_prapesan, kode_pemasok, harga, status_verifikasi, status_manajemen 
                   FROM transaksi_pre_order";
$result_transaction = $conn->query($sql_transaction);
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Verifikasi Preorder</h5>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Pra Pesan</th>
                                    <th>Kode Pemasok</th>
                                    <th>Harga</th>
                                    <th>Status Finance</th>
                                    <th>Status Manajemen</th>
                                    <th>Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($row = $result_transaction->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>{$row['kode_prapesan']}</td>
                                            <td>{$row['kode_pemasok']}</td>
                                            <td>{$row['harga']}</td>
                                            <td>{$row['status_verifikasi']}</td>
                                            <td>{$row['status_manajemen']}</td>
                                            <td>
                                                <form action='verifikasi-proses-preorder.php' method='POST'>
                                                    <input type='hidden' name='transaction_id' value='{$row['id']}'>
                                                    <select name='status_manajemen' class='form-select' required>
                                                        <option value='Menunggu' ".($row['status_manajemen'] == 'Menunggu' ? 'selected' : '').">Menunggu</option>
                                                        <option value='Ditolak' ".($row['status_manajemen'] == 'Ditolak' ? 'selected' : '').">Ditolak</option>
                                                        <option value='Disetujui' ".($row['status_manajemen'] == 'Disetujui' ? 'selected' : '').">Disetujui</option>
                                                    </select>
                                                    <button type='submit' class='btn btn-success btn-sm'>Verifikasi</button>
                                                </form>
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
