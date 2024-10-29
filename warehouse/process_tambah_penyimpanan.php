<?php
//process_tambah_penyimpanan
include '../config/db.php';
include '../config/session.php';

// Start transaction
$conn->begin_transaction();

try {
    if (empty($_POST['kode_penerimaan']) || empty($_POST['color_detail_id'])) {
        throw new Exception("Data tidak lengkap");
    }

    $kode_penerimaan = $_POST['kode_penerimaan'];
    $color_detail_id = $_POST['color_detail_id'];
    $roll_po = !empty($_POST['roll_po']) ? floatval($_POST['roll_po']) : 0;
    
    // Get nota and color detail information
    $stmt = $conn->prepare("
        SELECT np.*, cd.* 
        FROM nota_penerimaan np
        JOIN color_details cd ON np.kode_penerimaan = cd.kode_penerimaan
        WHERE np.kode_penerimaan = ? AND cd.id = ?
    ");
    $stmt->bind_param("ii", $kode_penerimaan, $color_detail_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    if (!$data) {
        throw new Exception("Data tidak ditemukan");
    }

    // Calculate values
    $total_roll = floatval($data['roll']);
    $roll_gudang = $total_roll - $roll_po;
    
    if ($roll_po > $total_roll) {
        throw new Exception("Jumlah roll PO melebihi total roll tersedia");
    }

    $message = "";
    
    // Insert into po_customer if roll_po > 0
    if ($roll_po > 0) {
        $stmt = $conn->prepare("
            INSERT INTO po_customer (
                kode_penerimaan, color_detail_id, nama_barang, nama_motif, 
                warna_motif, gsm, width_cm, roll, roll_length, small_roll,
                keterangan_barang, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'barang po', 'tersimpan')
        ");
        
        $stmt->bind_param("iisssiiddd",
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
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan data PO");
        }
        
        $message .= "Data berhasil disimpan ke PO Customer. ";
    }

    // Insert remaining into penyimpanan_barang
    if ($roll_gudang > 0) {
        $stmt = $conn->prepare("
            INSERT INTO penyimpanan_barang (
                kode_penerimaan, color_detail_id, nama_barang, nama_motif, 
                warna_motif, gsm, width_cm, roll, roll_length, small_roll,
                nomor_line, posisi_line, keterangan_barang, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'barang gudang', 'tersimpan')
        ");
        
        $nomor_line = $_POST['nomor_line'] ?? null;
        $posisi_line = $_POST['posisi_line'] ?? null;
        
        $stmt->bind_param("iisssiidddss",
            $kode_penerimaan,
            $color_detail_id,
            $data['nama_barang'],
            $data['nama_motif'],
            $data['warna_motif'],
            $data['gsm'],
            $data['width_cm'],
            $roll_gudang,
            $data['roll_length'],
            $data['small_roll'],
            $nomor_line,
            $posisi_line
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan data gudang");
        }
        
        $message .= "Data berhasil disimpan ke Gudang.";
    }

    $conn->commit();
    header("Location: tambah-penyimpanan.php?status=success&message=" . urlencode($message));
    exit;

} catch (Exception $e) {
    $conn->rollback();
    header("Location: tambah-penyimpanan.php?status=error&message=" . urlencode($e->getMessage()));
    exit;
}
?>