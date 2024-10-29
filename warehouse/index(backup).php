
// Fetch data for status cards
$totalSupplier = $conn->query("SELECT COUNT(*) as count FROM pemasok")->fetch_assoc()['count'];
$totalNotaPenerimaan = $conn->query("SELECT COUNT(*) as count FROM nota_penerimaan_barang")->fetch_assoc()['count'];
$totalStock = $conn->query("SELECT SUM(jumlah) as total FROM stok_barang")->fetch_assoc()['total'];
$totalBarangKeluar = $conn->query("SELECT SUM(jumlah) as total FROM surat_jalan")->fetch_assoc()['total'];
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Dashboard Warehouse</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
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
                                <h5 class="fs-4"><?php echo $totalSupplier; ?></h5>
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
                                <h5 class="fs-4"><?php echo $totalNotaPenerimaan; ?></h5>
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
                                <h5 class="fs-4"><?php echo $totalStock; ?></h5>
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
                                <h5 class="fs-4"><?php echo $totalBarangKeluar; ?></h5>
                                <span class="text-muted">Surat Jalan Dibuat </span>
                            </div>
                            <div class="avatar-text avatar-lg bg-teal text-white rounded">
                                <i class="feather-package"></i>
                            </div>
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