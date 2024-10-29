<?php
//edit-rak.php
include '../config/session.php'; // Check login session
include '../config/db.php'; // Include your database connection file
include 'header.php';
include 'navbar.php';

// Check if rak ID is provided and is not empty
if (!isset($_GET['kode_penyimpanan']) || empty($_GET['kode_penyimpanan'])) {
    header("Location: data-rak.php?status=error&message=Kode penyimpanan tidak valid");
    exit;
}

$kode_penyimpanan = $_GET['kode_penyimpanan'];

// Fetch rak data
$sql = "SELECT * FROM penyimpanan_barang WHERE kode_penyimpanan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $kode_penyimpanan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: data-rak.php?status=error&message=Data rak tidak ditemukan");
    exit;
}

$rak = $result->fetch_assoc();
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Rak</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Edit Rak</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="process_edit_rak.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="kode_penyimpanan" value="<?php echo htmlspecialchars($rak['kode_penyimpanan']); ?>">
                        
                        <!-- Kode Stok and Lokasi Penyimpanan -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode_stok" class="form-label">Kode Stok</label>
                                <select class="form-control select2" id="kode_stok" name="kode_stok" required>
                                    <option value="">Pilih Kode Stok</option>
                                    <!-- Use PHP to generate options dynamically -->
                                    <option value="<?php echo $rak['kode_stok']; ?>" selected><?php echo $rak['kode_stok']; ?></option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lokasi_penyimpanan" class="form-label">Lokasi Penyimpanan</label>
                                <input type="text" class="form-control" id="lokasi_penyimpanan" name="lokasi_penyimpanan" value="<?php echo htmlspecialchars($rak['lokasi_penyimpanan']); ?>" required>
                            </div>
                        </div>

                        <!-- Nomor Penyimpanan and Dates -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nomer_penyimpanan" class="form-label">Nomor Penyimpanan</label>
                                <input type="text" class="form-control" id="nomer_penyimpanan" name="nomer_penyimpanan" value="<?php echo htmlspecialchars($rak['nomer_penyimpanan']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="<?php echo htmlspecialchars($rak['tanggal_masuk']); ?>" required>
                            </div>
                        </div>

                        <!-- Tanggal Keluar and Barang Info -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_keluar" class="form-label">Tanggal Keluar</label>
                                <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar" value="<?php echo htmlspecialchars($rak['tanggal_keluar']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_barang" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?php echo htmlspecialchars($rak['nama_barang']); ?>" required>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <input type="text" class="form-control" id="jenis" name="jenis" value="<?php echo htmlspecialchars($rak['jenis']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="warna" class="form-label">Warna</label>
                                <input type="text" class="form-control" id="warna" name="warna" value="<?php echo htmlspecialchars($rak['warna']); ?>">
                            </div>
                        </div>

                        <!-- Quantity Info -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?php echo htmlspecialchars($rak['jumlah']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="panjang" class="form-label">Panjang</label>
                                <input type="number" class="form-control" id="panjang" name="panjang" value="<?php echo htmlspecialchars($rak['panjang']); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="roll" class="form-label">Roll</label>
                                <input type="number" class="form-control" id="roll" name="roll" value="<?php echo htmlspecialchars($rak['roll']); ?>">
                            </div>
                        </div>
                        <!-- Change this part in edit-rak.php -->
                        <div class="mb-3">
                            <label for="keterangan_barang" class="form-label">Keterangan Barang</label>
                            <textarea class="form-control" id="keterangan_barang" name="keterangan_barang" rows="3"><?php echo trim(htmlspecialchars($rak['keterangan_barang'])); ?></textarea>
                        </div>

                        <!-- Gambar -->
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar Barang</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                            <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($rak['gambar_barang']); ?>">
                            <?php if (!empty($rak['gambar_barang'])): ?>
                                <p>Gambar saat ini:</p>
                                <img src="../uploads/<?php echo htmlspecialchars($rak['gambar_barang']); ?>" alt="Gambar Barang" style="max-width: 150px;">
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Rak</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize select2 for kode_stok dropdown
        $('.select2').select2({
            placeholder: "Pilih Kode Stok",
            allowClear: true
        });

        // Fetch data when kode_stok changes
        $('#kode_stok').change(function() {
            var kodeStok = $(this).val();
            if (kodeStok) {
                $.ajax({
                    url: 'get_stok_data.php',
                    type: 'POST',
                    data: { kode_stok: kodeStok },
                    dataType: 'json',
                    success: function(data) {
                        // Update fields with data
                        $('#nama_barang').val(data.nama_barang || '');
                        $('#jenis').val(data.jenis || '');
                        $('#warna').val(data.warna || '');
                        $('#jumlah').val(data.jumlah || '');
                        $('#panjang').val(data.panjang || '');
                        $('#roll').val(data.roll || '');

                        if (data.gambar_barang) {
                            $('#preview_gambar').attr('src', '../uploads/' + data.gambar_barang).show();
                        } else {
                            $('#preview_gambar').attr('src', '').hide();
                        }
                    },
                    error: function() {
                        alert("Terjadi kesalahan saat mengambil data stok.");
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
