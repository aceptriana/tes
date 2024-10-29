<?php 
include '../config/session.php';
include '../config/db.php';
include 'header.php'; 
include 'navbar.php'; 

// Function to generate automatic kode prapesan
function generateKodePrepesan($conn) {
    $sql = "SELECT MAX(CAST(SUBSTRING(kode_prapesan, 3) AS UNSIGNED)) as max_kode FROM prapesan";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $max_kode = $row['max_kode'];

    if ($max_kode === null) {
        return 'PP001';
    } else {
        $next_number = $max_kode + 1;
        return 'PP' . str_pad($next_number, 3, '0', STR_PAD_LEFT);
    }
}

$kode_prapesan_auto = generateKodePrepesan($conn);

// Fetch penyimpanan_barang data for dropdown
$penyimpanan_query = "SELECT kode_penyimpanan, nama_barang FROM penyimpanan_barang";  // nama_barang for correct data
$penyimpanan_result = $conn->query($penyimpanan_query);

// Fetch pemasok data for dropdown
$pemasok_query = "SELECT kode_pemasok, nama FROM pemasok";
$pemasok_result = $conn->query($pemasok_query);

// Hapus jika tidak ada fungsi generateKodePemasok
// $kode_pemasok_auto = generateKodePemasok($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pre Order</title>
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
                    <h5 class="m-b-10">Tambah Pre Order</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Tambah Pre Order</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="process_tambah_po.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode_prapesan" class="form-label">Kode Pre Order</label>
                                <input type="text" class="form-control" id="kode_prapesan" name="kode_prapesan" value="<?php echo $kode_prapesan_auto; ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kode_pemasok" class="form-label">Kode Pemasok</label>
                                <select class="form-control select2" id="kode_pemasok" name="kode_pemasok" required>
                                    <option value="">Pilih Pemasok</option>
                                    <?php while($row = $pemasok_result->fetch_assoc()) { ?>
                                        <option value="<?php echo $row['kode_pemasok']; ?>"><?php echo $row['kode_pemasok'] . ' - ' . $row['nama']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode_penyimpanan" class="form-label">Kode Penyimpanan</label>
                                <select class="form-control select2" id="kode_penyimpanan" name="kode_penyimpanan" required>
                                    <option value="">Pilih Kode Penyimpanan</option>
                                    <?php while($row = $penyimpanan_result->fetch_assoc()) { ?>
                                        <option value="<?php echo $row['kode_penyimpanan']; ?>"><?php echo $row['kode_penyimpanan'] . ' - ' . $row['nama_barang']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_pesan" class="form-label">Tanggal Pesan</label>
                                <input type="date" class="form-control" id="tanggal_pesan" name="tanggal_pesan" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_dikirim" class="form-label">Tanggal Dikirim</label>
                                <input type="date" class="form-control" id="tanggal_dikirim" name="tanggal_dikirim">
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
                                <input type="text" class="form-control" id="warna" name="warna" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="panjang" class="form-label">Panjang</label>
                                <input type="number" class="form-control" id="panjang" name="panjang" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="roll" class="form-label">Roll</label>
                                <input type="number" class="form-control" id="roll" name="roll" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="deskripsi_barang" class="form-label">Deskripsi Barang</label>
                                <input type="text" class="form-control" id="deskripsi_barang" name="deskripsi_barang">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="gambar_barang" class="form-label">Gambar Barang</label>
                                <input type="file" class="form-control-file" id="gambar_barang" name="gambar_barang" accept="image/*">
                                <img id="preview_gambar" src="" alt="Preview Gambar" style="max-width: 200px; max-height: 200px; display: none;">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Tambah Pre Order</button>
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
            placeholder: "Cari kode penyimpanan",
            allowClear: true,
            width: '100%'
        });

        // Event saat kode penyimpanan berubah
        $('#kode_penyimpanan').change(function() {
            var kodePenyimpanan = $(this).val();
            if (kodePenyimpanan) {
                $.ajax({
                    url: 'get_rak_data.php',
                    type: 'POST',
                    data: { kode_penyimpanan: kodePenyimpanan },
                    dataType: 'json',
                    success: function(data) {
                        // Mengisi data barang ke dalam form
                        $('#nama').val(data.nama_barang);
                        $('#jenis').val(data.jenis);
                        $('#warna').val(data.warna);
                        $('#jumlah').val(data.jumlah);
                        $('#panjang').val(data.panjang);
                        $('#roll').val(data.roll);
                        $('#deskripsi_barang').val(data.keterangan_barang);
                        
                        // Menangani gambar
                        if (data.gambar_barang) {
                            let fullImagePath = '../uploads/' + data.gambar_barang; // Sesuaikan path ini
                            $('#preview_gambar').attr('src', fullImagePath).show();
                            
                            // Menambahkan input hidden untuk gambar
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
                // Kosongkan field jika tidak ada kode yang dipilih
                $('#nama, #jenis, #warna, #jumlah, #panjang, #roll, #deskripsi_barang').val('');
                $('#preview_gambar').attr('src', '').hide();
                $('#gambar_barang_hidden').remove();
            }
        });

        // Preview gambar yang diupload
        $("#gambar_barang").change(function() {
            readURL(this);
        });

        // Fungsi untuk membaca URL gambar
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    $('#preview_gambar').attr('src', e.target.result).show();
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    });
</script>

</body>
</html>
