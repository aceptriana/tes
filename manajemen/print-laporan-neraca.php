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
$assets = [];
$liabilities = [];
$equities = [];

// Define account codes
$asset_codes = [1000, 1010, 1020, 1030, 1040, 1050, 1060, 1070];
$liability_codes = [2000, 2010, 2020, 2030, 2040];
$equity_codes = [3000, 3010];

// Fetch data for assets
$asset_sql = "SELECT c.kode_akun, c.nama_akun, COALESCE(SUM(t.nominal), 0) as total 
              FROM coa c
              LEFT JOIN transactions t ON c.kode_akun = t.kode_coa AND t.jenis = 'Pemasukan' AND t.tanggal BETWEEN '$start_date' AND '$end_date'
              WHERE c.kode_akun IN (" . implode(',', $asset_codes) . ")
              GROUP BY c.kode_akun";
$asset_result = $conn->query($asset_sql);
while ($row = $asset_result->fetch_assoc()) {
    $assets[] = $row;
}

// Fetch data for liabilities
$liability_sql = "SELECT c.kode_akun, c.nama_akun, COALESCE(SUM(t.nominal), 0) as total 
                  FROM coa c
                  LEFT JOIN transactions t ON c.kode_akun = t.kode_coa AND t.jenis = 'Pengeluaran' AND t.tanggal BETWEEN '$start_date' AND '$end_date'
                  WHERE c.kode_akun IN (" . implode(',', $liability_codes) . ")
                  GROUP BY c.kode_akun";
$liability_result = $conn->query($liability_sql);
while ($row = $liability_result->fetch_assoc()) {
    $liabilities[] = $row;
}

// Fetch data for equities
$equity_sql = "SELECT c.kode_akun, c.nama_akun, COALESCE(SUM(t.nominal), 0) as total 
                FROM coa c
                LEFT JOIN transactions t ON c.kode_akun = t.kode_coa AND t.tanggal BETWEEN '$start_date' AND '$end_date'
                WHERE c.kode_akun IN (" . implode(',', $equity_codes) . ")
                GROUP BY c.kode_akun";
$equity_result = $conn->query($equity_sql);
while ($row = $equity_result->fetch_assoc()) {
    $equities[] = $row;
}

// Calculate totals
$total_assets = array_sum(array_column($assets, 'total'));
$total_liabilities = array_sum(array_column($liabilities, 'total'));
$total_equities = array_sum(array_column($equities, 'total'));

// Create the HTML content for the PDF
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Neraca</title>
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
    <h1>Laporan Neraca</h1>
    <h2>Sahara Textile</h2>
    <p>Periode: <?php echo htmlspecialchars($start_date); ?> s/d <?php echo htmlspecialchars($end_date); ?></p>

    <h6><strong>Aset</strong></h6>
    <table>
        <thead>
            <tr>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assets as $asset): ?>
                <tr>
                    <td><?php echo $asset['kode_akun']; ?></td>
                    <td><?php echo $asset['nama_akun']; ?></td>
                    <td>Rp. <?php echo number_format($asset['total'], 2, ',', '.'); ?>,-</td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2" class="text-right font-weight-bold">Total Aset</td>
                <td class="font-weight-bold">Rp. <?php echo number_format($total_assets, 2, ',', '.'); ?>,-</td>
            </tr>
        </tbody>
    </table>

    <h6><strong>Kewajiban</strong></h6>
    <table>
        <thead>
            <tr>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($liabilities as $liability): ?>
                <tr>
                    <td><?php echo $liability['kode_akun']; ?></td>
                    <td><?php echo $liability['nama_akun']; ?></td>
                    <td>Rp. <?php echo number_format($liability['total'], 2, ',', '.'); ?>,-</td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2" class="text-right font-weight-bold">Total Kewajiban</td>
                <td class="font-weight-bold">Rp. <?php echo number_format($total_liabilities, 2, ',', '.'); ?>,-</td>
            </tr>
        </tbody>
    </table>

    <h6><strong>Ekuitas</strong></h6>
    <table>
        <thead>
            <tr>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equities as $equity): ?>
                <tr>
                    <td><?php echo $equity['kode_akun']; ?></td>
                    <td><?php echo $equity['nama_akun']; ?></td>
                    <td>Rp. <?php echo number_format($equity['total'], 2, ',', '.'); ?>,-</td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2" class="text-right font-weight-bold">Total Ekuitas</td>
                <td class="font-weight-bold">Rp. <?php echo number_format($total_equities, 2, ',', '.'); ?>,-</td>
            </tr>
        </tbody>
    </table>

    <h6><strong>Jumlah Kewajiban dan Ekuitas</strong></h6>
    <table>
        <thead>
            <tr>
                <th>Total Kewajiban</th>
                <th>Total Ekuitas</th>
                <th>Total Kewajiban dan Ekuitas</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Rp. <?php echo number_format($total_liabilities, 2, ',', '.'); ?>,-</td>
                <td>Rp. <?php echo number_format($total_equities, 2, ',', '.'); ?>,-</td>
                <td>Rp. <?php echo number_format($total_liabilities + $total_equities, 2, ',', '.'); ?>,-</td>
            </tr>
        </tbody>
    </table>

    <h6><strong>Total Aset</strong></h6>
    <table>
        <thead>
            <tr>
                <th>Total Aset</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Rp. <?php echo number_format($total_assets, 2, ',', '.'); ?>,-</td>
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
$outputFilename = "laporan_neraca_{$start_date}_sampai_{$end_date}.pdf";

// Write the HTML content to the PDF
$mpdf->WriteHTML($htmlContent);

// Output the PDF to the browser
$mpdf->Output($outputFilename, 'D'); // Change 'D' to 'I' to display in the browser
?>
