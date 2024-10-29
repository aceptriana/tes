<?php
require_once '../vendor/autoload.php';
ob_start();
include '../config/db.php';
include '../config/session.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$kode_transaksi = $_GET['kode_transaksi'] ?? '';

if (empty($kode_transaksi)) {
    die("Kode transaksi tidak ditemukan.");
}

$sql_transaction = "SELECT t.kode_transaksi, t.nama_barang, c.customer_name, 
                           t.jumlah_beli, t.total_harga, t.alamat_kirim, t.tanggal_transaksi, t.keterangan, t.bayar, 
                           p.roll AS jumlah_roll, p.roll_length AS total_meteran, p.harga_jual
                   FROM transaksi_barang t
                   JOIN customers c ON t.customer_id = c.customer_id
                   JOIN penyimpanan_barang p ON t.kode_penerimaan = p.kode_penerimaan
                   WHERE t.kode_transaksi = ?";

$stmt = $conn->prepare($sql_transaction);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param('s', $kode_transaksi);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result_transaction = $stmt->get_result();
$transactions = $result_transaction->fetch_all(MYSQLI_ASSOC);

if (empty($transactions)) {
    die("Transaksi tidak ditemukan!");
}

$total_amount = array_sum(array_column($transactions, 'total_harga'));
$sisa = $total_amount - $transactions[0]['bayar'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .main-content {
            margin: 20px;
        }
        .invoice-container {
            border: 1px solid #ccc;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
        }
        .logo {
            max-height: 100px;
            float: right;
        }
    </style>
</head>
<body>

<div class="main-content container-lg">
    <div class="row">
        <div class="col-lg-12">
            <div class="invoice-container">
                <div class="card-body">
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <h2 class="fs-4 fw-bold text-primary">Invoice</h2>
                            <address class="text-muted">
                                JL. Adipati Ukur No. 101<br>
                                Baleendah, Bandung<br>
                                Telp. 0856-5933-6168
                            </address>
                            <br>
                        </div>
                        <div class="lh-lg">
                            <div>
                                <span class="fw-bold text-dark">Invoice:</span>
                                <span class="fw-bold text-primary">#<?php echo htmlspecialchars($transactions[0]['kode_transaksi']); ?></span>
                            </div>
                            <div>
                                <span class="fw-bold text-dark">Tanggal Transaksi:</span>
                                <span class="text-muted"><?php echo htmlspecialchars($transactions[0]['tanggal_transaksi']); ?></span>
                            </div>
                            <div>
                                <span class="fw-bold text-dark">Keterangan:</span>
                                <span class="text-muted"><?php echo htmlspecialchars($transactions[0]['keterangan']); ?></span>
                            </div>
                            <div>
                                <span class="fw-bold text-dark">Alamat Pengiriman:</span>
                                <span class="text-muted"><?php echo htmlspecialchars($transactions[0]['alamat_kirim']); ?></span>
                            </div>
                        </div>
                    </div>

                    <hr class="border-dashed">

                    <div>
                        <h2 class="fs-16 fw-bold text-dark">Invoiced To:</h2>
                        <address class="text-muted">
                            <?php echo htmlspecialchars($transactions[0]['customer_name']); ?><br>
                        </address>
                    </div>

                    <hr class="border-dashed">

                    <div>
                        <h3>Detail Barang</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Jumlah Roll</th>
                                    <th>Total Meteran</th>
                                    <th>Harga Barang</th> <!-- Changed from "Warna" to "Harga Barang" -->
                                    <th>Jumlah Beli</th>
                                    <th>Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($transaction['nama_barang']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['jumlah_roll']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['total_meteran']); ?></td>
                                    <td>Rp. <?php echo number_format($transaction['harga_jual'], 2, ',', '.'); ?>,-</td> <!-- Display harga_jual -->
                                    <td><?php echo htmlspecialchars($transaction['jumlah_beli']); ?></td>
                                    <td>Rp. <?php echo number_format($transaction['total_harga'], 2, ',', '.'); ?>,-</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <table class="table">
                            <tr>
                                <th>Jumlah</th>
                                <td>Rp. <?php echo number_format($total_amount, 2, ',', '.'); ?>,-</td>
                            </tr>
                            <tr>
                                <th>Uang Muka</th>
                                <td>Rp. <?php echo number_format($transactions[0]['bayar'], 2, ',', '.'); ?>,-</td>
                            </tr>
                            <tr>
                                <th>Sisa</th>
                                <td>Rp. <?php echo number_format($sisa, 2, ',', '.'); ?>,-</td>
                            </tr>
                        </table>
                    </div>

                    <div class="footer">
                        <p>Terima kasih atas transaksi Anda!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
<?php
$htmlContent = ob_get_clean();

try {
    $mpdf = new \Mpdf\Mpdf();
    $outputFilename = "invoice_transaksi_{$kode_transaksi}.pdf";
    $mpdf->WriteHTML($htmlContent);
    $mpdf->Output($outputFilename, 'D');
} catch (\Mpdf\MpdfException $e) {
    echo "Could not generate PDF: " . $e->getMessage();
}
?>