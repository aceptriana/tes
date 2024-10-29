<?php
require_once '../vendor/autoload.php'; // Ensure the path is correct

// Start output buffering
ob_start();

// Connect to the database
include '../config/db.php'; // Include your database connection
include '../config/session.php'; // Include session management

// Fetch filter parameters
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$kode_coa = $_GET['kode_coa'] ?? '';

// Initialize totals
$total_pemasukan = 0; 
$total_pengeluaran = 0;

// Fetch data if the filter is applied
if ($start_date && $end_date) {
    // Build the SQL query based on the filters
    $sql = "SELECT t.*, c.nama_akun 
            FROM transactions t
            JOIN coa c ON t.kode_coa = c.kode_akun
            WHERE t.tanggal BETWEEN ? AND ?";
    $params = [$start_date, $end_date];

    if ($kode_coa !== '') {
        $sql .= " AND t.kode_coa = ?";
        $params[] = $kode_coa;
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = [];
}

// Create the HTML content for the PDF
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jurnal</title>
    <style>
        /* Add your styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 10px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        p {
            text-align: center;
            margin-bottom: 20px;
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
        .footer p {
            margin: 5px 0; /* Space between footer lines */
        }
    </style>
</head>
<body>
    <h1>Laporan Jurnal</h1>
    <h2>Sahara Textile</h2>
    <p>Periode: <?php echo htmlspecialchars($start_date); ?> s/d <?php echo htmlspecialchars($end_date); ?></p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Akun</th> <!-- Mengganti Kode COA menjadi Nama Akun -->
                <th>Keterangan</th>
                <th>Jenis</th>
                <th>Nominal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['tanggal']}</td>
                        <td>{$row['nama_akun']}</td> <!-- Mengganti kode_coa dengan nama_akun -->
                        <td>{$row['keterangan']}</td>
                        <td>{$row['jenis']}</td>
                        <td>Rp. " . number_format($row['nominal'], 2, ',', '.') . "</td>
                    </tr>";

                    // Hitung total pemasukan dan pengeluaran
                    if ($row['jenis'] === 'Pemasukan') {
                        $total_pemasukan += $row['nominal'];
                    } elseif ($row['jenis'] === 'Pengeluaran') {
                        $total_pengeluaran += $row['nominal'];
                    }
                    
                    $no++;
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>Tidak ada data transaksi</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="footer">
            <p>Total Pemasukan: Rp. <?php echo number_format($total_pemasukan, 2, ',', '.'); ?>,-</p>
            <p>Total Pengeluaran: Rp. <?php echo number_format($total_pengeluaran, 2, ',', '.'); ?>,-</p>
            <p>Saldo Akhir: Rp. <?php echo number_format(($total_pemasukan - $total_pengeluaran), 2, ',', '.'); ?>,-</p>
        </div>
    <?php endif; ?>
</body>
</html>

<?php
// Capture the output
$htmlContent = ob_get_clean();

// Create an instance of mPDF
$mpdf = new \Mpdf\Mpdf();

// Generate the output filename
$outputFilename = "laporan_jurnal_{$start_date}_sampai_{$end_date}.pdf";

// Write the HTML content to the PDF
$mpdf->WriteHTML($htmlContent);

// Output the PDF to the browser
$mpdf->Output($outputFilename, 'D'); // Change 'D' to 'I' to display in the browser
?>
