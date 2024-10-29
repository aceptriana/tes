<?php
require_once '../vendor/autoload.php'; // Ensure the path is correct

// Connect to the database
include '../config/db.php'; // Include your database connection
include '../config/session.php'; // Include session management

// Fetch the transaction code from the query string
$kode_transaksi = $_GET['kode_transaksi'] ?? '';

// Fetch transaction details based on kode_transaksi
$sql_transaction = "SELECT t.kode_transaksi, t.nama_barang, c.customer_name, 
                           t.jumlah_beli, t.total_harga, t.alamat_kirim, t.tanggal_transaksi, t.keterangan, t.bayar, 
                           p.roll AS jumlah_roll, p.panjang AS total_meteran, p.harga_barang
                   FROM transaksi_barang t
                   JOIN customers c ON t.customer_id = c.customer_id
                   JOIN penyimpanan_barang p ON t.kode_penyimpanan = p.kode_penyimpanan
                   WHERE t.kode_transaksi = ?";
$stmt = $conn->prepare($sql_transaction);
$stmt->bind_param('s', $kode_transaksi);
$stmt->execute();
$result_transaction = $stmt->get_result();
$transactions = $result_transaction->fetch_all(MYSQLI_ASSOC);

// Check if transaction exists
if (!$transactions) {
    echo "Transaksi tidak ditemukan!";
    exit();
}

// Calculate total amount
$total_amount = 0;
foreach ($transactions as $transaction) {
    $total_amount += $transaction['total_harga'];
}

// Calculate remaining amount (sisa)
$sisa = $total_amount - $transactions[0]['bayar'];

// Create the HTML content for the invoice
$htmlContent = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Transaksi</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 10px; text-align: left; }
        .table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Invoice #'.htmlspecialchars($transactions[0]['kode_transaksi']).'</h2>
    <p>Tanggal Transaksi: '.htmlspecialchars($transactions[0]['tanggal_transaksi']).'</p>
    <p>Keterangan: '.htmlspecialchars($transactions[0]['keterangan']).'</p>
    <p>Alamat Pengiriman: '.htmlspecialchars($transactions[0]['alamat_kirim']).'</p>
    <h3>Invoiced To:</h3>
    <p>'.htmlspecialchars($transactions[0]['customer_name']).'</p>
    <h3>Detail Barang</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Jumlah Roll</th>
                <th>Total Meteran</th>
                <th>Harga Barang</th>
                <th>Jumlah Beli</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>';

foreach ($transactions as $transaction) {
    $htmlContent .= '<tr>
        <td>'.htmlspecialchars($transaction['nama_barang']).'</td>
        <td>'.htmlspecialchars($transaction['jumlah_roll']).'</td>
        <td>'.htmlspecialchars($transaction['total_meteran']).'</td>
        <td>Rp. '.number_format($transaction['harga_barang'], 2, ',', '.').'</td>
        <td>'.htmlspecialchars($transaction['jumlah_beli']).'</td>
        <td>Rp. '.number_format($transaction['total_harga'], 2, ',', '.').'</td>
    </tr>';
}

$htmlContent .= '</tbody>
    </table>
    <p>Jumlah: Rp. '.number_format($total_amount, 2, ',', '.').'</p>
    <p>Uang Muka: Rp. '.number_format($transactions[0]['bayar'], 2, ',', '.').'</p>
    <p>Sisa: Rp. '.number_format($sisa, 2, ',', '.').'</p>
    <p>Terima kasih atas transaksi Anda!</p>
</body>
</html>';

// Create an instance of mPDF
$mpdf = new \Mpdf\Mpdf();

// Write the HTML content to the PDF
$mpdf->WriteHTML($htmlContent);

// Output the PDF to the browser
$outputFilename = "invoice_transaksi_{$kode_transaksi}.pdf";
$mpdf->Output($outputFilename, 'D'); // Change 'D' to 'I' to display in the browser
?>
