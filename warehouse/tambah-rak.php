<?php 
//tambah-rak.php
include '../config/session.php';
include '../config/db.php';
include 'header.php'; 
include 'navbar.php'; 

// Function to generate automatic kode penyimpanan
function generateKodePenyimpanan($conn) {
    $sql = "SELECT MAX(CAST(SUBSTRING(kode_penyimpanan, 3) AS UNSIGNED)) as max_kode FROM penyimpanan_barang";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $max_kode = $row['max_kode'];

    if ($max_kode === null) {
        return 'PB001';
    } else {
        $next_number = $max_kode + 1;
        return 'PB' . str_pad($next_number, 3, '0', STR_PAD_LEFT);
    }
}

$kode_penyimpanan_auto = generateKodePenyimpanan($conn);

// Fetch stok_barang data for dropdown
$stok_query = "SELECT kode_stok_barang, nama FROM stok_barang";
$stok_result = $conn->query($stok_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Rak Barang</title>
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
                    <h5 class="m-b-10">Tambah Rak Barang</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Tambah Rak</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="process_tambah_rak.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode_penyimpanan" class="form-label">Kode Penyimpanan</label>
                                <input type="text" class="form-control" id="kode_penyimpanan" name="kode_penyimpanan" value="<?php echo $kode_penyimpanan_auto; ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kode_stok_barang" class="form-label">Kode Stok</label>
                                <select class="form-control select2" id="kode_stok_barang" name="kode_stok_barang" required>
                                    <option value="">Pilih Kode Stok</option>
                                    <?php while($row = $stok_result->fetch_assoc()) { ?>
                                        <option value="<?php echo $row['kode_stok_barang']; ?>"><?php echo $row['kode_stok_barang'] . ' - ' . $row['nama']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lokasi_penyimpanan" class="form-label">Lokasi Penyimpanan</label>
                                <select class="form-control" id="lokasi_penyimpanan" name="lokasi_penyimpanan" required>
                                    <option value="">Pilih Lokasi</option>
                                    <option value="Rak A">Rak A</option>
                                    <option value="Rak B">Rak B</option>
                                    <option value="Rak C">Rak C</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nomer_penyimpanan" class="form-label">Nomor Penyimpanan</label>
                                <select class="form-control" id="nomer_penyimpanan" name="nomer_penyimpanan" required>
                                    <option value="">Pilih Nomor</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="<?php echo date('Y-m-d'); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_keluar" class="form-label">Tanggal Keluar</label>
                                <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar">
                                <small class="form-text text-muted">Biarkan kosong jika barang belum keluar gudang.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control" id="nama" name="nama" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <input type="text" class="form-control" id="jenis" name="jenis" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="warna" class="form-label">Warna</label>
                                <input type="text" class="form-control" id="warna" name="warna" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="panjang" class="form-label">Panjang</label>
                                <input type="number" class="form-control" id="panjang" name="panjang" step="0.01" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="roll" class="form-label">Roll</label>
                                <input type="number" class="form-control" id="roll" name="roll" step="1" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="deskripsi_barang" class="form-label">Keterangan Barang</label>
                                <input type="text" class="form-control" id="deskripsi_barang" name="deskripsi_barang" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="gambar_barang" class="form-label">Gambar Barang</label>
                                <input type="file" class="form-control-file" id="gambar_barang" name="gambar_barang" accept="image/*">
                                <img id="preview_gambar" src="" alt="Preview Gambar" style="max-width: 200px; max-height: 200px; display: none;">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Tambah Rak</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Cari kode stok barang",
            allowClear: true,
            width: '100%'
        });

        $('#kode_stok_barang').change(function() {
        var kodeStokBarang = $(this).val();
        if (kodeStokBarang) {
            $.ajax({
                url: 'get_stok_data.php',
                type: 'POST',
                data: {kode_stok_barang: kodeStokBarang},
                dataType: 'json',
                success: function(data) {
                    $('#nama').val(data.nama);
                    $('#jenis').val(data.jenis);
                    $('#warna').val(data.warna);
                    $('#jumlah').val(data.jumlah);
                    $('#panjang').val(data.panjang);
                    $('#roll').val(data.roll);
                    $('#deskripsi_barang').val(data.deskripsi_barang);
                    
                    // Handle the image
                    if (data.gambar_barang) {
                        let fullImagePath = '/app/uploads/' + data.gambar_barang; // Sesuaikan path ini
                        $('#preview_gambar').attr('src', fullImagePath).show();
                        $('<input>').attr({
                            type: 'hidden',
                            id: 'gambar_barang_hidden',
                            name: 'gambar_barang_hidden',
                            value: data.gambar_barang
                        }).appendTo('form');
                    } else {
                        $('#preview_gambar').attr('src', '').hide();
                        $('#gambar_barang_hidden').remove();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Ajax error: " + status + "\nError: " + error);
                    alert("Terjadi kesalahan saat mengambil data stok. Silakan coba lagi.");
                }
            });
        } else {
            // Clear fields if no code is selected
            $('#nama, #jenis, #warna, #jumlah, #panjang, #roll, #deskripsi_barang').val('');
            $('#preview_gambar').attr('src', '').hide();
            $('#gambar_barang_hidden').remove();
        }
    });

        $('#lokasi_penyimpanan').change(function() {
            var lokasi = $(this).val();
            var nomerSelect = $('#nomer_penyimpanan');
            nomerSelect.empty();
            nomerSelect.append('<option value="">Pilih Nomor</option>');
            
            if (lokasi) {
                var rak = lokasi.split(' ')[1]; // Get the letter of the rack
                for (var i = 1; i <= 3; i++) {
                    nomerSelect.append('<option value="' + rak + '-0' + i + '">' + rak + '-0' + i + '</option>');
                }
            }
        });
    });
</script>

</body>
</html>