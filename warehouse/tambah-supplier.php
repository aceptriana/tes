<?php 
//tambah-supplier
include '../config/session.php'; // Check login session
include '../config/db.php'; // Include your database connection file
include 'header.php'; 
include 'navbar.php'; 

// Fungsi untuk menghasilkan kode supplier otomatis
function generateKodePemasok($conn) {
    $sql = "SELECT MAX(CAST(SUBSTRING(kode_pemasok, 2) AS UNSIGNED)) as max_kode FROM pemasok";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $max_kode = $row['max_kode'];

    if ($max_kode === null) {
        return 'P001';
    } else {
        $next_number = $max_kode + 1;
        return 'P' . str_pad($next_number, 3, '0', STR_PAD_LEFT);
    }
}

// Dapatkan kode supplier otomatis
$kode_pemasok_auto = generateKodePemasok($conn);
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Supplier</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Tambah Supplier</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="process_tambah_supplier.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode_pemasok" class="form-label">Kode Supplier</label>
                                <input type="text" class="form-control" id="kode_pemasok" name="kode_pemasok" value="<?php echo $kode_pemasok_auto; ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama Supplier</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kontak" class="form-label">Kontak Person</label>
                                <input type="text" class="form-control" id="kontak" name="kontak" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telepon" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="telepon" name="telepon" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="wechat" class="form-label">We Chat (Opsional)</label>
                                <input type="wechat" class="form-control" id="kontak" name="wechat" >
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email (Opsional)</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah Supplier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>