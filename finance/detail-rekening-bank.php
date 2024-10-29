<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Initialize variables
$rekeningDetails = [];
$totalMasuk = 0; // Variable to hold total incoming money
$totalKeluar = 0; // Variable to hold total outgoing money

// Get the bank account ID from the URL
$rekeningId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the details of the selected bank account
$sqlRekening = "SELECT * FROM rekening_bank WHERE id = ?";
$stmtRekening = $conn->prepare($sqlRekening);
$stmtRekening->bind_param("i", $rekeningId);
$stmtRekening->execute();
$rekeningResult = $stmtRekening->get_result();
$rekening = $rekeningResult->fetch_assoc();

if ($rekening) {
    // Fetch transactions related to the selected bank account
    $sqlTransaksi = "SELECT * FROM riwayat_transaksi WHERE id_rekening = ?";
    $stmtTransaksi = $conn->prepare($sqlTransaksi);
    $stmtTransaksi->bind_param("i", $rekeningId);
    $stmtTransaksi->execute();
    $transaksiResult = $stmtTransaksi->get_result();

    if ($transaksiResult && $transaksiResult->num_rows > 0) {
        while ($transaksi = $transaksiResult->fetch_assoc()) {
            // Determine transaction type based on your rules
            if (strpos($transaksi['keterangan'], 'pemasukan') !== false) {
                $totalMasuk += $transaksi['nominal'];
                $transaksi['jenis'] = 'Pemasukan'; // Assign the correct type
            } elseif (strpos($transaksi['keterangan'], 'pengeluaran') !== false) {
                $totalKeluar += $transaksi['nominal'];
                $transaksi['jenis'] = 'Pengeluaran'; // Assign the correct type
            } else {
                $transaksi['jenis'] = 'Unknown'; // Optional: track unknown types
            }
            $rekeningDetails[] = $transaksi; // Store transaction details
        }
    } else {
        echo "<script>alert('Tidak ada transaksi yang ditemukan!');</script>";
    }
} else {
    echo "<script>alert('Rekening tidak ditemukan!'); window.location.href='rekening-bank.php';</script>";
}
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Detail Rekening Bank</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Manajemen Rekening</li>
                    <li class="breadcrumb-item">Detail Rekening Bank</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Nama Rekening: <?php echo htmlspecialchars($rekening['nama_rekening']); ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">Nama Pemilik: <?php echo htmlspecialchars($rekening['nama_pemilik']); ?></h6>
                    <h6 class="card-subtitle mb-2 text-muted">Nomor Rekening: <?php echo htmlspecialchars($rekening['nomor_rekening']); ?></h6>

                    <h6 class="mt-4">Total Uang Masuk: Rp. <?php echo number_format($totalMasuk, 2, ',', '.'); ?></h6>
                    <h6>Total Uang Keluar: Rp. <?php echo number_format($totalKeluar, 2, ',', '.'); ?></h6>

                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rekeningDetails)) : ?>
                                <?php foreach ($rekeningDetails as $index => $transaksi) : ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($transaksi['tanggal']); ?></td>
                                        <td><?php echo htmlspecialchars($transaksi['jenis']); ?></td>
                                        <td>Rp. <?php echo number_format($transaksi['nominal'], 2, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars($transaksi['keterangan']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada transaksi yang ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
