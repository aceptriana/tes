<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Handle import from XLSX file
if (isset($_POST['import_coa'])) {
    require '../vendor/autoload.php';
    // Include PhpSpreadsheet

    $file = $_FILES['coa_file']['tmp_name'];

    // Load file using PhpSpreadsheet
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();
    $highestRow = $worksheet->getHighestRow();

    // Loop through rows and insert data into the database
    for ($row = 2; $row <= $highestRow; $row++) {
        $kode_akun = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
        $nama_akun = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
        $kategori = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
        $deskripsi = $worksheet->getCellByColumnAndRow(4, $row)->getValue();

        $sql = "INSERT INTO coa (kode_akun, nama_akun, kategori, deskripsi) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $kode_akun, $nama_akun, $kategori, $deskripsi);
        $stmt->execute();
    }

    header('Location: data-coa.php?status=import_success');
}

// Handle adding a new COA category
if (isset($_POST['add_coa'])) {
    $kode_akun = $_POST['kode_akun'];
    $nama_akun = $_POST['nama_akun'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];

    $sql = "INSERT INTO coa (kode_akun, nama_akun, kategori, deskripsi) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $kode_akun, $nama_akun, $kategori, $deskripsi);

    if ($stmt->execute()) {
        header('Location: data-coa.php?status=added');
    } else {
        header('Location: data-coa.php?status=error');
    }
}

// Handle search
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql = "SELECT * FROM coa WHERE kode_akun LIKE ? OR nama_akun LIKE ?";
    $search_value = "%" . $search_query . "%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $search_value, $search_value);
} else {
    $sql = "SELECT * FROM coa";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Chart of Accounts (COA)</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Data COA</li>
                </ul>
            </div>
        </div>

        <!-- Form to Add New COA Category -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="data-coa.php" method="POST">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="kode_akun" class="form-label">Kode Akun</label>
                                <input type="text" class="form-control" id="kode_akun" name="kode_akun" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="nama_akun" class="form-label">Nama Akun</label>
                                <input type="text" class="form-control" id="nama_akun" name="nama_akun" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="kategori" name="kategori" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <input type="text" class="form-control" id="deskripsi" name="deskripsi" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="add_coa">Tambah Kategori COA</button>
                    </form>
                </div>
            </div>

            <!-- Form to Import COA from XLSX -->
            <div class="card mt-3">
                <div class="card-body">
                    <form action="data-coa.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                            <label for="coa_file" class="form-label">
    Import COA (XLSX) 
    
</label>

                                <input type="file" class="form-control" id="coa_file" name="coa_file" accept=".xlsx" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success" name="import_coa">Import COA</button>
                    </form>
                </div>
            </div>

            <!-- Search COA -->
            <div class="card mt-3">
                <div class="card-body">
                    <form action="data-coa.php" method="GET">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="search" class="form-label">Cari COA</label>
                                <input type="text" class="form-control" id="search" name="search" value="<?php echo $search_query; ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info">Cari</button>
                    </form>
                </div>
            </div>

            <!-- COA Table -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Akun</th>
                                    <th>Nama Akun</th>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>{$row['kode_akun']}</td>
                                            <td>{$row['nama_akun']}</td>
                                            <td>{$row['kategori']}</td>
                                            <td>{$row['deskripsi']}</td>
                                            <td>
                                                <a href='edit-coa.php?id={$row['coa_id']}' class='btn btn-warning btn-sm'>Edit</a>
                                                <a href='delete-coa.php?id={$row['coa_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus COA ini?\");'>Hapus</a>
                                            </td>
                                          </tr>";
                                    $no++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
