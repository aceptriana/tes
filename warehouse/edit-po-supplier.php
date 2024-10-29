<?php
//edit-po-supplier.php
include '../config/session.php'; // Check login session
include '../config/db.php'; // Include your database connection file
include 'header.php';
include 'navbar.php';

// Check if preorder supplier ID is provided and is not empty
if (!isset($_GET['kode_preorder_supplier']) || empty($_GET['kode_preorder_supplier'])) {
    header("Location: data-supplier.php?status=error&message=Kode preorder tidak valid");
    exit;
}

$kode_preorder_supplier = $_GET['kode_preorder_supplier'];

// Fetch preorder supplier data
$sql = "SELECT * FROM preorder_supplier WHERE kode_preorder_supplier = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $kode_preorder_supplier);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: data-supplier.php?status=error&message=Data preorder tidak ditemukan");
    exit;
}

$po_supplier = $result->fetch_assoc();
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit PO Supplier</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Edit PO Supplier</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="process_edit_po_supplier.php" method="POST" enctype="multipart/form-data">
                        <!-- Kode Preorder Supplier -->
                        <div class="mb-3">
                            <label for="kode_preorder_supplier" class="form-label">Kode Preorder Supplier</label>
                            <input type="text" class="form-control" id="kode_preorder_supplier" name="kode_preorder_supplier" value="<?php echo htmlspecialchars($po_supplier['kode_preorder_supplier']); ?>" readonly>
                        </div>

                        <!-- Kode Penyimpanan -->
                        <div class="mb-3">
                            <label for="kode_penyimpanan" class="form-label">Kode Penyimpanan</label>
                            <input type="text" class="form-control" id="kode_penyimpanan" name="kode_penyimpanan" value="<?php echo htmlspecialchars($po_supplier['kode_penyimpanan']); ?>" readonly>
                        </div>

                        <!-- Tanggal Pesan and Tanggal Dikirim -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_pesan" class="form-label">Tanggal Pesan</label>
                                <input type="date" class="form-control" id="tanggal_pesan" name="tanggal_pesan" value="<?php echo htmlspecialchars($po_supplier['tanggal_pesan']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_dikirim" class="form-label">Tanggal Dikirim</label>
                                <input type="date" class="form-control" id="tanggal_dikirim" name="tanggal_dikirim" value="<?php echo htmlspecialchars($po_supplier['tanggal_dikirim']); ?>">
                            </div>
                        </div>

                        <!-- Nama, Jenis, and Warna -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($po_supplier['nama']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <input type="text" class="form-control" id="jenis" name="jenis" value="<?php echo htmlspecialchars($po_supplier['jenis']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="warna" class="form-label">Warna</label>
                                <input type="text" class="form-control" id="warna" name="warna" value="<?php echo htmlspecialchars($po_supplier['warna']); ?>">
                            </div>
                        </div>

                        <!-- Quantity Info -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?php echo htmlspecialchars($po_supplier['jumlah']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="panjang" class="form-label">Panjang</label>
                                <input type="number" class="form-control" id="panjang" name="panjang" value="<?php echo htmlspecialchars($po_supplier['panjang']); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="roll" class="form-label">Roll</label>
                                <input type="number" class="form-control" id="roll" name="roll" value="<?php echo htmlspecialchars($po_supplier['roll']); ?>">
                            </div>
                        </div>

                        <!-- Deskripsi Barang -->
                        <div class="mb-3">
                            <label for="deskripsi_barang" class="form-label">Deskripsi Barang</label>
                            <textarea class="form-control" id="deskripsi_barang" name="deskripsi_barang" rows="3"><?php echo trim(htmlspecialchars($po_supplier['deskripsi_barang'])); ?></textarea>
                        </div>

                        <!-- Gambar Barang -->
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar Barang</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                            <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($po_supplier['gambar_barang']); ?>">
                            <?php if (!empty($po_supplier['gambar_barang'])): ?>
                                <p>Gambar saat ini:</p>
                                <img src="../uploads/<?php echo htmlspecialchars($po_supplier['gambar_barang']); ?>" alt="Gambar Barang" style="max-width: 150px;">
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">Update PO Supplier</button>
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
