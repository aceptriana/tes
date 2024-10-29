<?php
//process_edit_surat.php
include '../config/session.php';
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get user input
    $kode_surat_jalan = trim($_POST['kode_surat_jalan']);
    $nama = trim($_POST['nama']);
    $jenis = trim($_POST['jenis']);
    $warna = trim($_POST['warna']);
    $jumlah = (int)$_POST['jumlah'];
    $panjang = !empty($_POST['panjang']) ? (float)$_POST['panjang'] : null;
    $roll = !empty($_POST['roll']) ? (int)$_POST['roll'] : null;
    $keterangan_barang = trim($_POST['keterangan_barang']);
    $dipesan_oleh = trim($_POST['dipesan_oleh']);
    $dikirim_oleh = trim($_POST['dikirim_oleh']);
    $tanggal_pengiriman = trim($_POST['tanggal_pengiriman']);
    $waktu_pengiriman = trim($_POST['waktu_pengiriman']);
    $lokasi_pengiriman = trim($_POST['lokasi_pengiriman']);
    $nama_penerima = trim($_POST['nama_penerima']);

    // Validate required fields
    $required_fields = [
        'kode_surat_jalan' => $kode_surat_jalan,
        'nama' => $nama,
        'jenis' => $jenis,
        'jumlah' => $jumlah,
        'dipesan_oleh' => $dipesan_oleh,
        'dikirim_oleh' => $dikirim_oleh,
        'tanggal_pengiriman' => $tanggal_pengiriman,
        'waktu_pengiriman' => $waktu_pengiriman,
        'lokasi_pengiriman' => $lokasi_pengiriman,
        'nama_penerima' => $nama_penerima
    ];

    foreach ($required_fields as $field => $value) {
        if (empty($value)) {
            header("Location: edit-surat-jalan.php?kode_surat_jalan=$kode_surat_jalan&status=error&message=Field $field tidak boleh kosong");
            exit;
        }
    }

    try {
        // First check if the record exists
        $check_sql = "SELECT kode_surat_jalan FROM surat_jalan WHERE kode_surat_jalan = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $kode_surat_jalan);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            throw new Exception("Data surat jalan tidak ditemukan.");
        }
        $check_stmt->close();

        // SQL query to update the record
        $sql = "UPDATE surat_jalan SET 
                nama = ?, 
                jenis = ?, 
                warna = ?, 
                jumlah = ?, 
                panjang = ?, 
                roll = ?, 
                keterangan_barang = ?,
                dipesan_oleh = ?,
                dikirim_oleh = ?,
                tanggal_pengiriman = ?,
                waktu_pengiriman = ?,
                lokasi_pengiriman = ?,
                nama_penerima = ?
                WHERE kode_surat_jalan = ?";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Bind parameters
        if (!$stmt->bind_param(
            "sssiddssssssss",
            $nama,
            $jenis,
            $warna,
            $jumlah,
            $panjang,
            $roll,
            $keterangan_barang,
            $dipesan_oleh,
            $dikirim_oleh,
            $tanggal_pengiriman,
            $waktu_pengiriman,
            $lokasi_pengiriman,
            $nama_penerima,
            $kode_surat_jalan
        )) {
            throw new Exception("Binding parameters failed: " . $stmt->error);
        }

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Don't check affected rows since no changes might still be valid
        header("Location: data-surat.php?status=success&message=Surat jalan berhasil diupdate");
        exit;

    } catch (Exception $e) {
        error_log("Error updating surat jalan: " . $e->getMessage());
        header("Location: edit-surat-jalan.php?kode_surat_jalan=$kode_surat_jalan&status=error&message=Gagal mengupdate surat jalan: " . urlencode($e->getMessage()));
        exit;
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($check_stmt)) {
            $check_stmt->close();
        }
        $conn->close();
    }
} else {
    header("Location: data-surat.php");
    exit;
}