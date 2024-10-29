<?php
include '../config/db.php';
include '../config/session.php';
include 'header.php';
include 'navbar.php';
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Dashboard Barang Masuk</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Barang Masuk</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <!-- Status Cards -->
            <div class="row">
                <div class="col-xxl-3 col-md-6 mb-4">
                    <div class="card card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <h5 class="fs-4">3</h5>
                                <span class="text-muted">Total Supplier</span>
                            </div>
                            <div class="avatar-text avatar-lg bg-primary text-white rounded">
                                <i class="feather-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6 mb-4">
                    <div class="card card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <h5 class="fs-4">3</h5>
                                <span class="text-muted">Nota Penerimaan</span>
                            </div>
                            <div class="avatar-text avatar-lg bg-success text-white rounded">
                                <i class="feather-file-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6 mb-4">
                    <div class="card card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <h5 class="fs-4">1500</h5>
                                <span class="text-muted">Total Stock</span>
                            </div>
                            <div class="avatar-text avatar-lg bg-warning text-white rounded">
                                <i class="feather-shopping-bag"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6 mb-4">
                    <div class="card card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <h5 class="fs-4">500</h5>
                                <span class="text-muted">Barang Keluar</span>
                            </div>
                            <div class="avatar-text avatar-lg bg-teal text-white rounded">
                                <i class="feather-package"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Data Tables -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Data Pemasok</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="pemasokTable">
                                    <thead>
                                        <tr>
                                            <th>Kode Pemasok</th>
                                            <th>Nama</th>
                                            <th>Kontak</th>
                                            <th>Telepon</th>
                                            <th>Email</th>
                                            <th>Alamat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM pemasok";
                                        $result = $conn->query($sql);
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                <td>{$row['kode_pemasok']}</td>
                                                <td>{$row['nama']}</td>
                                                <td>{$row['kontak']}</td>
                                                <td>{$row['telepon']}</td>
                                                <td>{$row['email']}</td>
                                                <td>{$row['alamat']}</td>
                                            </tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Data Nota Penerimaan Barang</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="notaTable">
                                    <thead>
                                        <tr>
                                            <th>Kode Nota</th>
                                            <th>Kode Pemasok</th>
                                            <th>Jenis Barang Dikirim</th>
                                            <th>Tanggal Diterima</th>
                                            <th>Dikirim Oleh</th>
                                            <th>Diterima Oleh</th>
                                            <th>Nama Barang</th>
                                            <th>Jenis</th>
                                            <th>Warna</th>
                                            <th>Jumlah</th>
                                            <th>Panjang</th>
                                            <th>Lebar</th>
                                            <th>Deskripsi Barang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM nota_penerimaan_barang";
                                        $result = $conn->query($sql);
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                <td>{$row['kode_nota']}</td>
                                                <td>{$row['kode_pemasok']}</td>
                                                <td>{$row['jenis_barang_dikirim']}</td>
                                                <td>{$row['tanggal_diterima']}</td>
                                                <td>{$row['dikirim_oleh']}</td>
                                                <td>{$row['diterima_oleh']}</td>
                                                <td>{$row['nama_barang']}</td>
                                                <td>{$row['jenis']}</td>
                                                <td>{$row['warna']}</td>
                                                <td>{$row['jumlah']}</td>
                                                <td>{$row['panjang']}</td>
                                                <td>{$row['lebar']}</td>
                                                <td>{$row['deskripsi_barang']}</td>
                                            </tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Data Stok Barang</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="stokTable">
                                    <thead>
                                        <tr>
                                            <th>Kode Stok Barang</th>
                                            <th>Kode Nota</th>
                                            <th>Nama</th>
                                            <th>Jenis</th>
                                            <th>Warna</th>
                                            <th>Jumlah</th>
                                            <th>Panjang</th>
                                            <th>Lebar</th>
                                            <th>Deskripsi Barang</th>
                                            <th>Tanggal Masuk Gudang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM stok_barang";
                                        $result = $conn->query($sql);
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                <td>{$row['kode_stok_barang']}</td>
                                                <td>{$row['kode_nota']}</td>
                                                <td>{$row['nama']}</td>
                                                <td>{$row['jenis']}</td>
                                                <td>{$row['warna']}</td>
                                                <td>{$row['jumlah']}</td>
                                                <td>{$row['panjang']}</td>
                                                <td>{$row['lebar']}</td>
                                                <td>{$row['deskripsi_barang']}</td>
                                                <td>{$row['tanggal_masuk_gudang']}</td>
                                            </tr>";
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

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
$(document).ready(function() {
    // Initialize DataTables
    $('#pemasokTable, #notaTable, #stokTable').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });

    // Barang Masuk/Keluar Chart
    var optionsBarangMasukKeluar = {
        series: [{
            name: 'Barang Masuk',
            data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
        }, {
            name: 'Barang Keluar',
            data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
        }],
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
        },
        yaxis: {
            title: {
                text: 'Jumlah Barang'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " barang"
                }
            }
        }
    };

    var chartBarangMasukKeluar = new ApexCharts(document.querySelector("#barangMasukKeluar"), optionsBarangMasukKeluar);
    chartBarangMasukKeluar.render();

    // Stok Barang Per Jenis Chart
    var optionsStokBarangPerJenis = {
        series: [44, 55, 13, 43, 22],
        chart: {
            width: 380,
            type: 'pie',
        },
        labels: ['Jenis A', 'Jenis B', 'Jenis C', 'Jenis D', 'Jenis E'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var chartStokBarangPerJenis = new ApexCharts(document.querySelector("#stokBarangPerJenis"), optionsStokBarangPerJenis);
    chartStokBarangPerJenis.render();
});
</script>