<?php 
//tambah-barang.php
include '../config/session.php'; // Check login session
include '../config/db.php'; // Include your database connection file
include 'header.php'; 
include 'navbar.php'; 

// Function to generate automatic kode stok
function generateKodeStok($conn) {
    $sql = "SELECT MAX(CAST(SUBSTRING(kode_stok_barang, 3) AS UNSIGNED)) as max_kode FROM stok_barang";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $max_kode = $row['max_kode'];

    if ($max_kode === null) {
        return 'SB001';
    } else {
        $next_number = $max_kode + 1;
        return 'SB' . str_pad($next_number, 3, '0', STR_PAD_LEFT);
    }
}

$kode_stok_auto = generateKodeStok($conn);

// Fetch nota data for dropdown
$nota_query = "SELECT kode_nota, jenis_barang_dikirim FROM nota_penerimaan_barang";
$nota_result = $conn->query($nota_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Stok Barang</title>
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
                    <h5 class="m-b-10">Tambah Stok Barang</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Tambah Barang</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                <form action="process_tambah_barang.php" method="POST" enctype="multipart/form-data">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode_stok_barang" class="form-label">Kode Stok Barang</label>
                                <input type="text" class="form-control" id="kode_stok_barang" name="kode_stok_barang" value="<?php echo $kode_stok_auto; ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kode_nota" class="form-label">Kode Nota</label>
                                <select class="form-control select2" id="kode_nota" name="kode_nota" required>
                                    <option value="">Pilih Nota</option>
                                    <?php while($row = $nota_result->fetch_assoc()) { ?>
                                        <option value="<?php echo $row['kode_nota']; ?>"><?php echo $row['kode_nota'] . ' - ' . $row['jenis_barang_dikirim']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <input type="text" class="form-control" id="jenis" name="jenis" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="warna" class="form-label">Warna</label>
                                <input type="text" class="form-control" id="warna" name="warna">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="panjang" class="form-label">Panjang</label>
                                <input type="number" class="form-control" id="panjang" name="panjang" >
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="roll" class="form-label">Roll</label>
                                <input type="number" class="form-control" id="roll" name="roll" >
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="tanggal_masuk_gudang" class="form-label">Tanggal Masuk Gudang</label>
                                <input type="date" class="form-control" id="tanggal_masuk_gudang" name="tanggal_masuk_gudang" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="deskripsi_barang" class="form-label">Deskripsi Barang</label>
                                <textarea class="form-control" id="deskripsi_barang" name="deskripsi_barang" rows="3"></textarea>
                         <div class="col-md-6 mb-3">
                                <label for="gambar_barang" class="form-label">Gambar Barang</label>
                                <input type="file" class="form-control" id="gambar_barang" name="gambar_barang" accept="image/png, image/jpeg, image/gif">
                                <input type="hidden" id="gambar_barang_hidden" name="gambar_barang_hidden">

                                <img id="preview_gambar" src="" alt="Preview" style="max-width: 200px; margin-top: 10px; display: none;">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah Barang</button>
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
        // Inisialisasi Select2
        $('.select2').select2({
            placeholder: "Cari kode atau jenis nota",
            allowClear: true,
            width: '100%'
        });

        // Event ketika kode nota berubah
        $('#kode_nota').change(function() {
            var kodeNota = $(this).val();
            if (kodeNota) {
                $.ajax({
                    url: 'get_nota_data.php',
                    type: 'POST',
                    data: {kode_nota: kodeNota},
                    dataType: 'json',
                    success: function(data) {
                        if (confirm("Apakah Anda ingin mengisi data otomatis berdasarkan nota yang dipilih?")) {
                            $('#nama').val(data.nama);
                            $('#jenis').val(data.jenis);
                            $('#warna').val(data.warna);
                            $('#jumlah').val(data.jumlah);
                            $('#panjang').val(data.panjang);
                            $('#roll').val(data.roll);
                            $('#deskripsi_barang').val(data.deskripsi_barang);
                            
                            // Handle the image
                            if (data.gambar_barang) {
                            let fullImagePath = '/app/uploads/' + data.gambar_barang; // Sesuaikan dengan path di server Anda
                            $('#preview_gambar').attr('src', fullImagePath).show();
                            $('#gambar_barang_hidden').val(data.gambar_barang); // Menyimpan nama gambar di hidden input
                        } else {
                            $('#preview_gambar').attr('src', '').hide();
                            $('#gambar_barang_hidden').val(''); // Kosongkan jika tidak ada gambar
                        }

                        }
                    },
                    error: function() {
                        alert("Terjadi kesalahan saat mengambil data nota.");
                    }
                });
            }
        });

        // Preview gambar yang diupload
        $('#gambar_barang').change(function() {
        const file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function(event) {
                $('#preview_gambar').attr('src', event.target.result).show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#preview_gambar').attr('src', '').hide();
        }
    });
    });
</script>
