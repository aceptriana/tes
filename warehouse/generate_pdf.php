<?php
require_once('../config/session.php');
require_once('../config/db.php');
require_once('tcpdf/tcpdf.php');

// Function to get barang details
function getBarangDetails($kode_penerimaan) {
    global $conn;
    
    $sql = "SELECT 
        cd.id as color_detail_id,
        pb.nama_barang,
        pb.nama_motif,
        pb.gsm,
        pb.width_cm as width,
        pb.roll,
        pb.roll_length,
        pb.small_roll,
        pb.total_length,
        pb.total_length_with_small_roll
    FROM penyimpanan_barang pb
    JOIN color_details cd ON pb.color_detail_id = cd.id
    WHERE pb.kode_penerimaan = ?
    ORDER BY cd.id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $kode_penerimaan);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $barang_details = [];
    while ($row = $result->fetch_assoc()) {
        $barang_details[] = $row;
    }
    
    return $barang_details;
}

// Check kode_penerimaan parameter
if (!isset($_GET['kode_penerimaan']) || empty($_GET['kode_penerimaan'])) {
    die("Kode Penerimaan tidak ditemukan.");
}

$kode_penerimaan = $_GET['kode_penerimaan'];
$barang_details = getBarangDetails($kode_penerimaan);

if (empty($barang_details)) {
    die("Data tidak ditemukan.");
}

// Create new PDF document
class MYPDF extends TCPDF {
    public function Header() {
        // Empty header
    }
    
    public function Footer() {
        // Empty footer
    }
}

$pdf = new MYPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Sahara Tekstil');
$pdf->SetAuthor('Sahara Tekstil');
$pdf->SetTitle('Surat Jalan - ' . $kode_penerimaan);

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, 15);

// Add page
$pdf->AddPage();

// Prepare HTML content
ob_start();
include 'surat_jalan_template.php';
$html = ob_get_clean();

// Output HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Clean output buffer
ob_end_clean();

// Generate filename
$date = date('Ymd');
$filename = 'Surat_Jalan_' . $kode_penerimaan . '_' . $date . '.pdf';

// Output PDF
$pdf->Output($filename, 'D');
exit();
?>