<?php
include '../config/db.php';
include '../config/session.php';

// Queries untuk dashboard
$querySupplier = "SELECT COUNT(*) as total FROM pemasok";
$queryNota = "SELECT COUNT(*) as total FROM nota_penerimaan";
$queryStockGudang = "SELECT COUNT(*) as total FROM penyimpanan_barang WHERE keterangan_barang = 'barang gudang'";
$queryPO = "SELECT COUNT(*) as total FROM po_customer WHERE keterangan_barang = 'barang po'";
$queryColorDetails = "SELECT SUM(total_length_with_small_roll) as total_length FROM color_details";

// Execute queries
$totalSupplier = mysqli_fetch_assoc(mysqli_query($conn, $querySupplier))['total'];
$totalNotaPenerimaan = mysqli_fetch_assoc(mysqli_query($conn, $queryNota))['total'];
$totalStockGudang = mysqli_fetch_assoc(mysqli_query($conn, $queryStockGudang))['total'];
$totalPoCustomer = mysqli_fetch_assoc(mysqli_query($conn, $queryPO))['total'];
$totalLength = mysqli_fetch_assoc(mysqli_query($conn, $queryColorDetails))['total_length'];

include 'header.php';
include 'navbar.php';
?>

<!-- Include CSS -->
<link rel="stylesheet" href="../assets/vendors/feather/feather.css">
<link rel="stylesheet" href="../assets/vendors/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/style.css">

<!-- Custom CSS for Dashboard -->
<style>
    .dashboard-card {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .avatar-text {
        transition: all 0.3s ease;
    }

    .dashboard-card:hover .avatar-text {
        transform: rotate(360deg);
    }

    .stats-counter {
        font-size: 2rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .stats-label {
        font-size: 1rem;
        color: #7f8c8d;
        margin-top: 5px;
    }

    .total-summary {
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
        color: white;
        padding: 20px;
        border-radius: 15px;
        margin-top: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>

<main class="nxl-container">
    <div class="nxl-content">
        <!-- Header Section -->
        <div class="page-header mb-4">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h4 class="text-primary mb-2">Dashboard Warehouse</h4>
                    <p class="text-muted">Overview statistik warehouse</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-xxl-3 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-counter"><?php echo number_format($totalSupplier); ?></div>
                                <div class="stats-label">Total Supplier</div>
                            </div>
                            <div class="avatar-text avatar-lg bg-primary-subtle text-primary rounded-circle p-3">
                                <i data-feather="users" class="feather-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-counter"><?php echo number_format($totalNotaPenerimaan); ?></div>
                                <div class="stats-label">Nota Penerimaan</div>
                            </div>
                            <div class="avatar-text avatar-lg bg-success-subtle text-success rounded-circle p-3">
                            <i data-feather="file-text" class="feather-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-counter"><?php echo number_format($totalStockGudang); ?></div>
                                <div class="stats-label">Total Stock Gudang</div>
                            </div>
                            <div class="avatar-text avatar-lg bg-warning-subtle text-warning rounded-circle p-3">
                                <i data-feather="package" class="feather-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-counter"><?php echo number_format($totalPoCustomer); ?></div>
                                <div class="stats-label">Total Stock PO</div>
                            </div>
                            <div class="avatar-text avatar-lg bg-danger-subtle text-danger rounded-circle p-3">
                                <i data-feather="shopping-cart" class="feather-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Length Summary -->
        <div class="row">
            <div class="col-12">
                <div class="total-summary">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-2">Total Panjang Kain</h5>
                            <p class="mb-0">Total keseluruhan panjang kain dari semua color details</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h3 class="mb-0"><?php echo number_format($totalLength, 2); ?> meter</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Aktivitas Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <!-- Add your recent activities content here -->
                        <p>Belum ada aktivitas terbaru.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Include JavaScript -->
<script src="../assets/vendors/jquery/jquery.min.js"></script>
<script src="../assets/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendors/feather/feather.min.js"></script>
<script>
    // Initialize Feather icons
    feather.replace();

    // Add animations for stats counter
    document.addEventListener('DOMContentLoaded', function() {
        $('.stats-counter').each(function () {
            $(this).prop('Counter', 0).animate({
                Counter: $(this).text()
            }, {
                duration: 2000,
                easing: 'swing',
                step: function (now) {
                    $(this).text(Math.ceil(now));
                }
            });
        });
    });
</script>

<?php include 'footer.php'; ?>