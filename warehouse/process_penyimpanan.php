<?php

// process_penyimpanan.php
include '../config/db.php';
include '../config/session.php';

// Start transaction
$conn->begin_transaction();

try {
    // Validasi input dasar
    if (empty($_POST['kode_penerimaan']) || empty($_POST['color_detail_id']) || 
        empty($_POST['nomor_line']) || empty($_POST['posisi_line'])) {
        throw new Exception("Data penyimpanan tidak lengkap");
    }

    $kode_penerimaan = $_POST['kode_penerimaan'];
    $color_detail_id = $_POST['color_detail_id'];
    $nomor_line = $_POST['nomor_line'];
    $posisi_line = $_POST['posisi_line'];
    $roll_storage = !empty($_POST['roll_storage']) ? floatval($_POST['roll_storage']) : 0;

    // Validasi roll_storage harus lebih dari 0
    if ($roll_storage <= 0) {
        throw new Exception("Jumlah roll penyimpanan harus lebih dari 0");
    }

    // Get nota and color detail information with remaining roll check
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
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $kode_penerimaan, $color_detail_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        throw new Exception("Data tidak ditemukan");
    }

    // Validasi ketersediaan roll
    $available_roll = $data['roll'] - $data['used_roll'];
    if ($roll_storage > $available_roll) {
        throw new Exception("Jumlah roll penyimpanan melebihi total roll tersedia ($available_roll)");
    }

    // Insert into penyimpanan_barang
    $sql_gudang = "INSERT INTO penyimpanan_barang (
        kode_penerimaan, color_detail_id, nama_barang, nama_motif, 
        warna_motif, gsm, width_cm, roll, roll_length, small_roll,
        nomor_line, posisi_line, keterangan_barang, status,
        lokasi_gudang
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'barang gudang', 'tersedia', ?)";
    
    $lokasi_gudang = $nomor_line . '-' . $posisi_line;
    
    $stmt_gudang = $conn->prepare($sql_gudang);
    $stmt_gudang->bind_param("iisssiiiddssss",
        $kode_penerimaan,
        $color_detail_id,
        $data['nama_barang'],
        $data['nama_motif'],
        $data['warna_motif'],
        $data['gsm'],
        $data['width_cm'],
        $roll_storage,
        $data['roll_length'],
        $data['small_roll'],
        $nomor_line,
        $posisi_line,
        $lokasi_gudang
    );
    
    $stmt_gudang->execute();
    $stmt_gudang->close();
    $stmt->close();
    
    $conn->commit();
    header("Location: tambah-penyimpanan.php?status=success&message=" . urlencode("Data berhasil disimpan ke Gudang"));
    exit;

} catch (Exception $e) {
    $conn->rollback();
    header("Location: tambah-penyimpanan.php?status=error&message=" . urlencode($e->getMessage()));
    exit;
}
?>