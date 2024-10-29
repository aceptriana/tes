<?php
include '../config/session.php'; // Check login session
include '../config/db.php'; // Include your database connection file
include 'header.php';
include 'navbar.php';

// Check if kode_surat_jalan is provided and is not empty
if (!isset($_GET['kode_surat_jalan']) || empty($_GET['kode_surat_jalan'])) {
    header("Location: data-surat.php?status=error&message=Kode surat jalan tidak valid");
    exit;
}

$kode_surat_jalan = $_GET['kode_surat_jalan'];

// Fetch surat jalan data
$sql = "SELECT * FROM surat_jalan WHERE kode_surat_jalan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $kode_surat_jalan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: data-surat.php?status=error&message=Data surat jalan tidak ditemukan");
    exit;
}
$surat = $result->fetch_assoc();
$stmt->close();
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Surat Jalan</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="data-surat.php">Data Surat Jalan</a></li>
                    <li class="breadcrumb-item active">Edit Surat Jalan</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="process_edit_surat.php" method="POST">
                        <input type="hidden" name="kode_surat_jalan" value="<?php echo htmlspecialchars($surat['kode_surat_jalan']); ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($surat['nama']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <input type="text" class="form-control" id="jenis" name="jenis" value="<?php echo htmlspecialchars($surat['jenis']); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="warna" class="form-label">Warna</label>
                                <input type="text" class="form-control" id="warna" name="warna" value="<?php echo htmlspecialchars($surat['warna']); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?php echo htmlspecialchars($surat['jumlah']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="panjang" class="form-label">Panjang</label>
                                <input type="number" class="form-control" id="panjang" name="panjang" value="<?php echo htmlspecialchars($surat['panjang']); ?>" step="0.01">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="roll" class="form-label">Roll</label>
                                <input type="number" class="form-control" id="roll" name="roll" value="<?php echo htmlspecialchars($surat['roll']); ?>" step="1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="dipesan_oleh" class="form-label">Dipesan Oleh</label>
                                <input type="text" class="form-control" id="dipesan_oleh" name="dipesan_oleh" value="<?php echo htmlspecialchars($surat['dipesan_oleh']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="dikirim_oleh" class="form-label">Dikirim Oleh</label>
                                <input type="text" class="form-control" id="dikirim_oleh" name="dikirim_oleh" value="<?php echo htmlspecialchars($surat['dikirim_oleh']); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_pengiriman" class="form-label">Tanggal Pengiriman</label>
                                <input type="date" class="form-control" id="tanggal_pengiriman" name="tanggal_pengiriman" value="<?php echo htmlspecialchars($surat['tanggal_pengiriman']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="waktu_pengiriman" class="form-label">Waktu Pengiriman</label>
                                <input type="time" class="form-control" id="waktu_pengiriman" name="waktu_pengiriman" value="<?php echo htmlspecialchars($surat['waktu_pengiriman']); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lokasi_pengiriman" class="form-label">Lokasi Pengiriman</label>
                                <input type="text" class="form-control" id="lokasi_pengiriman" name="lokasi_pengiriman" value="<?php echo htmlspecialchars($surat['lokasi_pengiriman']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_penerima" class="form-label">Nama Penerima</label>
                                <input type="text" class="form-control" id="nama_penerima" name="nama_penerima" value="<?php echo htmlspecialchars($surat['nama_penerima']); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan_barang" class="form-label">Keterangan Barang</label>
                            <textarea class="form-control" id="keterangan_barang" name="keterangan_barang" rows="3"><?php echo htmlspecialchars($surat['keterangan_barang']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Surat Jalan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php 
$conn->close();
include 'footer.php'; 
?>