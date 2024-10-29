<?php
// process_po_customer.php
include '../config/db.php';
include '../config/session.php';

// Set error handling untuk menangkap semua error
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Mulai transaksi
$conn->begin_transaction();

try {
    // Validasi input dasar
    if (empty($_POST['kode_penerimaan'])) {
        throw new Exception("Kode penerimaan tidak boleh kosong");
    }
    if (empty($_POST['color_detail_id'])) {
        throw new Exception("ID detail warna tidak boleh kosong");
    }
    if (!isset($_POST['roll_po'])) {
        throw new Exception("Jumlah roll PO harus diisi");
    }

    // Bersihkan dan konversi input
    $kode_penerimaan = trim($_POST['kode_penerimaan']);
    $color_detail_id = (int)$_POST['color_detail_id'];
    $roll_po = floatval($_POST['roll_po']);

    // Validasi nilai roll_po
    if ($roll_po <= 0) {
        throw new Exception("Jumlah roll PO harus lebih dari 0");
    }

    // Query yang sudah diperbaiki - menggunakan nama kolom yang benar (id bukan color_detail_id)
    $sql = "SELECT 
        np.*, cd.*,
        COALESCE((
            SELECT SUM(roll) 
            FROM (
                SELECT roll FROM penyimpanan_barang WHERE color_detail_id = cd.id
                UNION ALL
                SELECT roll FROM po_customer WHERE color_detail_id = cd.id
            ) combined
        ), 0) as used_roll
        FROM nota_penerimaan np
        JOIN color_details cd ON np.kode_penerimaan = cd.kode_penerimaan
        WHERE np.kode_penerimaan = ? AND cd.id = ?";
    
    // Prepare statement untuk query pertama
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error dalam persiapan query: " . $conn->error);
    }

    // Bind parameter dan eksekusi
    if (!$stmt->bind_param("si", $kode_penerimaan, $color_detail_id)) {
        throw new Exception("Error dalam binding parameter: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error dalam eksekusi query: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        throw new Exception("Data tidak ditemukan untuk kode penerimaan dan color detail yang diberikan");
    }

    // Validasi ketersediaan roll
    $available_roll = $data['roll'] - $data['used_roll'];
    if ($roll_po > $available_roll) {
        throw new Exception("Jumlah roll PO ($roll_po) melebihi total roll tersedia ($available_roll)");
    }

    // Query untuk insert ke po_customer
    $sql_po = "INSERT INTO po_customer (
        kode_penerimaan, 
        color_detail_id, 
        nama_barang, 
        nama_motif, 
        warna_motif, 
        gsm, 
        width_cm, 
        roll, 
        roll_length, 
        small_roll,
        keterangan_barang, 
        status,
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'barang po', 'tersedia', NOW())";
    
    // Prepare statement untuk insert
    $stmt_po = $conn->prepare($sql_po);
    if ($stmt_po === false) {
        throw new Exception("Error dalam persiapan query insert: " . $conn->error);
    }

    // Bind parameter untuk insert
    if (!$stmt_po->bind_param("sississddds",
        $kode_penerimaan,
        $color_detail_id,
        $data['nama_barang'],
        $data['nama_motif'],
        $data['warna_motif'],
        $data['gsm'],
        $data['width_cm'],
        $roll_po,
        $data['roll_length'],
        $data['small_roll']
    )) {
        throw new Exception("Error dalam binding parameter insert: " . $stmt_po->error);
    }

    // Eksekusi insert
    if (!$stmt_po->execute()) {
        throw new Exception("Error dalam menyimpan PO: " . $stmt_po->error);
    }

    // Tutup statement
    $stmt_po->close();
    $stmt->close();
    
    // Commit transaksi jika semua berhasil
    $conn->commit();

    // Redirect dengan pesan sukses
    header("Location: tambah-penyimpanan.php?status=success&message=" . urlencode("Data berhasil disimpan ke PO Customer"));
    exit;

} catch (Exception $e) {
    // Rollback transaksi jika terjadi error
    $conn->rollback();

    // Log error (opsional)
    error_log("Error dalam process_po_customer.php: " . $e->getMessage());

    // Redirect dengan pesan error
    header("Location: tambah-penyimpanan.php?status=error&message=" . urlencode($e->getMessage()));
    exit;
} finally {
    // Kembalikan error handler default
    restore_error_handler();
    
    // Tutup koneksi database
    if (isset($stmt) && $stmt) {
        $stmt->close();
    }
    if (isset($stmt_po) && $stmt_po) {
        $stmt_po->close();
    }
    if (isset($conn) && $conn) {
        $conn->close();
    }
}
?>