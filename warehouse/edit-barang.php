<?php
// edit-barang.php
include '../config/session.php';
include '../config/db.php';
include 'header.php';
include 'navbar.php';

if (!isset($_GET['kode_stok_barang']) || empty($_GET['kode_stok_barang'])) {
    header("Location: data-barang.php?status=error&message=Kode stok barang tidak valid");
    exit;
}

$kode_stok_barang = $_GET['kode_stok_barang'];

$sql = "SELECT * FROM stok_barang WHERE kode_stok_barang = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $kode_stok_barang);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: data-barang.php?status=error&message=Data barang tidak ditemukan");
    exit;
}
$barang = $result->fetch_assoc();

$nota_query = "SELECT kode_nota, jenis_barang_dikirim FROM nota_penerimaan_barang";
$nota_result = $conn->query($nota_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Stok Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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
                    <h5 class="m-b-10">Edit Barang</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Edit Stok Barang</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="process_edit_barang.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="kode_stok_barang" value="<?php echo htmlspecialchars($barang['kode_stok_barang']); ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode_nota" class="form-label">Kode Nota</label>
                                <select class="form-control select2" id="kode_nota" name="kode_nota" required>
                                    <option value="">Pilih Nota</option>
                                    <?php 
                                    $nota_result->data_seek(0);
                                    while($row = $nota_result->fetch_assoc()) { 
                                        $selected = ($row['kode_nota'] == $barang['kode_nota']) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $row['kode_nota']; ?>" <?php echo $selected; ?>>
                                            <?php echo $row['kode_nota'] . ' - ' . $row['jenis_barang_dikirim']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($barang['nama']); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <input type="text" class="form-control" id="jenis" name="jenis" value="<?php echo htmlspecialchars($barang['jenis']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="warna" class="form-label">Warna</label>
                                <input type="text" class="form-control" id="warna" name="warna" value="<?php echo htmlspecialchars($barang['warna']); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?php echo htmlspecialchars($barang['jumlah']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="panjang" class="form-label">Panjang</label>
                                <input type="number" step="0.01" class="form-control" id="panjang" name="panjang" value="<?php echo htmlspecialchars($barang['panjang']); ?>" >
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="roll" class="form-label">Roll</label>
                                <input type="number" class="form-control" id="roll" name="roll" value="<?php echo htmlspecialchars($barang['roll']); ?>" >
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_masuk_gudang" class="form-label">Tanggal Masuk Gudang</label>
                            <input type="date" class="form-control" id="tanggal_masuk_gudang" name="tanggal_masuk_gudang" value="<?php echo htmlspecialchars($barang['tanggal_masuk_gudang']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi_barang" class="form-label">Deskripsi Barang</label>
                            <textarea class="form-control" id="deskripsi_barang" name="deskripsi_barang" rows="3"><?php echo htmlspecialchars($barang['deskripsi_barang']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar Barang</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                            <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($barang['gambar_barang']); ?>">
                            <?php if (!empty($barang['gambar_barang'])) : ?>
                                <p>Gambar saat ini:</p>
                                <img src="../uploads/<?php echo htmlspecialchars($barang['gambar_barang']); ?>" alt="Gambar Barang" style="max-width: 150px;">
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Barang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();

    $('#kode_nota').change(function() {
        var kodeNota = $(this).val();
        if (kodeNota) {
            $.ajax({
                url: 'get_nota_data.php',
                type: 'POST',
                data: {kode_nota: kodeNota},
                dataType: 'json',
                success: function(data) {
                    $('#nama').val(data.nama || '');
                    $('#jenis').val(data.jenis || '');
                    $('#warna').val(data.warna || '');
                    $('#jumlah').val(data.jumlah || '');
                    $('#panjang').val(data.panjang || '');
                    $('#roll').val(data.roll || '');
                    $('#deskripsi_barang').val(data.deskripsi_barang || '');
                },
                error: function() {
                    alert("Terjadi kesalahan saat mengambil data nota.");
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