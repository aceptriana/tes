<?php
//edit-nota.php
include '../config/session.php'; // Check login session
include '../config/db.php'; // Include your database connection file
include 'header.php';
include 'navbar.php';

// Check if rak ID is provided and is not empty
if (!isset($_GET['kode_nota']) || empty($_GET['kode_nota'])) {
    header("Location: data-nota.php?status=error&message=Kode nota tidak valid");
    exit;
}

$kode_nota = $_GET['kode_nota'];

// Fetch nota data
$sql = "SELECT * FROM nota_penerimaan_barang WHERE kode_nota = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $kode_nota);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: data-nota.php?status=error&message=Data nota tidak ditemukan");
    exit;
}
$nota = $result->fetch_assoc();

// Fetch supplier data for dropdown
$supplier_query = "SELECT kode_pemasok, nama FROM pemasok";
$supplier_result = $conn->query($supplier_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Nota Penerimaan Barang</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            line-height: 38px;
        }
    </style>
</head>
<body>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Nota Penerimaan</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Edit Nota Penerimaan</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="process_edit_nota.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="kode_nota" value="<?php echo $nota['kode_nota']; ?>">
                        <div class="row">

                        <div class="col-md-6 mb-3">
                            <label for="kode_pemasok" class="form-label">Kode Pemasok</label>
                            <select class="form-control select2" id="kode_pemasok" name="kode_pemasok" required>
                                <option value="">Pilih Pemasok</option>
                                <?php 
                                // Reset pointer hasil query ke awal
                                $supplier_result->data_seek(0);
                                while($row = $supplier_result->fetch_assoc()) { 
                                    $selected = ($row['kode_pemasok'] == $nota['kode_pemasok']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $row['kode_pemasok']; ?>" <?php echo $selected; ?>>
                                        <?php echo $row['kode_pemasok'] . ' - ' . $row['nama']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                            <div class="col-md-6 mb-3">
                                <label for="jenis_barang_dikirim" class="form-label">Jenis Barang Diterima</label>
                                <input type="text" class="form-control" id="jenis_barang_dikirim" name="jenis_barang_dikirim" value="<?php echo $nota['jenis_barang_dikirim']; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_diterima" class="form-label">Tanggal Diterima</label>
                                <input type="date" class="form-control" id="tanggal_diterima" name="tanggal_diterima" value="<?php echo $nota['tanggal_diterima']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dikirim_oleh" class="form-label">Dikirim Oleh</label>
                                <input type="text" class="form-control" id="dikirim_oleh" name="dikirim_oleh" value="<?php echo $nota['dikirim_oleh']; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="diterima_oleh" class="form-label">Diterima Oleh</label>
                                <input type="text" class="form-control" id="diterima_oleh" name="diterima_oleh" value="<?php echo $nota['diterima_oleh']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_barang" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?php echo $nota['nama_barang']; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <input type="text" class="form-control" id="jenis" name="jenis" value="<?php echo $nota['jenis']; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="warna" class="form-label">Warna</label>
                                <input type="text" class="form-control" id="warna" name="warna" value="<?php echo $nota['warna']; ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?php echo $nota['jumlah']; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="panjang" class="form-label">Panjang</label>
                                <input type="number" class="form-control" id="panjang" name="panjang" value="<?php echo $nota['panjang']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="roll" class="form-label">Roll</label>
                                <input type="number" class="form-control" id="roll" name="roll" value="<?php echo $nota['roll']; ?>" >
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi_barang" class="form-label">Deskripsi Barang</label>
                            <textarea class="form-control" id="deskripsi_barang" name="deskripsi_barang" rows="3"><?php echo $nota['deskripsi_barang']; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar Barang</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                            <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($nota['gambar_barang']); ?>">
                            <?php if (!empty($nota['gambar_barang'])) : ?>
                                <p>Gambar saat ini:</p>
                                <img src="../uploads/<?php echo htmlspecialchars($nota['gambar_barang']); ?>" alt="Gambar Barang" style="max-width: 150px;">
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Nota</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php 
$stmt->close();
$conn->close();
include 'footer.php'; 
?>
