<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Check login session
include 'header.php';
include 'navbar.php';
require '../vendor/autoload.php'; // Include PHPSpreadsheet library

use PhpOffice\PhpSpreadsheet\IOFactory;

// Handle file upload for importing transactions
if (isset($_FILES['file']['name'])) {
    $allowedFileType = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if (in_array($_FILES['file']['type'], $allowedFileType)) {
        $targetPath = 'uploads/' . $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

        $spreadsheet = IOFactory::load($targetPath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        foreach ($sheetData as $key => $row) {
            if ($key > 0) { // Skip header row
                $tanggal = $row[0];
                $jenis = $row[1];
                $kode_coa = $row[2];
                $nominal = $row[3];
                $keterangan = $row[4];
                $nama_rekening = $row[5];

                $sql = "INSERT INTO transactions (tanggal, jenis, kode_coa, nominal, keterangan, nama_saldo) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssss", $tanggal, $jenis, $kode_coa, $nominal, $keterangan, $nama_rekening);
                $stmt->execute();

                // Update saldo based on jenis transaksi
                if ($jenis === 'Pemasukan') {
                    $update_sql = "UPDATE rekening_bank SET saldo = saldo + ? WHERE nama_rekening = ?";
                } else if ($jenis === 'Pengeluaran') {
                    $update_sql = "UPDATE rekening_bank SET saldo = saldo - ? WHERE nama_rekening = ?";
                }

                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ds", $nominal, $nama_rekening);
                $update_stmt->execute();
            }
        }
        echo "<div class='alert alert-success'>Transaksi berhasil diimport.</div>";
    } else {
        echo "<div class='alert alert-danger'>File harus dalam format Excel.</div>";
    }
}

// Handle delete request for transactions
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM transactions WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Transaksi berhasil dihapus.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menghapus transaksi.</div>";
    }
}

// Show alerts based on transaction status
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        echo "<div class='alert alert-success'>Transaksi berhasil ditambahkan.</div>";
    } elseif ($_GET['status'] == 'error') {
        echo "<div class='alert alert-danger'>Gagal menambahkan transaksi.</div>";
    } elseif ($_GET['status'] == 'insufficient_balance') {
        echo "<div class='alert alert-warning'>Saldo tidak mencukupi untuk transaksi pengeluaran.</div>";
    }
}

// Handle alerts based on transaction status
$statusMessage = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $statusMessage = "<div class='alert alert-success'>Transaksi berhasil ditambahkan.</div>";
    } elseif ($_GET['status'] == 'error') {
        $statusMessage = "<div class='alert alert-danger'>Gagal menambahkan transaksi.</div>";
    } elseif ($_GET['status'] == 'insufficient_balance') {
        $statusMessage = "<div class='alert alert-warning'>Saldo tidak mencukupi untuk transaksi pengeluaran.</div>";
    }
}
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Data Transaksi</h5>
                </div>
            </div>
        </div>

        <!-- Form untuk menambah transaksi -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="proses-data-transaksi.php" method="POST">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jenis" class="form-label">Jenis Transaksi</label>
                                <select class="form-control" id="jenis" name="jenis" required>
                                    <option value="Pemasukan">Pemasukan</option>
                                    <option value="Pengeluaran">Pengeluaran</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="kode_coa" class="form-label">Kode COA</label>
                                <select class="form-control" id="kode_coa" name="kode_coa" required>
                                    <?php 
                                    // Fetch COA data from COA table
                                    $coa_sql = "SELECT kode_akun, nama_akun FROM coa";
                                    $coa_result = $conn->query($coa_sql);
                                    while ($row = $coa_result->fetch_assoc()) {
                                        echo "<option value='{$row['kode_akun']}'>{$row['kode_akun']} - {$row['nama_akun']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
    <label for="nominal" class="form-label">Nominal</label>
    <input type="number" class="form-control" id="nominal" name="nominal" required placeholder="contoh : 1000000">
</div>

                            <div class="col-md-4 mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="nama_rekening" class="form-label">Nama Rekening</label>
                                <select class="form-control" id="nama_rekening" name="nama_rekening" required>
                                    <?php 
                                    // Fetch rekening data from rekening_bank table
                                    $rekening_sql = "SELECT nama_rekening FROM rekening_bank";
                                    $rekening_result = $conn->query($rekening_sql);
                                    while ($row = $rekening_result->fetch_assoc()) {
                                        echo "<option value='{$row['nama_rekening']}'>{$row['nama_rekening']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Tambah Transaksi</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tampilkan pesan status di bawah form -->
        <div class="main-content">
            <?php
            // Display the status message if set
            if (!empty($statusMessage)) {
                echo $statusMessage;
            }
            ?>
        </div>

        <!-- Tabel untuk menampilkan data transaksi -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Kode COA</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                                <th>Nama Rekening</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
// Fetch all transactions
$transaksi_sql = "SELECT * FROM transactions ORDER BY tanggal DESC";
$transaksi_result = $conn->query($transaksi_sql);
while ($row = $transaksi_result->fetch_assoc()) {
    // Format nominal to include "Rp" and comma as thousand separator
    $formatted_nominal = "Rp " . number_format($row['nominal'], 2, ',', '.');

    echo "<tr>
            <td>{$row['tanggal']}</td>
            <td>{$row['jenis']}</td>
            <td>{$row['kode_coa']}</td>
            <td>{$formatted_nominal}</td>
            <td>{$row['keterangan']}</td>
            <td>{$row['nama_saldo']}</td>
            <td>
                <a href='data-transaksi.php?delete_id={$row['id']}' class='btn btn-danger btn-sm'>Hapus</a>
            </td>
        </tr>";
}
?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
