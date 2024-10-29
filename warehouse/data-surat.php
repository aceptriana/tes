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
                    <h5 class="m-b-10">Data Surat Jalan</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Data Surat Jalan</li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <!-- [ Main Content ] start -->
        <div class="main-content">
        <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'success') {
                    echo "<div class='alert alert-success'>Surat Jalan successfully added.</div>";
                } elseif ($_GET['status'] == 'updated') {
                    echo "<div class='alert alert-success'>Surat Jalan successfully updated.</div>";
                } elseif ($_GET['status'] == 'deleted') {
                    echo "<div class='alert alert-success'>Surat Jalan successfully deleted.</div>";
                } elseif ($_GET['status'] == 'error') {
                    echo "<div class='alert alert-danger'>An error occurred.</div>";
                }
            }
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="suratJalanList">
                                    <thead>
                                        <tr>
                                            <th>Kode Surat Jalan</th>
                                            <th>Nama</th>
                                            <th>Jenis</th>
                                            <th>Warna</th>
                                            <th>Jumlah</th>
                                            <th>Panjang</th>
                                            <th>Roll</th>
                                            <th>Dipesan Oleh</th>
                                            <th>Dikirim Oleh</th>
                                            <th>Tanggal Pengiriman</th>
                                            <th>Lokasi Pengiriman</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch surat jalan from the database
                                        $sql = "SELECT * FROM surat_jalan";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                    <td>{$row['kode_surat_jalan']}</td>
                                                    <td>{$row['nama']}</td>
                                                    <td>{$row['jenis']}</td>
                                                    <td>{$row['warna']}</td>
                                                    <td>{$row['jumlah']}</td>
                                                    <td>{$row['panjang']}</td>
                                                    <td>{$row['roll']}</td>
                                                    <td>{$row['dipesan_oleh']}</td>
                                                    <td>{$row['dikirim_oleh']}</td>
                                                    <td>{$row['tanggal_pengiriman']}</td>
                                                    <td>{$row['lokasi_pengiriman']}</td>
                                                    <td class='text-end'>
                                                        <div class='hstack gap-2 justify-content-end'>
                                                             <a href='view-surat.php?id={$row['kode_surat_jalan']}' class='avatar-text avatar-md' title='View'>
                                                                <i class='feather-eye'></i>
                                                            </a>
                                                            <a href='edit-surat.php?kode_surat_jalan={$row['kode_surat_jalan']}' class='avatar-text avatar-md' title='Edit'>
                                                                <i class='feather-edit'></i>
                                                            </a>
                                                            <a href='#' class='avatar-text avatar-md delete-surat' title='Delete' data-kode-surat-jalan='{$row['kode_surat_jalan']}'>
                                                                <i class='feather-trash-2'></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='12' class='text-center'>No surat jalan found.</td></tr>";
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
        <!-- [ Main Content ] end -->
    </div>
</main>

<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-surat');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const kode_surat_jalan = this.getAttribute('data-kode-surat-jalan');
            confirmDelete(kode_surat_jalan);
        });
    });
});

function confirmDelete(kode_surat_jalan) {
    if (confirm("Are you sure you want to delete this surat jalan?")) {
        fetch('delete-surat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'kode_surat_jalan=' + encodeURIComponent(kode_surat_jalan)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = 'data-surat.php?status=deleted';
            } else {
                window.location.href = 'data-surat.php?status=error';
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            window.location.href = 'data-surat.php?status=error';
        });
    }
}
</script>