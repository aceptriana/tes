<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Initialize variables
$rekeningList = [];
$totalSaldoAwal = 0; // Variable to hold the total initial balance

// Fetch the existing accounts from the database
$sql = "SELECT * FROM rekening_bank";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rekeningList[] = $row;
        $totalSaldoAwal += $row['saldo']; // Add the saldo to the total balance
    }
}
?>
<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Rekening Bank</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Manajemen Rekening</li>
                    <li class="breadcrumb-item">Rekening Bank</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="proses-rekening-bank.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_rekening" class="form-label">Nama Rekening</label>
                                <input type="text" class="form-control" id="nama_rekening" name="nama_rekening" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_pemilik" class="form-label">Nama Pemilik</label>
                                <input type="text" class="form-control" id="nama_pemilik" name="nama_pemilik" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nomor_rekening" class="form-label">Nomor Rekening</label>
                                <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="saldo_awal" class="form-label">Saldo Awal</label>
                                <input type="number" class="form-control" id="saldo_awal" name="saldo_awal" step="0.01" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="tambah_rekening">Tambah Rekening</button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Daftar Rekening</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Rekening</th>
                                <th>Nama Pemilik</th>
                                <th>Nomor Rekening</th>
                                <th>Saldo Awal</th>
                                <th>Saldo Akhir</th>
                                <th>Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php if (!empty($rekeningList)) : ?>
        <?php foreach ($rekeningList as $index => $rekening) : ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo htmlspecialchars($rekening['nama_rekening']); ?></td>
                <td><?php echo htmlspecialchars($rekening['nama_pemilik']); ?></td>
                <td><?php echo htmlspecialchars($rekening['nomor_rekening']); ?></td>
                <td>Rp. <?php echo number_format($rekening['saldo'], 0, ',', '.'); ?></td>
                <td>Rp. <?php echo number_format($rekening['saldo_akhir'], 0, ',', '.'); ?></td>
                <td>
                    <a href="detail-rekening-bank.php?id=<?php echo $rekening['id']; ?>" class="btn btn-info btn-sm">Detail</a>
                    <a href="delete-rekening.php?id=<?php echo $rekening['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="7" class="text-center">Tidak ada rekening yang ditemukan.</td>
        </tr>
    <?php endif; ?>
</tbody>

                    </table>
                    <!-- Display total saldo awal -->
                    <div class="mt-3">
                        <strong>Total Saldo Awal: </strong>Rp. <?php echo number_format($totalSaldoAwal, 0, ',', '.'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
