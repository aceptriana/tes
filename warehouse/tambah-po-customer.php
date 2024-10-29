<?php 
//tambah-po-customer.php
include '../config/session.php';
include '../config/db.php';
include 'header.php'; 
include 'navbar.php'; 

// Function to generate automatic kode preorder customer
function generateKodePreorderCustomer($conn) {
    $sql = "SELECT MAX(CAST(SUBSTRING(kode_preorder_customer, 3) AS UNSIGNED)) as max_kode FROM preorder_customer";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $max_kode = $row['max_kode'];

    if ($max_kode === null) {
        return 'PC001';
    } else {
        $next_number = $max_kode + 1;
        return 'PC' . str_pad($next_number, 3, '0', STR_PAD_LEFT);
    }
}


$kode_preorder_customer_auto = generateKodePreorderCustomer($conn);

// Fetch stok_barang data for dropdown
$stok_query = "SELECT kode_stok_barang, nama FROM stok_barang";
$stok_result = $conn->query($stok_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah PO Customer</title>
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
                    <h5 class="m-b-10">Tambah PO Customer</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Tambah PO Customer</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="process_tambah_po_customer.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode_preorder_customer" class="form-label">Kode Preorder Customer</label>
                                <input type="text" class="form-control" id="kode_preorder_customer" name="kode_preorder_customer" value="<?php echo $kode_preorder_customer_auto; ?>" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode_stok_barang" class="form-label">Kode Stok Barang</label>
                                <select class="form-control select2" id="kode_stok_barang" name="kode_stok_barang" required>
                                    <option value="">Pilih Kode Stok Barang</option>
                                    <?php while($row = $stok_result->fetch_assoc()) { ?>
                                        <option value="<?php echo $row['kode_stok_barang']; ?>"><?php echo $row['kode_stok_barang'] . ' - ' . $row['nama']; ?></option>
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
                                <input type="number" class="form-control" id="panjang" name="panjang"  readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="roll" class="form-label">Roll</label>
                                <input type="number" class="form-control" id="roll" name="roll"  readonly>
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

                        <button type="submit" class="btn btn-primary">Tambah PO Customer</button>
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
            placeholder: "Cari kode stok barang",
            allowClear: true,
            width: '100%'
        });

        // Event saat kode stok barang berubah
        $('#kode_stok_barang').change(function() {
            var kodeStokBarang = $(this).val();
            if (kodeStokBarang) {
                $.ajax({
                    url: 'get_stok_data.php',
                    type: 'POST',
                    data: { kode_stok_barang: kodeStokBarang },
                    dataType: 'json',
                    success: function(data) {
                        // Mengisi data barang ke dalam form
                        $('#nama').val(data.nama);
                        $('#jenis').val(data.jenis);
                        $('#warna').val(data.warna);
                        $('#jumlah').val(data.jumlah);
                        $('#panjang').val(data.panjang);
                        $('#roll').val(data.roll);
                        $('#deskripsi_barang').val(data.deskripsi_barang);
                        
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
