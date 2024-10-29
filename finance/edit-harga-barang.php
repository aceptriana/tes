<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Cek apakah kode_penerimaan ada di query string
if (isset($_GET['kode_penerimaan'])) {
    $kode_penerimaan = $_GET['kode_penerimaan'];

    // Fetch data barang berdasarkan kode_penerimaan
    $sql = "SELECT * FROM penyimpanan_barang WHERE kode_penerimaan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $kode_penerimaan);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah ada data yang ditemukan
    if ($result->num_rows === 0) {
        echo '<script>alert("Barang tidak ditemukan."); window.location.href="data-harga-barang.php";</script>';
        exit;
    }

    $row = $result->fetch_assoc();
}

// Handle price update
if (isset($_POST['update_price'])) {
    $new_price_modal = $_POST['harga_modal'];
    $new_price_jual = $_POST['harga_jual'];

    $sql_update = "UPDATE penyimpanan_barang SET ";
    $params = [];
    $types = "";

    if (!empty($new_price_modal)) {
        $sql_update .= "harga_modal = ?";
        $params[] = $new_price_modal;
        $types .= 'd'; // Decimal
    }

    if (!empty($new_price_jual)) {
        if (!empty($params)) {
            $sql_update .= ", ";
        }
        $sql_update .= "harga_jual = ?";
        $params[] = $new_price_jual;
        $types .= 'd'; // Decimal
    }

    $sql_update .= " WHERE kode_penerimaan = ?";
    $params[] = $kode_penerimaan;
    $types .= 'i'; // Integer

    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param($types, ...$params);

    if ($stmt_update->execute()) {
        echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: "Harga barang berhasil diperbarui!",
                    confirmButtonText: "OK"
                }).then(() => {
                    window.location.href = "data-harga-barang.php";
                });
              </script>';
    } else {
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Kesalahan",
                    text: "Terjadi kesalahan saat memperbarui harga barang.",
                    confirmButtonText: "OK"
                });
              </script>';
    }
    $stmt_update->close();
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Harga Barang</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Edit Harga Barang</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="edit-harga-barang.php?kode_penerimaan=<?php echo $kode_penerimaan; ?>" method="POST">
                        <div class="form-group mb-3">
                            <label>Nama Barang:</label>
                            <input type="text" class="form-control" value="<?php echo $row['nama_barang']; ?>" disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label>Harga Modal:</label>
                            <input type="number" step="0.01" class="form-control" name="harga_modal" placeholder="Harga Modal" value="<?php echo number_format($row['harga_modal'], 0, '.', ''); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label>Harga Jual:</label>
                            <input type="number" step="0.01" class="form-control" name="harga_jual" placeholder="Harga Jual" value="<?php echo number_format($row['harga_jual'], 0, '.', ''); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary" name="update_price">Update Harga</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
