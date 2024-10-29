<?php
require_once '../vendor/autoload.php'; // Ensure the path is correct

// Start output buffering
ob_start();

// Connect to the database
include '../config/db.php'; // Include your database connection
include '../config/session.php'; // Include session management

// Fetch filter parameters
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // Default to the first of the month
$end_date = $_GET['end_date'] ?? date('Y-m-t'); // Default to the last day of the month

// Initialize arrays
$income = [];
$expenses = [];

// Define account codes
$income_codes = [4000, 4010]; // Example account codes for income
$expense_codes = [
    5000, 5010, 5020, 5030, 5040,
    5050, 5060, 5070, 5080, 5090,
    5100, 5110
];

// Fetch data for income
$income_sql = "SELECT c.kode_akun, c.nama_akun, COALESCE(SUM(t.nominal), 0) as total 
               FROM coa c
               LEFT JOIN transactions t ON c.kode_akun = t.kode_coa AND t.jenis = 'Pemasukan' AND t.tanggal BETWEEN '$start_date' AND '$end_date'
               WHERE c.kode_akun IN (" . implode(',', $income_codes) . ")
               GROUP BY c.kode_akun";
$income_result = $conn->query($income_sql);
while ($row = $income_result->fetch_assoc()) {
    $income[] = $row;
}

// Fetch data for expenses
$expense_sql = "SELECT c.kode_akun, c.nama_akun, COALESCE(SUM(t.nominal), 0) as total 
                FROM coa c
                LEFT JOIN transactions t ON c.kode_akun = t.kode_coa AND t.jenis = 'Pengeluaran' AND t.tanggal BETWEEN '$start_date' AND '$end_date'
                WHERE c.kode_akun IN (" . implode(',', $expense_codes) . ")
                GROUP BY c.kode_akun";
$expense_result = $conn->query($expense_sql);
while ($row = $expense_result->fetch_assoc()) {
    $expenses[] = $row;
}

// Calculate totals
$total_income = array_sum(array_column($income, 'total'));
$total_expenses = array_sum(array_column($expenses, 'total'));
$net_profit_loss = $total_income - $total_expenses;

// Create the HTML content for the PDF
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }
        th {
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
    <h1>Laporan Laba Rugi</h1>
    <h2>Sahara Textile</h2>
    <p>Periode: <?php echo htmlspecialchars($start_date); ?> s/d <?php echo htmlspecialchars($end_date); ?></p>

    <h6><strong>Pendapatan</strong></h6>
    <table>
        <thead>
            <tr>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($income as $item): ?>
                <tr>
                    <td><?php echo $item['kode_akun']; ?></td>
                    <td><?php echo $item['nama_akun']; ?></td>
                    <td>Rp. <?php echo number_format($item['total'], 2, ',', '.'); ?>,-</td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2" class="text-right font-weight-bold">Total Pendapatan</td>
                <td class="font-weight-bold">Rp. <?php echo number_format($total_income, 2, ',', '.'); ?>,-</td>
            </tr>
        </tbody>
    </table>

    <h6><strong>Pengeluaran</strong></h6>
    <table>
        <thead>
            <tr>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses as $item): ?>
                <tr>
                    <td><?php echo $item['kode_akun']; ?></td>
                    <td><?php echo $item['nama_akun']; ?></td>
                    <td>Rp. <?php echo number_format($item['total'], 2, ',', '.'); ?>,-</td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2" class="text-right font-weight-bold">Total Pengeluaran</td>
                <td class="font-weight-bold">Rp. <?php echo number_format($total_expenses, 2, ',', '.'); ?>,-</td>
            </tr>
        </tbody>
    </table>

    <h6><strong>Laba/Rugi Bersih</strong></h6>
    <table>
        <thead>
            <tr>
                <th>Laba/Rugi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Rp. <?php echo number_format($net_profit_loss, 2, ',', '.'); ?>,-</td>
            </tr>
        </tbody>
    </table>
</body>
</html>

<?php
// Capture the output
$htmlContent = ob_get_clean();

// Create an instance of mPDF
$mpdf = new \Mpdf\Mpdf();

// Generate the output filename
$outputFilename = "laporan_laba_rugi_{$start_date}_sampai_{$end_date}.pdf";

// Write the HTML content to the PDF
$mpdf->WriteHTML($htmlContent);

// Output the PDF to the browser
$mpdf->Output($outputFilename, 'D'); // Change 'D' to 'I' to display in the browser
?>
