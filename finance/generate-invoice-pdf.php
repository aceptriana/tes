<?php
require_once('../vendor/autoload.php'); // Load mPDF
include '../config/db.php'; // Include database connection

if (isset($_GET['invoice_id'])) {
    $invoice_id = $_GET['invoice_id'];

    // Fetch the invoice data from the database
    $sql = "SELECT * FROM invoices WHERE invoice_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $invoice_id);
    $stmt->execute();
    $invoice = $stmt->get_result()->fetch_assoc();

    if ($invoice) {
        // Create an instance of mPDF
        $mpdf = new \Mpdf\Mpdf();
        
        // Build HTML content
        $html = '
        <h1 style="text-align:center;">INVOICE</h1>
        <table style="width: 100%; border: none; margin-bottom: 20px;">
            <tr>
                <td><strong>No Faktur:</strong> ' . $invoice['no_faktur'] . '</td>
            </tr>
            <tr>
                <td><strong>Pembeli:</strong></td>
            </tr>
            <tr>
                <td>Nama: ' . $invoice['buyer_name'] . '</td>
            </tr>
            <tr>
                <td>Alamat: ' . nl2br($invoice['buyer_address']) . '</td>
            </tr>
            <tr>
                <td>Telepon: ' . $invoice['buyer_phone'] . '</td>
            </tr>
            <tr>
                <td>WA: ' . $invoice['buyer_wa'] . '</td>
            </tr>
            <tr>
                <td>NPWP: ' . $invoice['buyer_npwp'] . '</td>
            </tr>
        </table>
        <h3>Detail Barang</h3>
        <table style="width: 100%; border: 1px solid black; border-collapse: collapse;">
            <tr>
                <th style="border: 1px solid black; padding: 5px;">Deskripsi Barang</th>
                <th style="border: 1px solid black; padding: 5px;">Kuantitas</th>
                <th style="border: 1px solid black; padding: 5px;">Harga per Meter</th>
                <th style="border: 1px solid black; padding: 5px;">Total Harga</th>
            </tr>
            <tr>
                <td style="border: 1px solid black; padding: 5px;">' . $invoice['item_description'] . '</td>
                <td style="border: 1px solid black; padding: 5px;">' . $invoice['item_quantity'] . '</td>
                <td style="border: 1px solid black; padding: 5px;">' . number_format($invoice['item_price'], 2) . '</td>
                <td style="border: 1px solid black; padding: 5px;">' . number_format($invoice['total_price'], 2) . '</td>
            </tr>
        </table>
        <h3>Pembayaran</h3>
        <p>' . nl2br($invoice['payment_info']) . '</p>
        <h3>Keterangan</h3>
        <p>' . nl2br($invoice['notes']) . '</p>
        <p>Bandung, ' . date('d-M-Y', strtotime($invoice['created_at'])) . '</p>
        <p><strong>Dept Keuangan</strong></p>
        <p>________________________</p>
        ';

        // Write HTML to mPDF
        $mpdf->WriteHTML($html);

        // Output the PDF to the browser
        $mpdf->Output('invoice_' . $invoice['invoice_id'] . '.pdf', 'I');
    } else {
        echo "Invoice not found!";
    }
} else {
    echo "No invoice ID provided!";
}
