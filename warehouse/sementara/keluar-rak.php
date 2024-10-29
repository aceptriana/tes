<?php
include '../config/db.php'; // Koneksi ke database
include '../config/session.php'; // Cek login
include 'header.php';
include 'navbar.php';
?>

<main class="nxl-container">
    <div class="nxl-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Keluar Barang dari Rak</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Data Keluar Barang</li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="leadList">
                                    <thead>
                                        <tr>
                                            <th>Kode Rak</th>
                                            <th>Nama Barang</th>
                                            <th>Jumlah Tersedia</th>
                                            <th>Lokasi Rak</th>
                                            <th>Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch barang dari database
                                        $sql = "SELECT kode_penyimpanan, nama_barang, jumlah, lokasi_penyimpanan FROM penyimpanan_barang";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                    <td>{$row['kode_penyimpanan']}</td>
                                                    <td>{$row['nama_barang']}</td>
                                                    <td>{$row['jumlah']}</td>
                                                    <td>{$row['lokasi_penyimpanan']}</td>
                                                    <td>
                                                        <a href='proses-keluar-rak.php?kode_penyimpanan={$row['kode_penyimpanan']}' class='btn btn-warning'>Keluar Barang</a>
                                                    </td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='5' class='text-center'>Tidak ada barang di rak saat ini.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
