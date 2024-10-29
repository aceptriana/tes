<?php
//edit-po.php
include '../config/session.php';
include '../config/db.php';
include 'header.php';
include 'navbar.php';

// Check if kode_prapesan is provided and is not empty
if (!isset($_GET['kode_prapesan']) || empty($_GET['kode_prapesan'])) {
    header("Location: data-po.php?status=error&message=" . urlencode("Kode prapesan tidak valid"));
    exit;
}

$kode_prapesan = $_GET['kode_prapesan'];

// Fetch PO data
$sql = "SELECT * FROM prapesan WHERE kode_prapesan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $kode_prapesan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: data-po.php?status=error&message=" . urlencode("Data prapesan tidak ditemukan"));
    exit;
}

$po = $result->fetch_assoc();
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Prapesan (PO)</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Edit Prapesan</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="process_edit_po.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="kode_prapesan" value="<?php echo htmlspecialchars($po['kode_prapesan']); ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode_pemasok" class="form-label">Kode Pemasok</label>
                                <input type="text" class="form-control" id="kode_pemasok" name="kode_pemasok" value="<?php echo htmlspecialchars($po['kode_pemasok']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_pesan" class="form-label">Tanggal Pesan</label>
                                <input type="date" class="form-control" id="tanggal_pesan" name="tanggal_pesan" value="<?php echo htmlspecialchars($po['tanggal_pesan']); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_dikirim" class="form-label">Tanggal Dikirim</label>
                                <input type="date" class="form-control" id="tanggal_dikirim" name="tanggal_dikirim" value="<?php echo htmlspecialchars($po['tanggal_dikirim']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($po['nama']); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <input type="text" class="form-control" id="jenis" name="jenis" value="<?php echo htmlspecialchars($po['jenis']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="warna" class="form-label">Warna</label>
                                <input type="text" class="form-control" id="warna" name="warna" value="<?php echo htmlspecialchars($po['warna']); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?php echo htmlspecialchars($po['jumlah']); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="panjang" class="form-label">Panjang</label>
                                <input type="number" step="0.01" class="form-control" id="panjang" name="panjang" value="<?php echo htmlspecialchars($po['panjang']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="roll" class="form-label">Roll</label>
                                <input type="number" step="0.01" class="form-control" id="roll" name="roll" value="<?php echo htmlspecialchars($po['roll']); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi_barang" class="form-label">Deskripsi Barang</label>
                            <textarea class="form-control" id="deskripsi_barang" name="deskripsi_barang" rows="3"><?php echo htmlspecialchars($po['deskripsi_barang']); ?></textarea>
                        </div>

                        <!-- Gambar -->
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar Barang</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                            <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($po['gambar_barang']); ?>">
                            <?php if (!empty($po['gambar_barang'])): ?>
                                <p>Gambar saat ini:</p>
                                <img src="../uploads/<?php echo htmlspecialchars($po['gambar_barang']); ?>" alt="Gambar Barang" style="max-width: 150px;">
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Prapesan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#kode_pemasok').change(function() {
        var kode_pemasok = $(this).val();
        if (kode_pemasok) {
            $.ajax({
                url: 'get_rak_data.php',
                type: 'POST',
                data: { kode_penyimpanan: kode_pemasok },
                dataType: 'json',
                success: function(data) {
                    if (data.error) {
                        console.log('Error: ' + data.error);
                    } else {
                        $('#nama').val(data.nama_barang || '');
                        $('#jenis').val(data.jenis || '');
                        $('#warna').val(data.warna || '');
                        $('#jumlah').val(data.jumlah || '');
                        $('#panjang').val(data.panjang || '');
                        $('#roll').val(data.roll || '');
                        $('#deskripsi_barang').val(data.keterangan_barang || '');
                        
                        if (data.gambar_barang) {
                            $('img').attr('src', '../uploads/' + data.gambar_barang).show();
                        } else {
                            $('img').hide();
                        }
                    }
                },
                error: function() {
                    console.log('Terjadi kesalahan saat mengambil data.');
                }
            });
        }
    });
});
</script>

<?php 
$stmt->close();
$conn->close();
include 'footer.php'; 
?>