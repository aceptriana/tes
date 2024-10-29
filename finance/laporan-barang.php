<?php
include '../config/db.php'; // Sertakan koneksi database Anda
include '../config/session.php'; // Sertakan manajemen sesi
include 'header.php';
include 'navbar.php';

// Ambil parameter filter jika ada
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Inisialisasi data transaksi kosong
$transactions = [];
$total_barang = 0;
$total_harga = 0;
$total_saldo_akhir = 0;

// Ambil total saldo_akhir dari rekening_bank
$saldo_akhir_sql = "SELECT SUM(saldo_akhir) AS total_saldo_akhir FROM rekening_bank";
$saldo_akhir_result = $conn->query($saldo_akhir_sql);
if ($saldo_akhir_result->num_rows > 0) {
    $saldo_akhir_row = $saldo_akhir_result->fetch_assoc();
    $total_saldo_akhir = $saldo_akhir_row['total_saldo_akhir'];
}

// Ambil data jika filter diterapkan
if ($start_date && $end_date) {
    // Buat query SQL berdasarkan filter
    $sql = "SELECT pb.tanggal_masuk, pb.nama_barang, SUM(pb.jumlah) as total_jumlah, 
                   SUM(pb.harga_barang) as total_harga
            FROM penyimpanan_barang pb
            WHERE pb.tanggal_masuk BETWEEN ? AND ?
            GROUP BY pb.tanggal_masuk, pb.nama_barang";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ambil transaksi berdasarkan filter
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row; // Tambahkan baris tanpa duplikasi
    }
}
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Laporan Barang</h5>
                </div>
            </div>
        </div>

        <!-- Form Filter -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="laporan-barang.php" method="GET">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">Mulai Tanggal</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">Sampai Tanggal</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tampilkan Informasi Filter -->
        <div class="main-content">
            <div class="card mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Dari Tanggal:</h6>
                            <p><?php echo $start_date; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Sampai Tanggal:</h6>
                            <p><?php echo $end_date; ?></p>
                        </div>
                        <div class="col-md-12">
                            <h6>Total Saldo Akhir:</h6>
                            <p>Rp. <?php echo number_format($total_saldo_akhir, 0, ',', '.'); ?>,-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel untuk menampilkan transaksi -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="table-responsive"> 
                        <table class="table table-striped table-bordered table-hover"> 
                            <thead class="thead-light"> 
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Nama Barang</th>
                                    <th>Total Jumlah</th>
                                    <th>Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (!empty($transactions)) {
                                $no = 1;
                                foreach ($transactions as $row) {
                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>{$row['tanggal_masuk']}</td>
                                            <td>{$row['nama_barang']}</td>
                                          <td>{$row['total_jumlah']}</td>
                                            <td>Rp. " . number_format($row['total_harga'], 2, ',', '.') . ",-</td>
                                          </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>Silahkan filter terlebih dahulu</td></tr>";
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
