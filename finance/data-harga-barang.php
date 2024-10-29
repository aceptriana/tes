<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Handle price update
if (isset($_POST['update_price'])) {
    $kode_penerimaan = $_POST['kode_penerimaan'];
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

// Fetch products
$sql = "SELECT kode_penerimaan, nama_barang, keterangan_barang, warna_motif, roll, harga_modal, harga_jual FROM penyimpanan_barang";
$result = $conn->query($sql);

if (!$result) {
    die('Error: ' . $conn->error); // Tangani kesalahan query
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Harga Barang</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Perbarui Harga Barang</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Jenis</th>
                                    <th>Warna</th>
                                    <th>Stok</th>
                                    <th>Harga Modal</th>
                                    <th>Harga Jual</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "
                                    <tr>
                                        <td>{$no}</td>
                                        <td>{$row['nama_barang']}</td>
                                        <td>{$row['keterangan_barang']}</td>
                                        <td>{$row['warna_motif']}</td>
                                        <td>{$row['roll']}</td>
                                        <td>Rp " . number_format($row['harga_modal'], 0, ',', '.') . "</td>
                                        <td>Rp " . number_format($row['harga_jual'], 0, ',', '.') . "</td>
                                        <td>
                                            <a href='edit-harga-barang.php?kode_penerimaan={$row['kode_penerimaan']}' class='btn btn-primary btn-sm' title='Edit'>
                                                <i class='feather feather-edit-3'></i> Edit
                                            </a>
                                        </td>
                                    </tr>";
                                    $no++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
