<?php
include '../config/db.php';
include '../config/session.php';

// Check if kode parameter exists
if (!isset($_POST['kode'])) {
    header('Location: data-nota.php?status=error&message=' . urlencode('Kode nota tidak ditemukan'));
    exit;
}

$kode_penerimaan = $_POST['kode'];

try {
    // Start transaction
    $conn->begin_transaction();

    // First, get image paths before deleting
    $image_query = $conn->prepare("SELECT n.design_image, cd.photo_path 
                                  FROM nota_penerimaan n 
                                  LEFT JOIN color_details cd ON n.kode_penerimaan = cd.kode_penerimaan 
                                  WHERE n.kode_penerimaan = ?");
    $image_query->bind_param("i", $kode_penerimaan);
    $image_query->execute();
    $image_result = $image_query->get_result();
    $image_paths = $image_result->fetch_all(MYSQLI_ASSOC);

    // Delete color_details records
    $delete_details = $conn->prepare("DELETE FROM color_details WHERE kode_penerimaan = ?");
    $delete_details->bind_param("i", $kode_penerimaan);
    $delete_details->execute();

    // Delete nota_penerimaan record
    $delete_nota = $conn->prepare("DELETE FROM nota_penerimaan WHERE kode_penerimaan = ?");
    $delete_nota->bind_param("i", $kode_penerimaan);
    $delete_nota->execute();

    // Check if the nota actually existed and was deleted
    if ($delete_nota->affected_rows === 0) {
        throw new Exception('Nota tidak ditemukan');
    }

    // Commit the transaction
    $conn->commit();

    // Delete image files
    foreach ($image_paths as $row) {
        if (!empty($row['design_image']) && file_exists('../' . $row['design_image'])) {
            unlink('../' . $row['design_image']);
        }
        if (!empty($row['photo_path']) && file_exists('../' . $row['photo_path'])) {
            unlink('../' . $row['photo_path']);
        }
    }

    header('Location: data-nota.php?status=success&message=' . urlencode('Data berhasil dihapus'));

} catch (Exception $e) {
    $conn->rollback();
    header('Location: data-nota.php?status=error&message=' . urlencode('Gagal menghapus data: ' . $e->getMessage()));
}

// Close prepared statements
if (isset($image_query)) $image_query->close();
if (isset($delete_details)) $delete_details->close();
if (isset($delete_nota)) $delete_nota->close();

$conn->close();
?>