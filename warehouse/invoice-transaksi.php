<?php
require_once '../vendor/autoload.php'; // Ensure the path is correct

// Start output buffering
ob_start();

// Connect to the database
include '../config/db.php'; // Include your database connection
include '../config/session.php'; // Include session management

// Fetch the transaction ID from the query string
$transaction_id = $_GET['id'] ?? 0;

// Fetch transaction details
$sql_transaction = "SELECT t.id, t.kode_penyimpanan, b.nama_barang, c.customer_name, t.jumlah_beli, t.total_harga, t.alamat_kirim, t.tanggal_transaksi 
                   FROM transaksi_barang t
                   JOIN customers c ON t.customer_id = c.customer_id
                   JOIN penyimpanan_barang b ON t.kode_penyimpanan = b.kode_penyimpanan
                   WHERE t.id = ?";
$stmt = $conn->prepare($sql_transaction);
$stmt->bind_param('i', $transaction_id);
$stmt->execute();
$result_transaction = $stmt->get_result();
$transaction = $result_transaction->fetch_assoc();

// Check if transaction exists
if (!$transaction) {
    echo "Transaksi tidak ditemukan!";
    exit();
}

// Create the HTML content for the invoice
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
                            <div class="fs-24 fw-bolder font-montserrat-alt text-uppercase">PT Sahara Textile </div>
                            <address class="text-muted">
                                P.O. Box 18728,<br>
                                Bandung, Indonesia<br>
                                VAT No: 123 456 789
                            </address>
                        </div>
                        <div class="lh-lg">
                            <h2 class="fs-4 fw-bold text-primary">Invoice</h2>
                            <div>
                                <span class="fw-bold text-dark">Invoice:</span>
                                <span class="fw-bold text-primary">#<?php echo htmlspecialchars($transaction['id']); ?></span>
                            </div>
                            <div>
                                <span class="fw-bold text-dark">Tanggal Transaksi:</span>
                                <span class="text-muted"><?php echo htmlspecialchars($transaction['tanggal_transaksi']); ?></span>
                            </div>
                            <div>
                                <span class="fw-bold text-dark">Alamat Pengiriman:</span>
                                <span class="text-muted"><?php echo htmlspecialchars($transaction['alamat_kirim']); ?></span>
                            </div>
                        </div>
                    </div>

                    <hr class="border-dashed">
                    
                    <div>
                        <h2 class="fs-16 fw-bold text-dark">Invoiced To:</h2>
                        <address class="text-muted">
                            <?php echo htmlspecialchars($transaction['customer_name']); ?><br>
                        </address>
                    </div>

                    <hr class="border-dashed">
                    
                    <div>
                        <h3>Detail Barang</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th> <!-- Kolom untuk nama_barang -->
                                    <th>Kode Penyimpanan</th>
                                    <th>Jumlah Beli</th>
                                    <th>Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($transaction['nama_barang']); ?></td> <!-- Tampilkan nama_barang -->
                                    <td><?php echo htmlspecialchars($transaction['kode_penyimpanan']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['jumlah_beli']); ?></td>
                                    <td>Rp. <?php echo number_format($transaction['total_harga'], 2, ',', '.'); ?>,-</td>
                                </tr>
                            </tbody>
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
// Capture the output
$htmlContent = ob_get_clean();

// Create an instance of mPDF
$mpdf = new \Mpdf\Mpdf();

// Generate the output filename
$outputFilename = "invoice_transaksi_{$transaction_id}.pdf";

// Write the HTML content to the PDF
$mpdf->WriteHTML($htmlContent);

// Output the PDF to the browser
$mpdf->Output($outputFilename, 'D'); // Change 'D' to 'I' to display in the browser
?>
