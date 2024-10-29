<?php
include '../config/db.php'; // File untuk koneksi database
include '../config/session.php'; // Cek login
include 'header.php';
include 'navbar.php';

?>

<main class="nxl-container">
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Pesanan</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Data Pesanan</li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

<?php include 'footer.php'; ?>

