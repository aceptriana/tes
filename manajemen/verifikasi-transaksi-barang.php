<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Fetch transactions for verification including status
$sql_transaction = "SELECT t.id, b.nama_barang, c.customer_name, t.jumlah_beli, t.total_harga, t.alamat_kirim, t.tanggal_transaksi, t.status 
                   FROM transaksi_barang t
                   JOIN customers c ON t.customer_id = c.customer_id
                   JOIN penyimpanan_barang b ON t.kode_penyimpanan = b.kode_penyimpanan";
$result_transaction = $conn->query($sql_transaction);
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Verifikasi Transaksi Customer</h5>
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
                                    <th>Nama Barang</th>
                                    <th>Nama Customer</th>
                                    <th>Jumlah Beli</th>
                                    <th>Total Harga</th>
                                    <th>Alamat Kirim</th>
                                    <th>Tanggal Transaksi</th>
                                    <th>Status</th>
                                    <th>Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($row = $result_transaction->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>{$row['nama_barang']}</td>
                                            <td>{$row['customer_name']}</td>
                                            <td>{$row['jumlah_beli']}</td>
                                            <td>{$row['total_harga']}</td>
                                            <td>{$row['alamat_kirim']}</td>
                                            <td>{$row['tanggal_transaksi']}</td>
                                            <td>{$row['status']}</td>
                                            <td>
                                                <form action='verifikasi-proses.php' method='POST'>
                                                    <input type='hidden' name='transaction_id' value='{$row['id']}'>
                                                    <select name='status' class='form-select' required>
                                                        <option value='Menunggu' ".($row['status'] == 'Menunggu' ? 'selected' : '').">Menunggu</option>
                                                        <option value='Ditolak' ".($row['status'] == 'Ditolak' ? 'selected' : '').">Ditolak</option>
                                                        <option value='Disetujui' ".($row['status'] == 'Disetujui' ? 'selected' : '').">Disetujui</option>
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
