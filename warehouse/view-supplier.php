<?php include '../config/db.php'; // File untuk koneksi database 
include '../config/session.php'; // Cek login 
include 'header.php'; 
include 'navbar.php'; 

// Fungsi untuk membersihkan input
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>

<main class="nxl-container">
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Supplier Details</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Supplier Details</li>
                </ul>

                <div class="d-md-none d-flex align-items-center">
                    <a href="javascript:void(0)" class="page-header-right-open-toggle">
                        <i class="feather-align-right fs-20"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <!-- [ Main Content ] start -->
        <div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <?php
                    if (isset($_GET['id']) && !empty($_GET['id'])) {
                        $kode_pemasok = intval(sanitize_input($_GET['id']));

                        $sql = "SELECT * FROM pemasok WHERE kode_pemasok = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $kode_pemasok);
                        
                        if ($stmt->execute()) {
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $pemasok = $result->fetch_assoc();
                                // ... (kode untuk menampilkan detail pemasok tetap sama)
                            } else {
                                echo "<p class='text-center alert alert-warning'>Supplier not found.</p>";
                            }
                        } else {
                            echo "<p class='text-center alert alert-danger'>Error executing query: " . $stmt->error . "</p>";
                        }
                        $stmt->close();
                    } else {
                        echo "<p class='text-center alert alert-warning'>No supplier ID provided.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
        <!-- [ Main Content ] end -->
    </div>
</main>

<?php include 'footer.php'; ?>
